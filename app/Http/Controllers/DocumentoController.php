<?php

namespace App\Http\Controllers;

use App\Models\Documento;
use App\Models\Processo;
use App\Models\UsuarioAdministrador;
use App\Services\DocumentoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use ZipArchive;

class DocumentoController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        try {
            $data = $request->validate([
                'arquivo' => ['required', 'file', 'max:'.config('sigep.upload_max_kb')],
                'etapa_id' => ['required', 'integer', 'exists:etapa,id'],
                'tipo' => ['required', 'string', 'max:255'],
            ]);

            $documento = app(DocumentoService::class)->salvar(
                $request->file('arquivo'),
                $data['etapa_id'],
                $data['tipo'],
                auth()->user()->siape,
            );

            return response()->json([
                'ok' => true,
                'documento' => [
                    'id' => $documento->id,
                    'titulo' => $documento->titulo,
                    'descricao' => $documento->descricao,
                    'tipo_display' => $documento->tipo_display,
                    'data_upload' => $documento->data_upload->format('d/m/Y H:i'),
                    'url' => asset($documento->caminho),
                    'preview_url' => route('documentos.visualizar', $documento),
                    'download_url' => route('documentos.download', $documento),
                    'pode_excluir' => $this->podeExcluir($documento),
                ],
            ]);
        } catch (ValidationException $e) {
            return response()->json(['ok' => false, 'errors' => $e->errors()], 422);
        } catch (\Throwable $e) {
            return response()->json(['ok' => false, 'erro' => 'Erro ao salvar arquivo: '.$e->getMessage()], 500);
        }
    }

    public function download(Documento $documento)
    {
        $caminhoCompleto = public_path($documento->caminho);

        if (! file_exists($caminhoCompleto)) {
            abort(404, 'Arquivo nao encontrado.');
        }

        return response()->download($caminhoCompleto, $documento->nome_original);
    }

    public function visualizar(Documento $documento)
    {
        $caminhoCompleto = $this->caminhoSeguro($documento);
        $extensao = strtolower(pathinfo($documento->nome_original, PATHINFO_EXTENSION));

        if ($extensao === 'docx') {
            return view('documentos.visualizar-docx', [
                'documento' => $documento,
                'blocos' => $this->lerDocx($caminhoCompleto),
            ]);
        }

        $mime = (new \finfo(FILEINFO_MIME_TYPE))->file($caminhoCompleto);
        $tiposVisualizaveis = [
            'application/pdf',
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/webp',
            'text/plain',
        ];

        abort_unless(in_array($mime, $tiposVisualizaveis, true), 415, 'A visualizacao no navegador nao esta disponivel para este formato. Use o botao de download.');

        $response = response()->file($caminhoCompleto, [
            'Content-Type' => $mime,
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, no-store',
        ]);
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_INLINE,
            $documento->nome_original,
            Str::ascii($documento->nome_original),
        );

        return $response;
    }

    private function caminhoSeguro(Documento $documento): string
    {
        $diretorioUploads = realpath(public_path('uploads'));
        $caminhoCompleto = realpath(public_path($documento->caminho));

        abort_if(
            ! $diretorioUploads
                || ! $caminhoCompleto
                || ! is_file($caminhoCompleto)
                || ! str_starts_with($caminhoCompleto, $diretorioUploads.DIRECTORY_SEPARATOR),
            404,
            'Arquivo nao encontrado.',
        );

        return $caminhoCompleto;
    }

    /**
     * Retorna os paragrafos e tabelas do corpo principal de um arquivo DOCX.
     * A leitura usa apenas o XML local do pacote e nao carrega entidades externas.
     *
     * @return array<int, array{tipo:string, texto?:string, linhas?:array<int, array<int, string>>}>
     */
    private function lerDocx(string $caminho): array
    {
        $zip = new ZipArchive;
        abort_if($zip->open($caminho) !== true, 422, 'Nao foi possivel abrir este documento DOCX.');

        $xml = $zip->getFromName('word/document.xml');
        $zip->close();

        abort_if($xml === false, 422, 'O documento DOCX nao possui conteudo legivel.');

        $dom = new \DOMDocument;
        $carregado = @$dom->loadXML($xml, LIBXML_NONET | LIBXML_NOBLANKS);
        abort_unless($carregado, 422, 'O conteudo deste documento DOCX e invalido.');

        $xpath = new \DOMXPath($dom);
        $xpath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');
        $blocos = [];

        foreach ($xpath->query('/w:document/w:body/*') as $elemento) {
            if ($elemento->localName === 'p') {
                $texto = $this->textoDocx($xpath, $elemento);
                if ($texto !== '') {
                    $blocos[] = ['tipo' => 'paragrafo', 'texto' => $texto];
                }

                continue;
            }

            if ($elemento->localName === 'tbl') {
                $linhas = [];
                foreach ($xpath->query('./w:tr', $elemento) as $linha) {
                    $celulas = [];
                    foreach ($xpath->query('./w:tc', $linha) as $celula) {
                        $paragrafos = [];
                        foreach ($xpath->query('./w:p', $celula) as $paragrafo) {
                            $paragrafos[] = $this->textoDocx($xpath, $paragrafo);
                        }
                        $celulas[] = implode("\n", array_filter($paragrafos, fn ($texto) => $texto !== ''));
                    }
                    $linhas[] = $celulas;
                }
                $blocos[] = ['tipo' => 'tabela', 'linhas' => $linhas];
            }
        }

        return $blocos;
    }

    private function textoDocx(\DOMXPath $xpath, \DOMNode $elemento): string
    {
        $texto = '';
        foreach ($xpath->query('.//w:t | .//w:tab | .//w:br | .//w:cr', $elemento) as $parte) {
            $texto .= match ($parte->localName) {
                'tab' => "\t",
                'br', 'cr' => "\n",
                default => $parte->textContent,
            };
        }

        return trim($texto);
    }

    public function downloadTodos(Processo $processo): RedirectResponse|BinaryFileResponse
    {
        $documentos = Documento::query()
            ->whereHas('etapa', fn ($query) => $query->where('numero_sei_processo', $processo->numero_sei))
            ->where('tipo', '!=', Documento::TIPO_PDF_SEI)
            ->with('etapa')
            ->orderBy('data_upload')
            ->get();

        $caminhoZip = tempnam(sys_get_temp_dir(), 'sigep-docs-');

        if ($caminhoZip === false) {
            return back()->with('error', 'Não foi possível gerar o arquivo compactado.');
        }

        $zip = new ZipArchive;

        if ($zip->open($caminhoZip, ZipArchive::OVERWRITE) !== true) {
            if (is_file($caminhoZip)) {
                unlink($caminhoZip);
            }

            return back()->with('error', 'Não foi possível gerar o arquivo compactado.');
        }

        $contadores = [];
        $adicionados = 0;

        foreach ($documentos as $documento) {
            $caminho = public_path($documento->caminho);

            if (! is_file($caminho)) {
                continue;
            }

            $zip->addFile($caminho, $this->nomeArquivoZip($documento, $contadores));
            $adicionados++;
        }

        $zip->close();

        if ($adicionados === 0) {
            if (is_file($caminhoZip)) {
                unlink($caminhoZip);
            }

            return back()->with('error', 'Não há documentos disponíveis para download.');
        }

        return response()
            ->download($caminhoZip, 'sigep-documentos-'.$processo->numero_sei.'.zip')
            ->deleteFileAfterSend(true);
    }

    public function destroy(Documento $documento): JsonResponse
    {
        if (! $this->podeExcluir($documento)) {
            return response()->json(['ok' => false, 'erro' => 'Sem permissao para excluir este documento.'], 403);
        }

        $caminhoCompleto = public_path($documento->caminho);
        if (file_exists($caminhoCompleto)) {
            unlink($caminhoCompleto);
        }

        $documento->delete();

        return response()->json(['ok' => true]);
    }

    private function nomeArquivoZip(Documento $documento, array &$contadores): string
    {
        $extensao = Str::slug((string) $documento->descricao, '');
        $base = Str::slug(pathinfo($documento->nome_original, PATHINFO_FILENAME)) ?: 'documento';
        $prefixo = 'etapa_'.($documento->etapa?->ordem ?? 0).'_'.Str::slug($documento->tipo, '_');
        $chave = $prefixo.'_'.$base.($extensao !== '' ? '.'.$extensao : '');

        $contadores[$chave] = ($contadores[$chave] ?? 0) + 1;

        if ($contadores[$chave] === 1) {
            return $chave;
        }

        return $prefixo.'_'.$base.'_'.$contadores[$chave].($extensao !== '' ? '.'.$extensao : '');
    }

    private function podeExcluir(Documento $documento): bool
    {
        if (! auth()->check()) {
            return false;
        }

        $user = auth()->user();

        if ($user instanceof UsuarioAdministrador) {
            return true;
        }

        return $user->siape === $documento->etapa->processo->id_relator;
    }
}

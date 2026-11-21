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

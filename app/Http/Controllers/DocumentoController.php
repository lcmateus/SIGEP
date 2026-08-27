<?php

namespace App\Http\Controllers;

use App\Models\Documento;
use App\Models\Etapa;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DocumentoController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        try {
            $data = $request->validate([
                'arquivo' => ['required', 'file', 'max:10240'],
                'etapa_id' => ['required', 'integer', 'exists:etapa,id'],
                'tipo' => ['required', 'string', 'max:255'],
            ]);

            $arquivo = $request->file('arquivo');

            if (!$arquivo || !$arquivo->isValid()) {
                return response()->json(['ok' => false, 'erro' => 'Arquivo invalido.'], 422);
            }

            $nomeOriginal = $arquivo->getClientOriginalName();
            $titulo = pathinfo($nomeOriginal, PATHINFO_FILENAME);
            $extensao = pathinfo($nomeOriginal, PATHINFO_EXTENSION);
            $nomeSalvo = Str::uuid() . '.' . $extensao;

            $diretorio = public_path('uploads');
            if (!is_dir($diretorio)) {
                mkdir($diretorio, 0755, true);
            }

            $arquivo->move($diretorio, $nomeSalvo);
            $caminho = 'uploads/' . $nomeSalvo;

            $documento = Documento::query()->create([
                'titulo' => $titulo,
                'descricao' => $extensao,
                'caminho' => $caminho,
                'upload_feito_por' => auth()->user()->siape,
                'etapa_id' => $data['etapa_id'],
                'tipo' => $data['tipo'],
                'data_upload' => now(),
            ]);

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
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['ok' => false, 'errors' => $e->errors()], 422);
        } catch (\Throwable $e) {
            return response()->json(['ok' => false, 'erro' => 'Erro ao salvar arquivo: ' . $e->getMessage()], 500);
        }
    }

    public function download(Documento $documento)
    {
        $caminhoCompleto = public_path($documento->caminho);

        if (!file_exists($caminhoCompleto)) {
            abort(404, 'Arquivo nao encontrado.');
        }

        return response()->download($caminhoCompleto, $documento->nome_original);
    }

    public function destroy(Documento $documento): JsonResponse
    {
        if (!$this->podeExcluir($documento)) {
            return response()->json(['ok' => false, 'erro' => 'Sem permissao para excluir este documento.'], 403);
        }

        $caminhoCompleto = public_path($documento->caminho);
        if (file_exists($caminhoCompleto)) {
            unlink($caminhoCompleto);
        }

        $documento->delete();

        return response()->json(['ok' => true]);
    }

    private function podeExcluir(Documento $documento): bool
    {
        if (!auth()->check()) {
            return false;
        }

        $user = auth()->user();

        if ($user instanceof \App\Models\UsuarioAdministrador) {
            return true;
        }

        return $user->siape === $documento->etapa->processo->id_relator;
    }
}

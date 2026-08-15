<?php

namespace App\Http\Controllers;

use App\Enums\DocumentoCondicao;
use App\Enums\DocumentoTipo;
use App\Models\Documento;
use App\Models\Etapa;
use App\Models\Processo;
use App\Services\FluxoProcessual;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentoController extends Controller
{
    public function index(Etapa $etapa): JsonResponse
    {
        return response()->json($etapa->documentos);
    }

    public function show(Documento $documento): JsonResponse
    {
        $documento->load('etapa');

        return response()->json($documento);
    }

    public function store(Request $request, Etapa $etapa, FluxoProcessual $fluxo): RedirectResponse
    {
        $data = $request->validate([
            'tipo' => ['required', 'string', 'in:' . collect(DocumentoTipo::cases())->pluck('value')->implode(',')],
            'condicao' => ['nullable', 'string', 'in:' . collect(DocumentoCondicao::cases())->pluck('value')->implode(',')],
            'anexo' => ['required', 'file', 'max:102400'],
        ]);

        $caminho = $request->file('anexo')->store('documentos/' . $etapa->id, 'public');

        $documento = $etapa->documentos()->create([
            'tipo' => $data['tipo'],
            'condicao' => $data['condicao'] ?? null,
            'data_insercao' => now(),
            'anexo' => $caminho,
        ]);

        if (in_array($documento->condicao, [DocumentoCondicao::Acpp->value, DocumentoCondicao::Pae->value], true)) {
            $fluxo->verificarCondicaoDocumento($etapa->processo);
        }

        return redirect()->back();
    }

    public function download(Documento $documento)
    {
        abort_unless(Storage::disk('public')->exists($documento->anexo), 404);

        return Storage::disk('public')->download($documento->anexo);
    }

    public function destroy(Documento $documento): RedirectResponse
    {
        if (Storage::disk('public')->exists($documento->anexo)) {
            Storage::disk('public')->delete($documento->anexo);
        }

        $documento->delete();

        return redirect()->back();
    }
}

<?php

namespace App\Http\Controllers;

use App\Enums\EtapaTipo;
use App\Enums\ProcessoStatus;
use App\Models\Etapa;
use App\Models\Processo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EtapaController extends Controller
{
    public function index(Request $request, Processo $processo): JsonResponse
    {
        return response()->json(
            $processo->etapas()->with(['rodadasVotacao', 'documentos'])->get()
        );
    }

    public function show(Etapa $etapa): JsonResponse
    {
        $etapa->load(['processo', 'rodadasVotacao.votos', 'documentos']);

        return response()->json($etapa);
    }

    public function store(Request $request, Processo $processo): RedirectResponse
    {
        $data = $request->validate([
            'tipo' => ['required', 'string', 'in:' . collect(EtapaTipo::cases())->pluck('value')->implode(',')],
        ]);

        $etapa = $processo->etapas()->create([
            'tipo' => $data['tipo'],
            'status' => ProcessoStatus::EmElaboracao,
        ]);

        return redirect()->back();
    }

    public function update(Request $request, Etapa $etapa): RedirectResponse
    {
        $data = $request->validate([
            'tipo' => ['sometimes', 'string', 'in:' . collect(EtapaTipo::cases())->pluck('value')->implode(',')],
        ]);

        $etapa->update($data);

        return redirect()->back();
    }

    public function destroy(Etapa $etapa): RedirectResponse
    {
        $etapa->delete();

        return redirect()->back();
    }
}

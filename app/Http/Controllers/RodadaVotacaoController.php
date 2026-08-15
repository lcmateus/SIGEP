<?php

namespace App\Http\Controllers;

use App\Enums\ResultadoVotacao;
use App\Models\Etapa;
use App\Models\RodadaVotacao;
use App\Services\FluxoProcessual;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RodadaVotacaoController extends Controller
{
    public function index(Etapa $etapa): JsonResponse
    {
        return response()->json(
            $etapa->rodadasVotacao()->with('votos')->get()
        );
    }

    public function show(RodadaVotacao $rodadaVotacao): JsonResponse
    {
        $rodadaVotacao->load(['etapa', 'votos.membro', 'presidente']);

        return response()->json($rodadaVotacao);
    }

    public function store(Request $request, Etapa $etapa): RedirectResponse
    {
        $data = $request->validate([
            'data_abertura' => ['nullable', 'date'],
        ]);

        $etapa->rodadasVotacao()->create([
            'data_abertura' => $data['data_abertura'] ?? now(),
        ]);

        return redirect()->back();
    }

    public function encerrar(RodadaVotacao $rodadaVotacao, FluxoProcessual $fluxo): RedirectResponse
    {
        $fluxo->encerrarRodada($rodadaVotacao);

        return redirect()->back();
    }

    public function registrarResultado(Request $request, RodadaVotacao $rodadaVotacao, FluxoProcessual $fluxo): RedirectResponse
    {
        $data = $request->validate([
            'resultado' => ['required', 'string', 'in:' . collect(ResultadoVotacao::cases())->pluck('value')->implode(',')],
        ]);

        $rodadaVotacao->update(['resultado' => $data['resultado']]);

        $fluxo->processarResultadoVotacao($rodadaVotacao);

        return redirect()->back();
    }

    public function destroy(RodadaVotacao $rodadaVotacao): RedirectResponse
    {
        $rodadaVotacao->delete();

        return redirect()->back();
    }
}

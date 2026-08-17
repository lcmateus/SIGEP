<?php

namespace App\Http\Controllers;

use App\Enums\VotoOpcao;
use App\Models\RodadaVotacao;
use App\Models\Voto;
use App\Services\FluxoProcessual;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class VotoController extends Controller
{
    public function index(RodadaVotacao $rodadaVotacao): JsonResponse
    {
        return response()->json(
            $rodadaVotacao->votos()->with('membro')->get()
        );
    }

    public function show(Voto $voto): JsonResponse
    {
        $voto->load(['membro', 'rodada']);

        return response()->json($voto);
    }

    public function store(Request $request, RodadaVotacao $rodadaVotacao, FluxoProcessual $fluxo): RedirectResponse
    {
        $data = $request->validate([
            'opcao' => ['required', 'string', 'in:' . collect(VotoOpcao::cases())->pluck('value')->implode(',')],
            'justificativa' => ['nullable', 'string', 'max:2000'],
            'is_minerva' => ['boolean'],
        ]);

        $isMinerva = $data['is_minerva'] ?? false;

        if ($isMinerva) {
            $this->authorizeMinerva($request, $rodadaVotacao);
        }

        Voto::query()->create([
            'opcao' => $data['opcao'],
            'justificativa' => $data['justificativa'] ?? null,
            'is_minerva' => $isMinerva,
            'membro_id' => $request->user()->id,
            'rodada_id' => $rodadaVotacao->id,
        ]);

        if ($isMinerva) {
            $fluxo->registrarVotoMinerva($rodadaVotacao);
        }

        return redirect()->back();
    }

    private function authorizeMinerva(Request $request, RodadaVotacao $rodadaVotacao): void
    {
        $user = $request->user();

        abort_unless(
            $user && $rodadaVotacao->presidente_id === $user->id,
            403,
            'Apenas o presidente da rodada pode registrar voto de minerva.'
        );
    }
}

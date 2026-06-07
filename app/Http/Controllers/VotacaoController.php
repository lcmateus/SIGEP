<?php

namespace App\Http\Controllers;

use App\Models\Processo;
use App\Models\Voto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VotacaoController extends Controller
{
    public function create(Processo $processo): View
    {
        abort_unless(auth()->check() && auth()->user()->isAtivo(), 403);

        return view('processos.votar', [
            'processo' => $processo,
        ]);
    }

    public function store(Request $request, Processo $processo): RedirectResponse
    {
        abort_unless(auth()->check() && auth()->user()->isAtivo(), 403);
        abort_if($processo->status !== 'ativa', 422, 'Processo sem votacao ativa.');

        $data = $request->validate([
            'tipo' => ['required', 'in:favor,contra,abstencao,favor_com_ressalvas'],
            'justificativa' => ['required_if:tipo,favor_com_ressalvas', 'nullable', 'string'],
        ]);

        abort_if(
            Voto::query()
                ->where('usuario_id', auth()->id())
                ->where('processo_id', $processo->id)
                ->exists(),
            422,
            'Usuario ja votou neste processo.'
        );

        Voto::query()->create([
            'usuario_id' => auth()->id(),
            'processo_id' => $processo->id,
            'tipo' => $data['tipo'],
            'justificativa' => $data['justificativa'] ?? null,
        ]);

        return redirect()->route('resultados')->with('status', 'Voto registrado.');
    }
}

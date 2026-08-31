<?php

namespace App\Http\Controllers;

use App\Models\Documento;
use App\Models\Processo;
use App\Models\RodadaVotacao;
use App\Models\Voto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VotacaoController extends Controller
{
    public function votar(RodadaVotacao $rodada): View
    {
        abort_unless(
            auth()->check() && auth()->user() instanceof \App\Models\UsuarioMembro,
            403
        );

        $processo = $rodada->etapa?->processo;

        abort_if(
            !$processo || $rodada->resultado !== null,
            404,
            'Votacao indisponivel.'
        );

        abort_if(
            Voto::query()
                ->where('id_membro', auth()->user()->siape)
                ->where('id_rodada', $rodada->id)
                ->exists(),
            403,
            'Voce ja votou nesta rodada.'
        );

        $processo->load(['relator', 'administrador', 'etapas']);

        $documentos = Documento::query()
            ->whereIn('etapa_id', $processo->etapas->pluck('id'))
            ->orderBy('data_upload')
            ->get()
            ->groupBy('etapa_id');

        return view('votacoes.votar', [
            'rodada' => $rodada,
            'processo' => $processo,
            'documentosPorEtapa' => $documentos,
        ]);
    }

    public function registrarVoto(Request $request, RodadaVotacao $rodada): RedirectResponse
    {
        abort_unless(
            auth()->check() && auth()->user() instanceof \App\Models\UsuarioMembro,
            403
        );

        abort_if(
            $rodada->resultado !== null,
            422,
            'Votacao ja encerrada.'
        );

        $data = $request->validate([
            'opcao' => ['required', 'in:aprova,desaprova,aprova com resalva,abstenho'],
            'justificativa' => ['nullable', 'string'],
        ]);

        $siape = auth()->user()->siape;

        abort_if(
            Voto::query()
                ->where('id_membro', $siape)
                ->where('id_rodada', $rodada->id)
                ->exists(),
            422,
            'Voce ja votou nesta rodada.'
        );

        Voto::query()->create([
            'opcao' => $data['opcao'],
            'justificativa' => $data['justificativa'] ?? null,
            'is_minerva' => false,
            'id_membro' => $siape,
            'id_rodada' => $rodada->id,
        ]);

        return redirect()->route('votacoes.disponiveis')
            ->with('status', 'Voto registrado com sucesso.');
    }

    public function abertas(): View
    {
        abort_unless(
            auth()->check() && auth()->user() instanceof \App\Models\UsuarioAdministrador,
            403
        );

        $rodadas = RodadaVotacao::query()
            ->whereNull('resultado')
            ->with(['etapa.processo.relator', 'etapa.processo.administrador'])
            ->orderByDesc('data_abertura')
            ->get();

        return view('votacoes.abertas', [
            'rodadas' => $rodadas,
        ]);
    }

    public function atualizarEncerramento(Request $request, RodadaVotacao $rodada): RedirectResponse
    {
        abort_unless(
            auth()->check() && auth()->user() instanceof \App\Models\UsuarioAdministrador,
            403
        );

        $data = $request->validate([
            'data_encerramento' => ['required', 'date', 'after:today'],
        ]);

        $rodada->update(['data_encerramento' => $data['data_encerramento']]);

        return redirect()->route('votacoes.abertas')
            ->with('status', 'Data de encerramento atualizada.');
    }

    public function disponiveis(): View
    {
        abort_unless(auth()->check() && auth()->user() instanceof \App\Models\UsuarioMembro, 403);

        $siape = auth()->user()->siape;

        $rodadas = RodadaVotacao::query()
            ->whereNull('resultado')
            ->whereDoesntHave('votos', fn ($q) => $q->where('id_membro', $siape))
            ->with(['etapa.processo.relator'])
            ->orderByDesc('data_abertura')
            ->get();

        return view('votacoes.disponiveis', [
            'rodadas' => $rodadas,
        ]);
    }

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

        $data = $request->validate([
            'id_rodada' => ['required', 'integer', 'exists:rodada_votacao,id'],
            'opcao' => ['required', 'in:aprova,desaprova,aprova com resalva'],
            'justificativa' => ['nullable', 'string'],
        ]);

        $siape = auth()->user()->siape;

        abort_if(
            Voto::query()
                ->where('id_membro', $siape)
                ->where('id_rodada', $data['id_rodada'])
                ->exists(),
            422,
            'Usuario ja votou nesta rodada.'
        );

        Voto::query()->create([
            'opcao' => $data['opcao'],
            'justificativa' => $data['justificativa'] ?? null,
            'is_minerva' => false,
            'id_membro' => $siape,
            'id_rodada' => $data['id_rodada'],
        ]);

        return redirect()->route('resultados')->with('status', 'Voto registrado.');
    }
}

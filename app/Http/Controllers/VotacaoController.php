<?php

namespace App\Http\Controllers;

use App\Models\Documento;
use App\Models\Etapa;
use App\Models\Processo;
use App\Models\RodadaVotacao;
use App\Models\Voto;
use App\Services\VotacaoService;
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
            $rodada->data_encerramento !== null && now()->gt($rodada->data_encerramento),
            404,
            'Votacao encerrada.'
        );

        abort_if(
            $rodada->etapa?->status === Etapa::STATUS_AGUARDANDO_MINERVA,
            404,
            'Votacao aguardando voto de Minerva.'
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

    public function registrarVoto(Request $request, RodadaVotacao $rodada, VotacaoService $service): RedirectResponse
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

        abort_if(
            $rodada->data_encerramento !== null && now()->gt($rodada->data_encerramento),
            422,
            'Votacao encerrada (prazo expirado).'
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

        if ($service->apurarSeTodosVotaram($rodada)) {
            return redirect()->route('votacoes.disponiveis')
                ->with('status', 'Voto registrado. Todos votaram e a votacao foi apurada automaticamente.');
        }

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
            ->whereDoesntHave('etapa', fn ($q) => $q->where('status', Etapa::STATUS_AGUARDANDO_MINERVA))
            ->whereDoesntHave('votos', fn ($q) => $q->where('id_membro', $siape))
            ->with(['etapa.processo.relator'])
            ->orderByDesc('data_abertura')
            ->get();

        return view('votacoes.disponiveis', [
            'rodadas' => $rodadas,
        ]);
    }

    public function minerva(): View
    {
        abort_unless(auth()->check() && auth()->user() instanceof \App\Models\UsuarioMembro, 403);

        $presidente = auth()->user();

        $isPresidente = $presidente->isPresidente();

        $rodadas = collect();

        if ($isPresidente) {
            $rodadas = RodadaVotacao::query()
                ->whereHas('etapa', fn ($q) => $q->where('status', Etapa::STATUS_AGUARDANDO_MINERVA))
                ->with(['etapa.processo.relator', 'etapa.processo.administrador'])
                ->orderByDesc('data_abertura')
                ->get();
        }

        return view('votacoes.minerva', [
            'rodadas' => $rodadas,
            'isPresidente' => $isPresidente,
        ]);
    }

    public function votarMinerva(Request $request, RodadaVotacao $rodada, VotacaoService $service): RedirectResponse
    {
        abort_unless(auth()->check() && auth()->user() instanceof \App\Models\UsuarioMembro, 403);

        $presidente = auth()->user();

        abort_if(!$presidente->isPresidente(), 403, 'Apenas o presidente pode registrar voto de Minerva.');

        abort_if(
            $rodada->resultado !== null,
            422,
            'Votacao ja encerrada.'
        );

        $etapa = $rodada->etapa;

        abort_if(
            !$etapa || $etapa->status !== Etapa::STATUS_AGUARDANDO_MINERVA,
            422,
            'Etapa nao esta Aguardando Minerva.'
        );

        $data = $request->validate([
            'opcao' => ['required', 'in:aprova,desaprova'],
        ]);

        $jaVotouMinerva = Voto::query()
            ->where('id_rodada', $rodada->id)
            ->where('is_minerva', true)
            ->exists();

        abort_if($jaVotouMinerva, 422, 'Voto de Minerva ja registrado nesta rodada.');

        Voto::query()->create([
            'opcao' => $data['opcao'],
            'justificativa' => null,
            'is_minerva' => true,
            'id_membro' => $presidente->siape,
            'id_rodada' => $rodada->id,
        ]);

        $service->aplicarVotoMinerva($rodada, $data['opcao']);

        $presidente->update(['is_presidente' => false]);

        return redirect()->route('votacoes.minerva')
            ->with('status', 'Voto de Minerva registrado. Resultado apurado com sucesso.');
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

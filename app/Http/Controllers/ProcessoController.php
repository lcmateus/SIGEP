<?php

namespace App\Http\Controllers;

use App\Models\Documento;
use App\Models\Etapa;
use App\Models\Processo;
use App\Models\RodadaVotacao;
use App\Models\UsuarioAdministrador;
use App\Models\UsuarioMembro;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class ProcessoController extends Controller
{
    public function index(): View
    {
        return view('processos.index', [
            'processos' => Processo::query()->with('relator')->latest()->get(),
        ]);
    }

    public function devolvidos(): View
    {
        $processos = Processo::query()
            ->whereHas('etapas', fn ($query) => $query->where('status', Etapa::STATUS_DEVOLVIDO))
            ->with('relator')
            ->latest()
            ->get();

        return view('processos.devolvidos', [
            'processos' => $processos,
        ]);
    }

    public function arquivados(): View
    {
        $query = Processo::query()
            ->whereHas('etapas', fn ($q) => $q->where('status', Etapa::STATUS_ARQUIVADO))
            ->with('relator');

        if (auth()->user() instanceof UsuarioMembro) {
            $query->where('id_relator', auth()->user()->siape);
        }

        $processos = $query->latest()->get();

        return view('processos.arquivados', [
            'processos' => $processos,
        ]);
    }

    public function meus(): View
    {
        $processos = Processo::query()
            ->where('id_relator', auth()->user()->siape)
            ->whereDoesntHave('etapas', fn ($q) => $q->whereIn('status', [
                Etapa::STATUS_DEVOLVIDO,
                Etapa::STATUS_ARQUIVADO,
            ]))
            ->with('relator')
            ->latest()
            ->get();

        return view('processos.meus', [
            'processos' => $processos,
        ]);
    }

    public function publicos(): View
    {
        $processos = Processo::query()
            ->whereDoesntHave('etapas', fn ($q) => $q->whereIn('status', [
                Etapa::STATUS_DEVOLVIDO,
                Etapa::STATUS_ARQUIVADO,
            ]))
            ->with(['relator', 'etapas'])
            ->latest()
            ->get();

        $documentosPorEtapa = Documento::query()
            ->whereIn('etapa_id', $processos->flatMap->etapas->pluck('id')->unique())
            ->orderBy('data_upload')
            ->get()
            ->groupBy('etapa_id');

        $rodadasPorProcesso = RodadaVotacao::query()
            ->whereHas('etapa', fn ($query) => $query->whereIn('numero_sei_processo', $processos->pluck('numero_sei')))
            ->with('etapa')
            ->orderBy('data_abertura')
            ->get()
            ->groupBy(fn ($rodada) => $rodada->etapa?->numero_sei_processo);

        return view('processos.publicos', [
            'processos' => $processos,
            'documentosPorEtapa' => $documentosPorEtapa,
            'rodadasPorProcesso' => $rodadasPorProcesso,
        ]);
    }

    public function create(): View
    {
        $this->authorizeAdmin();

        return view('processos.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeAdmin();

        $data = $request->validate([
            'numero_sei' => ['required', 'string', 'max:255', 'unique:processo,numero_sei'],
        ]);

        $relator = $this->proximoRelator();

        if (!$relator) {
            return back()
                ->withErrors(['numero_sei' => 'Nenhum membro ativo disponivel para ser relator.'])
                ->withInput();
        }

        $processo = Processo::query()->create($data + [
            'data_admissao' => Carbon::today(),
            'data_devolucao' => null,
            'id_administrador' => auth()->user()->siape,
            'id_relator' => $relator->siape,
        ]);

        Etapa::query()->create([
            'numero_sei_processo' => $processo->numero_sei,
            'ordem' => 1,
            'tipo' => Etapa::TIPO_JUIZO,
            'status' => Etapa::STATUS_EM_ELABORACAO,
        ]);

        return redirect()->route('processos.index')->with('status', 'Processo criado.');
    }

    public function show(Processo $processo): View
    {
        $processo->load(['relator', 'administrador', 'etapas']);

        $documentos = Documento::query()
            ->whereIn('etapa_id', $processo->etapas->pluck('id'))
            ->orderBy('data_upload')
            ->get()
            ->groupBy('etapa_id');

        $rodadas = RodadaVotacao::query()
            ->whereHas('etapa', fn ($query) => $query->where('numero_sei_processo', $processo->numero_sei))
            ->with('etapa')
            ->orderBy('data_abertura')
            ->get();

        return view('processos.show', [
            'processo' => $processo,
            'documentosPorEtapa' => $documentos,
            'rodadas' => $rodadas,
        ]);
    }

    public function aceitar(Processo $processo): View
    {
        $processo->load(['relator', 'administrador', 'etapas']);

        $documentos = Documento::query()
            ->whereIn('etapa_id', $processo->etapas->pluck('id'))
            ->orderBy('data_upload')
            ->get()
            ->groupBy('etapa_id');

        $rodadas = RodadaVotacao::query()
            ->whereHas('etapa', fn ($query) => $query->where('numero_sei_processo', $processo->numero_sei))
            ->with('etapa')
            ->orderBy('data_abertura')
            ->get();

        return view('processos.aceitar', [
            'processo' => $processo,
            'documentosPorEtapa' => $documentos,
            'rodadas' => $rodadas,
        ]);
    }

    public function processarAceitar(Request $request, Processo $processo): RedirectResponse
    {
        abort_unless(
            auth()->check() && auth()->user()->siape === $processo->id_relator,
            403
        );

        $data = $request->validate([
            'decisao' => ['required', 'in:sim,nao'],
        ]);

        $etapaAtual = $processo->etapas
            ->sortByDesc('ordem')
            ->first(fn ($etapa) => $etapa->status !== Etapa::STATUS_FINALIZADO);

        if (!$etapaAtual || $etapaAtual->tipo !== Etapa::TIPO_JUIZO) {
            return back()->withErrors(['decisao' => 'Etapa invalida para esta operacao.']);
        }

        if ($data['decisao'] === 'nao') {
            $etapaAtual->update(['status' => Etapa::STATUS_DEVOLVIDO]);
            $processo->update(['data_devolucao' => now()]);

            return redirect()->route('processos.meus')
                ->with('status', 'Processo devolvido ao Secretario Geral.');
        }

        $etapaAtual->update(['status' => Etapa::STATUS_FINALIZADO]);

        $proximaOrdem = ($processo->etapas->max('ordem') ?? 0) + 1;

        Etapa::query()->create([
            'numero_sei_processo' => $processo->numero_sei,
            'ordem' => $proximaOrdem,
            'tipo' => Etapa::TIPO_PROCEDIMENTO_PRELIMINAR,
            'status' => Etapa::STATUS_EM_ELABORACAO,
        ]);

        return redirect()->route('processos.show', $processo)
            ->with('status', 'Processo encaminhado para Procedimento Preliminar.');
    }

    public function update(Request $request, Processo $processo): RedirectResponse
    {
        $this->authorizeAdmin();

        $data = $request->validate([
            'data_admissao' => ['required', 'date'],
            'data_devolucao' => ['nullable', 'date', 'after_or_equal:data_admissao'],
            'id_administrador' => ['required', 'string', 'exists:usuario_administrador,siape'],
            'id_relator' => ['required', 'string', 'exists:usuario_membro,siape'],
        ]);

        $processo->update($data);

        return redirect()->route('processos.show', $processo)->with('status', 'Processo atualizado.');
    }

    public function destroy(Processo $processo): RedirectResponse
    {
        $this->authorizeAdmin();

        $processo->delete();

        return redirect()->route('processos.index')->with('status', 'Processo excluido.');
    }

    public function acaoAdmin(Request $request, Processo $processo): RedirectResponse
    {
        $this->authorizeAdmin();

        $etapaAtual = $processo->etapa_atual;

        if (!$etapaAtual || $etapaAtual->status !== Etapa::STATUS_DEVOLVIDO) {
            return back()->with('error', 'Etapa invalida para esta operacao.');
        }

        $data = $request->validate([
            'decisao' => ['required', 'in:reativar,arquivar'],
        ]);

        if ($data['decisao'] === 'reativar') {
            $etapaAtual->update(['status' => Etapa::STATUS_EM_ELABORACAO]);
        } elseif ($data['decisao'] === 'arquivar') {
            $etapaAtual->update(['status' => Etapa::STATUS_ARQUIVADO]);
        }

        return redirect()->route('processos.show', $processo)
            ->with('status', 'Acao executada com sucesso.');
    }

    public function devolverSecretario(Processo $processo): RedirectResponse
    {
        abort_unless(
            auth()->check()
                && auth()->user() instanceof UsuarioMembro
                && auth()->user()->siape === $processo->id_relator,
            403
        );

        $etapaEmElaboracao = $processo->etapas
            ->sortByDesc('ordem')
            ->first(fn ($etapa) => $etapa->status === Etapa::STATUS_EM_ELABORACAO);

        if (!$etapaEmElaboracao) {
            return back()->with('error', 'Nenhuma etapa em elaboracao para devolver.');
        }

        $etapaEmElaboracao->update(['status' => Etapa::STATUS_DEVOLVIDO]);
        $processo->update(['data_devolucao' => now()]);

        return redirect()->route('processos.show', $processo)
            ->with('status', 'Processo devolvido ao Secretario Geral.');
    }

    public function iniciarVotacao(Processo $processo): RedirectResponse
    {
        abort_unless(
            auth()->check()
                && auth()->user() instanceof UsuarioMembro
                && auth()->user()->siape === $processo->id_relator,
            403
        );

        $etapaEmElaboracao = $processo->etapas
            ->sortByDesc('ordem')
            ->first(fn ($etapa) => $etapa->status === Etapa::STATUS_EM_ELABORACAO);

        if (!$etapaEmElaboracao) {
            return back()->with('error', 'Nenhuma etapa em elaboracao para iniciar votacao.');
        }

        $etapaEmElaboracao->update(['status' => Etapa::STATUS_EM_VOTACAO]);

        RodadaVotacao::query()->create([
            'data_abertura' => now(),
            'data_encerramento' => now()->addWeeks(2),
            'resultado' => null,
            'id_etapa' => $etapaEmElaboracao->id,
        ]);

        UsuarioMembro::query()->where('is_presidente', true)->update(['is_presidente' => false]);

        $novoPresidente = UsuarioMembro::query()
            ->whereNotNull('data_ativacao')
            ->inRandomOrder()
            ->first();

        if ($novoPresidente) {
            $novoPresidente->update(['is_presidente' => true]);
        }

        return redirect()->route('dashboard')
            ->with('status', 'Votacao iniciada com sucesso.');
    }

    private function proximoRelator(): ?UsuarioMembro
    {
        return UsuarioMembro::query()
            ->whereNotNull('data_ativacao')
            ->withCount('processosComoRelator')
            ->orderBy('processos_como_relator_count')
            ->orderBy('data_ativacao')
            ->first();
    }

    private function authorizeAdmin(): void
    {
        abort_unless(auth()->check() && auth()->user() instanceof UsuarioAdministrador, 403);
    }
}

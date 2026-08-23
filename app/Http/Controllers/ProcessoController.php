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

    public function publicos(): View
    {
        $processos = Processo::query()->with(['relator', 'etapas'])->latest()->get();

        $documentosPorEtapa = Documento::query()
            ->where('referencia_tipo', 'etapa')
            ->whereIn('referencia_id', $processos->flatMap->etapas->pluck('id')->unique())
            ->orderBy('data_upload')
            ->get()
            ->groupBy('referencia_id');

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
            ->where('referencia_tipo', 'etapa')
            ->whereIn('referencia_id', $processo->etapas->pluck('id'))
            ->orderBy('data_upload')
            ->get()
            ->groupBy('referencia_id');

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

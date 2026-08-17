<?php

namespace App\Http\Controllers;

use App\Enums\ProcessoStatus;
use App\Models\Processo;
use App\Services\DistribuicaoService;
use App\Services\FluxoProcessual;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProcessoController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Processo::query()
            ->with(['administrador', 'relator', 'etapas']);

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        return response()->json($query->paginate(15));
    }

    public function show(Processo $processo): JsonResponse
    {
        $processo->load(['administrador', 'relator', 'etapas.rodadasVotacao.votos', 'etapas.documentos']);

        return response()->json($processo);
    }

    public function store(Request $request, DistribuicaoService $distribuicao): RedirectResponse
    {
        $data = $request->validate([
            'numero_sei' => ['required', 'string', 'max:255', 'unique:processos,numero_sei'],
            'data_admissao' => ['nullable', 'date'],
            'data_devolucao' => ['nullable', 'date', 'after_or_equal:data_admissao'],
            'id_relator' => ['nullable', 'exists:users,id'],
        ]);

        $idRelator = $data['id_relator'] ?? $distribuicao->distribuirRelator();

        Processo::query()->create([
            'numero_sei' => $data['numero_sei'],
            'data_admissao' => $data['data_admissao'] ?? null,
            'data_devolucao' => $data['data_devolucao'] ?? null,
            'status' => ProcessoStatus::EmElaboracao,
            'id_administrador' => $request->user()?->id,
            'id_relator' => $idRelator,
        ]);

        return redirect()->route('processos.create');
    }

    public function update(Request $request, Processo $processo): RedirectResponse
    {
        $data = $request->validate([
            'numero_sei' => ['sometimes', 'string', 'max:255', 'unique:processos,numero_sei,' . $processo->id],
            'data_admissao' => ['sometimes', 'nullable', 'date'],
            'data_devolucao' => ['sometimes', 'nullable', 'date', 'after_or_equal:data_admissao'],
            'id_relator' => ['sometimes', 'nullable', 'exists:users,id'],
        ]);

        $processo->update($data);

        return redirect()->back();
    }

    public function destroy(Processo $processo): RedirectResponse
    {
        $processo->delete();

        return redirect()->back();
    }

    public function arquivar(Processo $processo, FluxoProcessual $fluxo): RedirectResponse
    {
        $fluxo->arquivar($processo);

        return redirect()->back();
    }

    public function abrirVotacao(Processo $processo, FluxoProcessual $fluxo): RedirectResponse
    {
        $fluxo->abrirVotacao($processo);

        return redirect()->back();
    }
}

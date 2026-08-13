<?php

namespace App\Http\Controllers;

use App\Models\Processo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProcessoController extends Controller
{
    public function index(): View
    {
        return view('processos.index', [
            'processos' => Processo::query()->latest()->get(),
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
            'titulo' => ['required', 'string', 'max:255'],
            'descricao' => ['required', 'string'],
            'status' => ['nullable', 'in:ativa,encerrada,rascunho'],
            'data_inicio' => ['nullable', 'date'],
            'data_fim' => ['nullable', 'date', 'after_or_equal:data_inicio'],
        ]);

        $processo = Processo::query()->create([
            ...$data,
            'status' => $data['status'] ?? 'ativa',
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('processos.show', $processo)->with('status', 'Processo criado.');
    }

    public function show(Processo $processo): View
    {
        return view('processos.show', [
            'processo' => $processo,
        ]);
    }

    public function update(Request $request, Processo $processo): RedirectResponse
    {
        $this->authorizeAdmin();

        $data = $request->validate([
            'titulo' => ['required', 'string', 'max:255'],
            'descricao' => ['required', 'string'],
            'status' => ['required', 'in:ativa,encerrada,rascunho'],
            'data_inicio' => ['nullable', 'date'],
            'data_fim' => ['nullable', 'date', 'after_or_equal:data_inicio'],
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

    private function authorizeAdmin(): void
    {
        abort_unless(auth()->check() && auth()->user()->isAdmin(), 403);
    }
}

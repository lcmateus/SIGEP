<?php

namespace App\Http\Controllers;

use App\Models\Processo;
use App\Models\UsuarioAdministrador;
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
            'numero_sei' => ['required', 'string', 'max:255', 'unique:processos,numero_sei'],
            'data_admissao' => ['nullable', 'date'],
            'data_devolucao' => ['nullable', 'date', 'after_or_equal:data_admissao'],
            'id_administrador' => ['nullable', 'exists:usuario_administrador,siape'],
            'id_relator' => ['nullable', 'exists:usuario_membro,siape'],
        ]);

        Processo::query()->create($data);

        return redirect()->route('processos.index')->with('status', 'Processo criado.');
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
            'numero_sei' => ['required', 'string', 'max:255', 'unique:processos,numero_sei,'.$processo->numero_sei],
            'data_admissao' => ['nullable', 'date'],
            'data_devolucao' => ['nullable', 'date', 'after_or_equal:data_admissao'],
            'id_administrador' => ['nullable', 'exists:usuario_administrador,siape'],
            'id_relator' => ['nullable', 'exists:usuario_membro,siape'],
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
        abort_unless(auth()->check() && auth()->user() instanceof UsuarioAdministrador, 403);
    }
}
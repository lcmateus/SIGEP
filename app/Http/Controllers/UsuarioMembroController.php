<?php

namespace App\Http\Controllers;

use App\Models\UsuarioAdministrador;
use App\Models\UsuarioMembro;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UsuarioMembroController extends Controller
{
    public function index(): View
    {
        $this->authorizeAdmin();

        return view('usuarios.list', [
            'usuarios' => UsuarioMembro::query()->latest()->get(),
            'totalAdmins' => UsuarioAdministrador::query()->count(),
            'totalPendentes' => UsuarioMembro::query()->whereNull('data_ativacao')->count(),
        ]);
    }

    public function update(Request $request, UsuarioMembro $usuario): RedirectResponse
    {
        $this->authorizeAdmin();

        $data = $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:usuario_membro,email,'.$usuario->siape],
            'siape' => ['required', 'string', 'max:20', 'unique:usuario_membro,siape,'.$usuario->siape],
            'data_ativacao' => ['nullable', 'date'],
            'ativado_por' => ['nullable', 'exists:usuario_administrador,siape'],
            'is_presidente' => ['boolean'],
        ]);

        $usuario->update($data);

        return redirect()->route('usuarios.list')->with('status', 'Usuario atualizado.');
    }

    public function approve(UsuarioMembro $usuario): RedirectResponse
    {
        $this->authorizeAdmin();

        $usuario->update(['data_ativacao' => now(), 'ativado_por' => auth()->user()->siape]);

        return redirect()->route('usuarios.list')->with('status', 'Usuario aprovado.');
    }

    public function destroy(UsuarioMembro $usuario): RedirectResponse
    {
        $this->authorizeAdmin();

        abort_if($usuario->is_presidente, 422, 'O presidente nao pode ser excluido.');

        $usuario->delete();

        return redirect()->route('usuarios.list')->with('status', 'Usuario excluido.');
    }

    private function authorizeAdmin(): void
    {
        abort_unless(auth()->check() && auth()->user() instanceof UsuarioAdministrador, 403);
    }
}

<?php

namespace App\Http\Controllers;

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
        ]);
    }

    public function update(Request $request, UsuarioMembro $usuario): RedirectResponse
    {
        $this->authorizeAdmin();

        $data = $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:usuario_membros,email,'.$usuario->id],
            'siape' => ['required', 'string', 'max:20', 'unique:usuario_membros,siape,'.$usuario->id],
            'data_ativacao' => ['nullable', 'date'],
            'ativado_por' => ['nullable', 'exists:usuario_administradors,id'],
            'is_presidente' => ['boolean'],
        ]);

        $usuario->update($data);

        return redirect()->route('usuarios.list')->with('status', 'Usuario atualizado.');
    }

    public function approve(UsuarioMembro $usuario): RedirectResponse
    {
        $this->authorizeAdmin();

        $usuario->update(['data_ativacao' => now(), 'ativado_por' => auth()->id()]);

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
        abort_unless(auth()->check() && auth()->user()->isAdmin(), 403);
    }
}

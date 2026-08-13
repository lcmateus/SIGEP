<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UsuarioController extends Controller
{
    public function index(): View
    {
        $this->authorizeAdmin();

        return view('usuarios.list', [
            'usuarios' => User::query()->latest()->get(),
            'totalAdmins' => User::query()->where('role', 'admin')->count(),
            'totalPendentes' => User::query()->where('status', 'pendente')->count(),
        ]);
    }

    public function update(Request $request, User $usuario): RedirectResponse
    {
        $this->authorizeAdmin();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$usuario->id],
            'siape' => ['required', 'string', 'max:20', 'unique:users,siape,'.$usuario->id],
            'tipo_membro' => ['nullable', 'in:titular,suplente'],
            'status' => ['required', 'in:pendente,ativo,inativo'],
            'role' => ['required', 'in:admin,membro'],
        ]);

        $usuario->update($data);

        return redirect()->route('usuarios.list')->with('status', 'Usuario atualizado.');
    }

    public function approve(User $usuario): RedirectResponse
    {
        $this->authorizeAdmin();

        $usuario->update(['status' => 'ativo']);

        return redirect()->route('usuarios.list')->with('status', 'Usuario aprovado.');
    }

    public function destroy(User $usuario): RedirectResponse
    {
        $this->authorizeAdmin();

        abort_if($usuario->isAdmin(), 422, 'O admin principal nao pode ser excluido.');

        $usuario->delete();

        return redirect()->route('usuarios.list')->with('status', 'Usuario excluido.');
    }

    private function authorizeAdmin(): void
    {
        abort_unless(auth()->check() && auth()->user()->isAdmin(), 403);
    }
}

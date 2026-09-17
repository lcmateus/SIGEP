<?php

namespace App\Http\Controllers;

use App\Models\UsuarioAdministrador;
use App\Rules\SenhaForte;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UsuarioAdministradorController extends Controller
{
    public function index(): View
    {
        $this->authorizeAdmin();

        return view('administradores.list', [
            'administradores' => UsuarioAdministrador::query()->latest()->get(),
        ]);
    }

    public function create(): View
    {
        $this->authorizeAdmin();

        return view('administradores.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeAdmin();

        $data = $request->validate([
            'siape' => ['required', 'string', 'max:20', 'unique:usuario_administradors,siape'],
            'nome' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:usuario_administradors,email'],
            'password' => ['required', 'string', 'confirmed', new SenhaForte],
        ]);

        $data['password'] = bcrypt($data['password']);
        UsuarioAdministrador::create($data);

        return redirect()->route('administradores.list')->with('status', 'Administrador criado.');
    }

    public function update(Request $request, UsuarioAdministrador $administrador): RedirectResponse
    {
        $this->authorizeAdmin();

        $data = $request->validate([
            'siape' => ['required', 'string', 'max:20', 'unique:usuario_administradors,siape,'.$administrador->id],
            'nome' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:usuario_administradors,email,'.$administrador->id],
            'password' => ['nullable', 'string', 'confirmed', new SenhaForte],
        ]);

        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = bcrypt($data['password']);
        }

        $administrador->update($data);

        return redirect()->route('administradores.list')->with('status', 'Administrador atualizado.');
    }

    public function destroy(UsuarioAdministrador $administrador): RedirectResponse
    {
        $this->authorizeAdmin();

        $administrador->delete();

        return redirect()->route('administradores.list')->with('status', 'Administrador excluido.');
    }

    private function authorizeAdmin(): void
    {
        abort_unless(auth()->check() && auth()->user()->isAdmin(), 403);
    }
}

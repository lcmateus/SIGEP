<?php

namespace App\Http\Controllers;

use App\Models\UsuarioAdministrador;
use App\Rules\EmailUnico;
use App\Rules\SenhaForte;
use App\Rules\SiapeUnico;
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
            'siape' => ['required', 'string', 'max:20', new SiapeUnico],
            'nome' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', new EmailUnico],
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
            'siape' => ['required', 'string', 'max:20', new SiapeUnico($administrador->siape)],
            'nome' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', new EmailUnico($administrador->siape)],
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

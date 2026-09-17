<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use App\Rules\SenhaForte;

class PerfilController extends Controller
{
    public function edit(): View
    {
        return view('perfil.edit', [
            'usuario' => auth()->user(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = auth()->user();

        $data = $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:' . $user->getTable() . ',email,' . $user->siape . ',siape'],
            'password' => ['nullable', 'confirmed', new SenhaForte],
        ]);

        $user->nome = $data['nome'];
        $user->email = $data['email'];

        if (! empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        return redirect()->route('perfil')->with('status', 'Perfil atualizado com sucesso.');
    }
}

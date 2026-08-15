<?php

namespace App\Http\Controllers;

use App\Enums\UserStatus;
use App\Enums\UserTipo;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'siape' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials)) {
            return back()->withErrors([
                'siape' => 'SIAPE ou senha invalidos.',
            ])->onlyInput('siape');
        }

        $request->session()->regenerate();

        if (! $request->user()->isAtivo()) {
            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()->withErrors([
                'siape' => 'Cadastro aguardando autorizacao.',
            ])->onlyInput('siape');
        }

        return redirect()->route('dashboard');
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'siape' => ['required', 'string', 'max:30', 'unique:users,siape'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'min:8'],
            'tipo_membro' => ['nullable', 'string'],
        ]);

        $isFirstUser = ! User::query()->exists();

        User::query()->create([
            'siape' => $data['siape'],
            'nome' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'tipo' => $isFirstUser ? UserTipo::Admin : UserTipo::Membro,
            'status' => $isFirstUser ? UserStatus::Ativo : UserStatus::Pendente,
        ]);

        return redirect()
            ->route('home')
            ->with('status', $isFirstUser
                ? 'Administrador inicial criado com sucesso.'
                : 'Cadastro enviado para autorizacao.'
            );
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('home.home');
    }

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
                'siape' => 'Cadastro aguardando aprovacao.',
            ])->onlyInput('siape');
        }

        return redirect()->route('dashboard');
    }

    public function showRegister(): View
    {
        return view('home.cadastro');
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'siape' => ['required', 'string', 'max:20', 'unique:users,siape'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'tipo_membro' => ['required', 'in:titular,suplente'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        User::query()->create([
            ...$data,
            'role' => 'membro',
            'status' => 'pendente',
        ]);

        return redirect()->route('home')->with('status', 'Cadastro enviado para aprovacao.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}

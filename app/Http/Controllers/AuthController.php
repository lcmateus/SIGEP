<?php

namespace App\Http\Controllers;

use App\Models\UsuarioMembro;
use App\Models\UsuarioAdministrador;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('home.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'siape' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $membro = UsuarioMembro::where('siape', $credentials['siape'])->first();
        
        if ($membro && Auth::guard('web')->validate(['siape' => $credentials['siape'], 'password' => $credentials['password']])) {
            if (!$membro->data_ativacao) {
                return back()->withErrors([
                    'siape' => 'Cadastro aguardando aprovacao.',
                ])->onlyInput('siape');
            }
            
            Auth::login($membro);
            $request->session()->regenerate();
            return redirect()->route('dashboard');
        }

        $admin = UsuarioAdministrador::where('siape', $credentials['siape'])->first();
        
        if ($admin && Auth::guard('web')->validate(['siape' => $credentials['siape'], 'password' => $credentials['password']])) {
            Auth::login($admin);
            $request->session()->regenerate();
            return redirect()->route('dashboard');
        }

        return back()->withErrors([
            'siape' => 'SIAPE ou senha invalidos.',
        ])->onlyInput('siape');
    }

    public function showRegister(): View
    {
        return view('home.cadastro');
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'siape' => ['required', 'string', 'max:20', 'unique:usuario_membro,siape'],
            'nome' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:usuario_membro,email'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $data['password'] = bcrypt($data['password']);
        UsuarioMembro::create($data);

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

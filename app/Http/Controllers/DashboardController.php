<?php

namespace App\Http\Controllers;

use App\Models\Processo;
use App\Models\UsuarioAdministrador;
use App\Models\UsuarioMembro;
use App\Models\Voto;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        $role = match (true) {
            $user instanceof UsuarioAdministrador => 'admin',
            $user instanceof UsuarioMembro => 'membro',
            default => 'guest',
        };

        return view('dashboard', [
            'usuario' => $role,
            'totalUsuarios' => UsuarioMembro::query()->count() + UsuarioAdministrador::query()->count(),
            'votacoesAtivas' => Processo::query()->where('status', 'ativa')->count(),
            'votacoesEncerradas' => Processo::query()->where('status', 'encerrada')->count(),
            'totalVotos' => Voto::query()->count(),
            'votacoesDisponiveis' => Processo::query()->where('status', 'ativa')->count(),
            'votacoesRealizadas' => $user ? Voto::query()->where('usuario_id', $user->siape)->count() : 0,
            'processosRecentes' => Processo::query()->latest()->limit(5)->get(),
        ]);
    }
}
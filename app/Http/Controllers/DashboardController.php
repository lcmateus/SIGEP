<?php

namespace App\Http\Controllers;

use App\Models\Processo;
use App\Models\User;
use App\Models\Voto;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        return view('dashboard', [
            'usuario' => $user?->role ?? 'guest',
            'totalUsuarios' => User::query()->count(),
            'votacoesAtivas' => Processo::query()->where('status', 'ativa')->count(),
            'votacoesEncerradas' => Processo::query()->where('status', 'encerrada')->count(),
            'totalVotos' => Voto::query()->count(),
            'votacoesDisponiveis' => Processo::query()->where('status', 'ativa')->count(),
            'votacoesRealizadas' => $user ? Voto::query()->where('usuario_id', $user->id)->count() : 0,
            'processosRecentes' => Processo::query()->latest()->limit(5)->get(),
        ]);
    }
}

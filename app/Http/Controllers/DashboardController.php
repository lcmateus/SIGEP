<?php

namespace App\Http\Controllers;

use App\Models\Processo;
use App\Models\UsuarioAdministrador;
use App\Models\UsuarioMembro;
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

        $recentesQuery = Processo::query()->with('relator');

        if ($user instanceof UsuarioMembro) {
            $recentesQuery->where('id_relator', $user->siape);
        }

        return view('dashboard', [
            'usuario' => $role,
            'totalProcessos' => Processo::query()->count(),
            'emAndamento' => Processo::query()->whereNull('data_devolucao')->count(),
            'devolvidos' => Processo::query()->whereNotNull('data_devolucao')->count(),
            'membrosAtivos' => UsuarioMembro::query()->whereNotNull('data_ativacao')->count(),
            'meusProcessos' => $user instanceof UsuarioMembro
                ? Processo::query()->where('id_relator', $user->siape)->count()
                : 0,
            'processosRecentes' => $recentesQuery->latest()->limit(5)->get(),
        ]);
    }
}

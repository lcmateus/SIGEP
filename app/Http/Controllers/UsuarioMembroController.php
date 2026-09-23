<?php

namespace App\Http\Controllers;

use App\Models\RodadaVotacao;
use App\Models\UsuarioAdministrador;
use App\Models\UsuarioMembro;
use App\Rules\EmailUnico;
use App\Rules\SiapeUnico;
use App\Services\MembroService;
use App\Services\NotificacaoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UsuarioMembroController extends Controller
{
    public function index(): View
    {
        $this->authorizeAdmin();

        return view('usuarios.list', [
            'usuarios' => UsuarioMembro::query()->latest()->get(),
            'siapePresidentes' => RodadaVotacao::query()
                ->whereNull('resultado')
                ->whereNotNull('id_presidente')
                ->distinct()
                ->pluck('id_presidente')
                ->all(),
            'totalAdmins' => UsuarioAdministrador::query()->count(),
            'totalPendentes' => UsuarioMembro::query()->whereNull('data_ativacao')->count(),
        ]);
    }

    public function update(Request $request, UsuarioMembro $usuario): RedirectResponse
    {
        $this->authorizeAdmin();

        $data = $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', new EmailUnico($usuario->siape)],
            'siape' => ['required', 'string', 'max:20', new SiapeUnico($usuario->siape)],
            'data_ativacao' => ['nullable', 'date'],
            'ativado_por' => ['nullable', 'exists:usuario_administrador,siape'],
            'is_presidente' => ['boolean'],
        ]);

        $usuario->update($data);

        return redirect()->route('usuarios.list')->with('status', 'Usuario atualizado.');
    }

    public function approve(UsuarioMembro $usuario): RedirectResponse
    {
        $this->authorizeAdmin();

        $usuario->update(['data_ativacao' => now(), 'ativado_por' => auth()->user()->siape]);

        app(NotificacaoService::class)->notificarCadastroAprovado($usuario);

        return redirect()->route('usuarios.list')->with('status', 'Usuario aprovado.');
    }

    public function destroy(UsuarioMembro $usuario): RedirectResponse
    {
        $this->authorizeAdmin();

        $haOutrosAtivos = UsuarioMembro::query()
            ->whereNotNull('data_ativacao')
            ->where('siape', '!=', $usuario->siape)
            ->exists();

        if ($usuario->isAtivo() && ! $haOutrosAtivos) {
            return back()->with('error', 'Nao e possivel excluir o ultimo membro ativo.');
        }

        app(MembroService::class)->excluir($usuario);

        return redirect()->route('usuarios.list')->with('status', 'Usuario excluido.');
    }

    private function authorizeAdmin(): void
    {
        abort_unless(auth()->check() && auth()->user() instanceof UsuarioAdministrador, 403);
    }
}

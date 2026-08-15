<?php

namespace App\Http\Controllers;

use App\Enums\UserStatus;
use App\Enums\UserTipo;
use App\Models\User;
use App\Services\DistribuicaoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class UsuarioController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = User::query();

        if ($tipo = $request->query('tipo')) {
            $query->where('tipo', $tipo);
        }

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        if ($busca = $request->query('busca')) {
            $query->where(function ($q) use ($busca) {
                $q->where('nome', 'like', "%{$busca}%")
                    ->orWhere('email', 'like', "%{$busca}%")
                    ->orWhere('siape', 'like', "%{$busca}%");
            });
        }

        return response()->json($query->paginate(15));
    }

    public function show(User $usuario): JsonResponse
    {
        $usuario->load(['processosAdministrados', 'processosRelatados', 'votos.rodada.etapa.processo']);

        return response()->json($usuario);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'siape' => ['required', 'string', 'max:30', 'unique:users,siape'],
            'nome' => ['required_without:name', 'string', 'max:255'],
            'name' => ['required_without:nome', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $isFirstUser = ! User::query()->exists();

        User::query()->create([
            'siape' => $data['siape'],
            'nome' => $data['nome'] ?? $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'tipo' => $isFirstUser ? UserTipo::Admin : UserTipo::Membro,
            'status' => $isFirstUser ? UserStatus::Ativo : UserStatus::Pendente,
        ]);

        return redirect()->route('usuarios.list');
    }

    public function update(Request $request, User $usuario): RedirectResponse
    {
        $data = $request->validate([
            'nome' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email', 'max:255', 'unique:users,email,' . $usuario->id],
            'siape' => ['sometimes', 'string', 'max:30', 'unique:users,siape,' . $usuario->id],
            'password' => ['sometimes', 'string', 'min:8', 'confirmed'],
        ]);

        if (isset($data['password'])) {
            $data['password'] = $data['password'];
        }

        $usuario->update($data);

        return redirect()->back();
    }

    public function approve(User $usuario): RedirectResponse
    {
        $usuario->update(['status' => UserStatus::Ativo]);

        return redirect()->route('usuarios.list');
    }

    public function deactivate(User $usuario): RedirectResponse
    {
        $usuario->update(['status' => UserStatus::Pendente]);

        return redirect()->back();
    }

    public function destroy(User $usuario): RedirectResponse
    {
        $usuario->delete();

        return redirect()->back();
    }

    public function transferirSecretaria(Request $request, User $novoSecretario, DistribuicaoService $distribuicao): RedirectResponse
    {
        $data = $request->validate([
            'confirmar' => ['required', 'boolean', 'accepted'],
        ]);

        $secretarioAtual = User::query()
            ->where('tipo', UserTipo::Admin)
            ->where('status', UserStatus::Ativo)
            ->first();

        if (! $secretarioAtual) {
            return redirect()->back()->withErrors(['erro' => 'Nenhum Secretário Geral ativo encontrado.']);
        }

        if ($secretarioAtual->id === $novoSecretario->id) {
            return redirect()->back()->withErrors(['erro' => 'O usuário informado já é o Secretário Geral atual.']);
        }

        if (! $novoSecretario->isMembro() || ! $novoSecretario->isAtivo()) {
            return redirect()->back()->withErrors(['erro' => 'O novo Secretário Geral deve ser um membro ativo.']);
        }

        $distribuicao->redistribuirProcessos($secretarioAtual, $novoSecretario);

        $secretarioAtual->update([
            'tipo' => UserTipo::Membro,
            'status' => UserStatus::Ativo,
        ]);

        $novoSecretario->update([
            'tipo' => UserTipo::Admin,
            'status' => UserStatus::Ativo,
        ]);

        return redirect()->route('usuarios.list')
            ->with('status', 'Função de Secretário Geral transferida com sucesso.');
    }
}

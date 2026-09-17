<?php

namespace App\Http\Controllers;

use App\Models\RecuperacaoSenha;
use App\Models\UsuarioAdministrador;
use App\Models\UsuarioMembro;
use App\Rules\SenhaForte;
use App\Services\NotificacaoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class PasswordResetController extends Controller
{
    private const POOL = 'ABCDEFGHJKMNPQRSTUVWXYZ23456789';

    private const CODIGO_TAMANHO = 6;

    private const VALIDADE_MINUTOS = 10;

    private const MAX_TENTATIVAS = 5;

    public function showRequestForm(): View
    {
        return view('home.forgot-password');
    }

    public function sendCode(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'siape' => ['required', 'string', 'max:20'],
        ]);

        $usuario = UsuarioMembro::query()->where('siape', $data['siape'])->first()
            ?? UsuarioAdministrador::query()->where('siape', $data['siape'])->first();

        if ($usuario) {
            $tipo = $usuario instanceof UsuarioAdministrador ? 'admin' : 'membro';
            $codigo = $this->gerarCodigo();

            RecuperacaoSenha::query()->where('siape', $usuario->siape)->delete();

            RecuperacaoSenha::query()->create([
                'siape' => $usuario->siape,
                'email' => $usuario->email,
                'token' => Hash::make($codigo),
                'expira_em' => now()->addMinutes(self::VALIDADE_MINUTOS),
            ]);

            try {
                app(NotificacaoService::class)->enviarCodigoRecuperacao(
                    $usuario->email,
                    $usuario->nome,
                    $codigo,
                );
            } catch (\Throwable $e) {
                report($e);
            }

            $request->session()->put('recuperacao.siape', $usuario->siape);
            $request->session()->put('recuperacao.tipo', $tipo);
            $request->session()->put('recuperacao.email', $this->mascararEmail($usuario->email));
            $request->session()->forget('recuperacao.autorizado');
        }

        return redirect()->route('password.code')
            ->with('status', 'Se o SIAPE existir, um codigo de recuperacao foi enviado para o e-mail cadastrado.');
    }

    public function showCodeForm(): View|RedirectResponse
    {
        if (! session()->has('recuperacao.siape')) {
            return redirect()->route('forgot-password');
        }

        return view('home.codigo-recuperacao', [
            'email' => session('recuperacao.email'),
        ]);
    }

    public function verifyCode(Request $request): RedirectResponse
    {
        if (! session()->has('recuperacao.siape')) {
            return redirect()->route('forgot-password');
        }

        $data = $request->validate([
            'codigo' => ['required', 'string', 'size:6'],
        ]);

        $siape = session('recuperacao.siape');

        $registro = RecuperacaoSenha::query()
            ->where('siape', $siape)
            ->latest()
            ->first();

        if (! $registro
            || $registro->usada()
            || $registro->expirada()
            || $registro->atingiuLimiteTentativas(self::MAX_TENTATIVAS)
        ) {
            $this->invalidarFluxo($request);

            return redirect()->route('forgot-password')
                ->with('status', 'O codigo informado e invalido ou expirou. Solicite um novo codigo.');
        }

        if (! Hash::check($data['codigo'], $registro->token)) {
            $registro->increment('tentativas');

            if ($registro->atingiuLimiteTentativas(self::MAX_TENTATIVAS)) {
                $this->invalidarFluxo($request);

                return redirect()->route('forgot-password')
                    ->with('status', 'Muitas tentativas incorretas. Solicite um novo codigo.');
            }

            return back()->withErrors(['codigo' => 'Codigo invalido.']);
        }

        $registro->update(['usado_em' => now()]);

        session()->put('recuperacao.autorizado', true);

        return redirect()->route('password.reset');
    }

    public function showResetForm(): View|RedirectResponse
    {
        if (! session()->has('recuperacao.siape') || ! session()->get('recuperacao.autorizado')) {
            return redirect()->route('forgot-password');
        }

        return view('home.redefinir-senha');
    }

    public function resetPassword(Request $request): RedirectResponse
    {
        if (! session()->has('recuperacao.siape') || ! session()->get('recuperacao.autorizado')) {
            return redirect()->route('forgot-password');
        }

        $data = $request->validate([
            'password' => ['required', 'confirmed', new SenhaForte],
        ]);

        $siape = session('recuperacao.siape');
        $tipo = session('recuperacao.tipo');

        $modelo = $tipo === 'admin'
            ? UsuarioAdministrador::query()->where('siape', $siape)->first()
            : UsuarioMembro::query()->where('siape', $siape)->first();

        if (! $modelo) {
            $this->invalidarFluxo($request);

            return redirect()->route('forgot-password')
                ->with('status', 'Nao foi possivel redefinir a senha. Solicite um novo codigo.');
        }

        $modelo->update(['password' => bcrypt($data['password'])]);

        RecuperacaoSenha::query()->where('siape', $siape)->delete();

        app(NotificacaoService::class)->notificarSenhaAlterada($modelo->email, $modelo->nome);

        $this->invalidarFluxo($request);

        return redirect()->route('home')
            ->with('status', 'Senha redefinida com sucesso. Faca login com a nova senha.');
    }

    private function gerarCodigo(): string
    {
        $codigo = '';

        for ($i = 0; $i < self::CODIGO_TAMANHO; $i++) {
            $codigo .= self::POOL[random_int(0, strlen(self::POOL) - 1)];
        }

        return $codigo;
    }

    private function mascararEmail(string $email): string
    {
        [$local, $dominio] = explode('@', $email);

        $localVisivel = mb_substr($local, 0, 1) . str_repeat('*', max(0, mb_strlen($local) - 1));

        return $localVisivel . '@' . $dominio;
    }

    private function invalidarFluxo(Request $request): void
    {
        $request->session()->forget([
            'recuperacao.siape',
            'recuperacao.tipo',
            'recuperacao.email',
            'recuperacao.autorizado',
        ]);
    }
}
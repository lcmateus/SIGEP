<?php

namespace App\Services;

use App\Mail\CadastroAprovado;
use App\Mail\CodigoRecuperacao;
use App\Mail\NovaVotacao;
use App\Mail\NovoCadastroPendente;
use App\Mail\ProcessoDevolvido;
use App\Mail\ProcessoDesignado;
use App\Mail\ProcessoReativado;
use App\Mail\SenhaAlterada;
use App\Mail\VotacaoEncerrada;
use App\Mail\VotacaoTerminaAmanha;
use App\Models\Processo;
use App\Models\RodadaVotacao;
use App\Models\UsuarioAdministrador;
use App\Models\UsuarioMembro;
use Illuminate\Support\Facades\Mail;

class NotificacaoService
{
    protected function enviar(string $email, $mailable): void
    {
        try {
            Mail::to($email)->send($mailable);
        } catch (\Throwable $e) {
            report($e);
        }
    }

    public function notificarNovoCadastroPendente(string $nome, string $siape, string $email): void
    {
        $admins = UsuarioAdministrador::query()->get(['nome', 'email']);

        foreach ($admins as $admin) {
            $this->enviar($admin->email, new NovoCadastroPendente(
                $nome,
                $siape,
                $email,
                now()->format('d/m/Y H:i'),
            ));
        }
    }

    public function notificarCadastroAprovado(UsuarioMembro $membro): void
    {
        $this->enviar($membro->email, new CadastroAprovado($membro->nome));
    }

    public function notificarProcessoDesignado(UsuarioMembro $relator, Processo $processo): void
    {
        $etapa = $processo->etapas()->orderBy('ordem')->first();

        $this->enviar($relator->email, new ProcessoDesignado(
            $relator->nome,
            $processo->numero_sei,
            $etapa?->tipo ?? 'Não informada',
        ));
    }

    public function notificarProcessoDevolvido(Processo $processo): void
    {
        $admin = $processo->administrador;

        if (! $admin) {
            return;
        }

        $this->enviar($admin->email, new ProcessoDevolvido(
            $admin->nome,
            $processo->numero_sei,
            $processo->relator?->nome ?? 'Não informado',
            now()->format('d/m/Y H:i'),
        ));
    }

    public function notificarProcessoReativado(Processo $processo): void
    {
        $relator = $processo->relator;

        if (! $relator) {
            return;
        }

        $this->enviar($relator->email, new ProcessoReativado(
            $relator->nome,
            $processo->numero_sei,
        ));
    }

    public function notificarNovaVotacao(RodadaVotacao $rodada): void
    {
        $processo = $rodada->etapa?->processo;

        if (! $processo) {
            return;
        }

        $membros = UsuarioMembro::query()
            ->whereNotNull('data_ativacao')
            ->get(['nome', 'email']);

        $encerramento = $rodada->data_encerramento?->format('d/m/Y H:i') ?? 'Não informado';

        foreach ($membros as $membro) {
            $this->enviar($membro->email, new NovaVotacao(
                $membro->nome,
                $processo->numero_sei,
                $rodada->etapa?->tipo ?? 'Não informado',
                $encerramento,
            ));
        }

        $admin = $processo->administrador;

        if ($admin) {
            $this->enviar($admin->email, new NovaVotacao(
                $admin->nome,
                $processo->numero_sei,
                $rodada->etapa?->tipo ?? 'Não informado',
                $encerramento,
            ));
        }
    }

    public function notificarEncerramentoProximo(RodadaVotacao $rodada): void
    {
        $processo = $rodada->etapa?->processo;

        if (! $processo) {
            return;
        }

        $votantes = $rodada->votos()
            ->where('is_minerva', false)
            ->pluck('id_membro');

        $membros = UsuarioMembro::query()
            ->whereNotNull('data_ativacao')
            ->whereNotIn('siape', $votantes)
            ->get(['nome', 'email']);

        foreach ($membros as $membro) {
            $this->enviar($membro->email, new VotacaoTerminaAmanha(
                $membro->nome,
                $processo->numero_sei,
                $rodada->data_encerramento?->format('d/m/Y H:i') ?? 'Não informado',
            ));
        }
    }

    public function notificarVotacaoEncerrada(RodadaVotacao $rodada, string $resultado): void
    {
        $processo = $rodada->etapa?->processo;

        if (! $processo) {
            return;
        }

        $resultadoDisplay = str_replace('_', ' ', ucfirst($resultado));

        $membros = UsuarioMembro::query()
            ->whereNotNull('data_ativacao')
            ->get(['nome', 'email']);

        foreach ($membros as $membro) {
            $this->enviar($membro->email, new VotacaoEncerrada(
                $membro->nome,
                $processo->numero_sei,
                $resultadoDisplay,
            ));
        }
    }

    public function enviarCodigoRecuperacao(string $email, string $nome, string $codigo): void
    {
        $this->enviar($email, new CodigoRecuperacao(
            $nome,
            $codigo,
            now()->addMinutes(10)->format('d/m/Y H:i'),
        ));
    }

    public function notificarSenhaAlterada(string $email, string $nome): void
    {
        $this->enviar($email, new SenhaAlterada(
            $nome,
            now()->format('d/m/Y H:i'),
        ));
    }
}
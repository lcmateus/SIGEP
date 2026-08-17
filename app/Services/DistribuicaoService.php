<?php

namespace App\Services;

use App\Enums\UserStatus;
use App\Enums\UserTipo;
use App\Models\Processo;
use App\Models\User;
use Illuminate\Support\Collection;

class DistribuicaoService
{
    public function distribuirRelator(?int $excluirUsuarioId = null): ?int
    {
        $query = User::query()
            ->where('tipo', UserTipo::Membro)
            ->where('status', UserStatus::Ativo);

        if ($excluirUsuarioId) {
            $query->where('id', '!=', $excluirUsuarioId);
        }

        $membros = $query->get();

        if ($membros->isEmpty()) {
            return null;
        }

        $membroMenorCarga = null;
        $menorCarga = PHP_INT_MAX;

        foreach ($membros as $membro) {
            $carga = $this->contarProcessosAtivosComoRelator($membro->id);

            if ($carga < $menorCarga) {
                $menorCarga = $carga;
                $membroMenorCarga = $membro;
            }
        }

        return $membroMenorCarga?->id;
    }

    public function redistribuirProcessos(User $antigoSecretario, User $novoSecretario): void
    {
        $processosComoAdministrador = Processo::query()
            ->where('id_administrador', $antigoSecretario->id)
            ->get();

        foreach ($processosComoAdministrador as $processo) {
            $processo->update(['id_administrador' => $novoSecretario->id]);
        }

        $processosComoRelator = Processo::query()
            ->where('id_relator', $antigoSecretario->id)
            ->get();

        foreach ($processosComoRelator as $processo) {
            $novoRelatorId = $this->distribuirRelator($antigoSecretario->id);

            if ($novoRelatorId) {
                $processo->update(['id_relator' => $novoRelatorId]);
            }
        }
    }

    private function contarProcessosAtivosComoRelator(int $usuarioId): int
    {
        return Processo::query()
            ->where('id_relator', $usuarioId)
            ->whereIn('status', [
                'em_elaboracao',
                'em_votacao',
                'aguardando_minerva',
                'votacao_encerrada',
            ])
            ->count();
    }

    public function obterMembroComMenorCarga(): ?User
    {
        $relatorId = $this->distribuirRelator();

        if ($relatorId) {
            return User::find($relatorId);
        }

        return null;
    }
}
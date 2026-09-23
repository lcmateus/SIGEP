<?php

namespace App\Services;

use App\Models\UsuarioMembro;

class RelatorService
{
    /**
     * Escolhe o proximo relator: membro ativo com menos processos como relator,
     * desempatando pela data de ativacao mais antiga.
     */
    public function proximoRelator(?string $excluirSiape = null): ?UsuarioMembro
    {
        return UsuarioMembro::query()
            ->whereNotNull('data_ativacao')
            ->when($excluirSiape, fn ($query) => $query->where('siape', '!=', $excluirSiape))
            ->withCount('processosComoRelator')
            ->orderBy('processos_como_relator_count')
            ->orderBy('data_ativacao')
            ->first();
    }
}
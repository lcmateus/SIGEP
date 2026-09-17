<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecuperacaoSenha extends Model
{
    protected $table = 'recuperacao_senha';

    protected $fillable = [
        'siape',
        'email',
        'token',
        'tentativas',
        'usado_em',
        'expira_em',
    ];

    protected $casts = [
        'tentativas' => 'integer',
        'usado_em' => 'datetime',
        'expira_em' => 'datetime',
    ];

    public function expirada(): bool
    {
        return $this->expira_em->isPast();
    }

    public function usada(): bool
    {
        return $this->usado_em !== null;
    }

    public function atingiuLimiteTentativas(int $limite = 5): bool
    {
        return $this->tentativas >= $limite;
    }
}
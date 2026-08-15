<?php

namespace App\Models;

use App\Enums\VotoOpcao;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Voto extends Model
{
    use HasFactory;
    protected $attributes = [
        'is_minerva' => false,
    ];

    protected $fillable = [
        'opcao',
        'justificativa',
        'is_minerva',
        'membro_id',
        'rodada_id',
    ];

    protected function casts(): array
    {
        return [
            'opcao' => VotoOpcao::class,
            'is_minerva' => 'boolean',
        ];
    }

    public function membro(): BelongsTo
    {
        return $this->belongsTo(User::class, 'membro_id');
    }

    public function rodada(): BelongsTo
    {
        return $this->belongsTo(RodadaVotacao::class, 'rodada_id');
    }
}

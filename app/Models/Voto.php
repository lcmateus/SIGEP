<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Voto extends Model
{
    protected $table = 'voto';

    protected $primaryKey = 'id';

    public $incrementing = true;

    protected $keyType = 'integer';

    public $timestamps = false;

    protected $fillable = [
        'opcao',
        'justificativa',
        'is_minerva',
        'id_membro',
        'id_rodada',
    ];

    protected $casts = [
        'is_minerva' => 'boolean',
    ];

    public function membro(): BelongsTo
    {
        return $this->belongsTo(UsuarioMembro::class, 'id_membro', 'siape');
    }

    public function rodada(): BelongsTo
    {
        return $this->belongsTo(RodadaVotacao::class, 'id_rodada', 'id');
    }
}

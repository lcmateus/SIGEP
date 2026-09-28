<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RodadaVotacao extends Model
{
    protected $table = 'rodada_votacao';

    public $timestamps = false;

    protected $primaryKey = 'id';

    public $incrementing = true;

    protected $keyType = 'integer';

    protected $fillable = [
        'id_etapa',
        'id_presidente',
        'resultado',
        'data_abertura',
        'data_encerramento',
    ];

    protected $casts = [
        'data_abertura' => 'datetime',
        'data_encerramento' => 'datetime',
    ];

    public function etapa(): BelongsTo
    {
        return $this->belongsTo(Etapa::class, 'id_etapa', 'id');
    }

    public function presidente(): BelongsTo
    {
        return $this->belongsTo(UsuarioMembro::class, 'id_presidente', 'siape');
    }

    public function votos(): HasMany
    {
        return $this->hasMany(Voto::class, 'id_rodada', 'id');
    }
}

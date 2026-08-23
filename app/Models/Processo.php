<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Processo extends Model
{
    protected $table = 'processo';

    protected $primaryKey = 'numero_sei';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'numero_sei',
        'data_admissao',
        'data_devolucao',
        'id_administrador',
        'id_relator',
    ];

    protected $casts = [
        'data_admissao' => 'date',
        'data_devolucao' => 'date',
    ];

    public function administrador(): BelongsTo
    {
        return $this->belongsTo(UsuarioAdministrador::class, 'id_administrador', 'siape');
    }

    public function relator(): BelongsTo
    {
        return $this->belongsTo(UsuarioMembro::class, 'id_relator', 'siape');
    }

    public function etapas(): HasMany
    {
        return $this->hasMany(Etapa::class, 'numero_sei_processo', 'numero_sei');
    }

    public function getEtapaAtualAttribute()
    {
        return $this->etapas
            ->sortByDesc('ordem')
            ->first(fn ($etapa) => $etapa->status !== Etapa::STATUS_FINALIZADO)
            ?? $this->etapas->sortByDesc('ordem')->first();
    }
}

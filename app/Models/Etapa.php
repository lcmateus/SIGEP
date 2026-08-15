<?php

namespace App\Models;

use App\Enums\EtapaTipo;
use App\Enums\ProcessoStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Etapa extends Model
{
    use HasFactory;
    protected $attributes = [
        'status' => ProcessoStatus::EmElaboracao->value,
    ];

    protected $fillable = [
        'tipo',
        'status',
        'processo_id',
    ];

    protected function casts(): array
    {
        return [
            'tipo' => EtapaTipo::class,
            'status' => ProcessoStatus::class,
        ];
    }

    public function processo(): BelongsTo
    {
        return $this->belongsTo(Processo::class);
    }

    public function rodadasVotacao(): HasMany
    {
        return $this->hasMany(RodadaVotacao::class);
    }

    public function documentos(): HasMany
    {
        return $this->hasMany(Documento::class);
    }
}

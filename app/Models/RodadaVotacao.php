<?php

namespace App\Models;

use App\Enums\ResultadoVotacao;
use App\Enums\UserStatus;
use App\Enums\UserTipo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RodadaVotacao extends Model
{
    use HasFactory;
    protected $table = 'rodadas_votacao';

    protected $attributes = [
        'resultado' => ResultadoVotacao::Pendente->value,
    ];

    protected $fillable = [
        'data_abertura',
        'data_encerramento',
        'resultado',
        'etapa_id',
        'presidente_id',
    ];

    protected static function booted(): void
    {
        static::creating(function (RodadaVotacao $rodada): void {
            if ($rodada->presidente_id !== null) {
                return;
            }

            $rodada->presidente_id = User::query()
                ->where('tipo', UserTipo::Membro)
                ->where('status', UserStatus::Ativo)
                ->inRandomOrder()
                ->value('id');
        });
    }

    protected function casts(): array
    {
        return [
            'data_abertura' => 'datetime',
            'data_encerramento' => 'datetime',
            'resultado' => ResultadoVotacao::class,
        ];
    }

    public function etapa(): BelongsTo
    {
        return $this->belongsTo(Etapa::class);
    }

    public function presidente(): BelongsTo
    {
        return $this->belongsTo(User::class, 'presidente_id');
    }

    public function votos(): HasMany
    {
        return $this->hasMany(Voto::class, 'rodada_id');
    }
}

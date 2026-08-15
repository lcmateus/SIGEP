<?php

namespace App\Models;

use App\Enums\ProcessoStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Processo extends Model
{
    use HasFactory;

    protected $attributes = [
        'status' => ProcessoStatus::EmElaboracao->value,
    ];

    protected $fillable = [
        'numero_sei',
        'data_admissao',
        'data_devolucao',
        'status',
        'id_administrador',
        'id_relator',
    ];

    protected function casts(): array
    {
        return [
            'data_admissao' => 'date',
            'data_devolucao' => 'date',
            'status' => ProcessoStatus::class,
        ];
    }

    public function administrador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_administrador');
    }

    public function relator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_relator');
    }

    public function etapas(): HasMany
    {
        return $this->hasMany(Etapa::class);
    }
}

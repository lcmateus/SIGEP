<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Processo extends Model
{
    use HasFactory;

    protected $fillable = [
        'numero_SEI',
        'titulo',
        'descricao',
        'status',
        'data_inicio',
        'data_fim',
        'desfecho',
        'siape_relator',
        'insert_by',
        'pdf_SEI',
        'etapa_atual'
    ];

    protected function casts(): array
    {
        return [
            'data_inicio' => 'datetime',
            'data_fim' => 'datetime',
        ];
    }

    public function criador(): BelongsTo
    {
        return $this->belongsTo(UsuarioAdministrador::class, 'insert_by', 'siape');
    }

    public function relator(): BelongsTo
    {
        return $this->belongsTo(UsuarioMembro::class, 'siape_relator', 'siape');
    }

    public function etapas(): HasMany{
        return $this->hasMany(Etapa::class);
    }

    public function etapaAtual()
    {
        return $this->belongsTo(Etapa::class, 'etapa_atual_id');
    }

    

    public function getStatusDisplay()
    {
        return $this->etapaAtual?->status_display ?? 'Sem etapa';
    }

    public function getEtapaTipoDisplay()
    {
        return $this->etapaAtual?->tipo_display ?? 'Nenhuma';
    }
}

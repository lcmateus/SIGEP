<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Processo extends Model
{
    protected $table = 'processo';

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $keyType = 'integer';

    protected $fillable = [
        'titulo',
        'descricao',
        'status',
        'data_admissao',
        'data_devolucao',
        'desfecho',
        'siape_relator',
        'siape_administrador',
        'pdf_SEI',
        'etapa_atual'
    ];

    protected $casts = [
        'data_admissao' => 'date',
        'data_devolucao' => 'date',
    ];

    public function administrador(): BelongsTo
    {

        return $this->belongsTo(UsuarioAdministrador::class, 'siape_administrador', 'siape');
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


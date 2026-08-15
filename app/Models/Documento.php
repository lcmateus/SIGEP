<?php

namespace App\Models;

use App\Enums\DocumentoCondicao;
use App\Enums\DocumentoTipo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Documento extends Model
{
    use HasFactory;
    protected $attributes = [
        'tipo' => DocumentoTipo::Outro->value,
    ];

    protected $fillable = [
        'tipo',
        'condicao',
        'data_insercao',
        'anexo',
        'etapa_id',
    ];

    protected function casts(): array
    {
        return [
            'tipo' => DocumentoTipo::class,
            'condicao' => DocumentoCondicao::class,
            'data_insercao' => 'datetime',
        ];
    }

    public function etapa(): BelongsTo
    {
        return $this->belongsTo(Etapa::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Documento extends Model
{
    protected $table = 'documento';

    protected $primaryKey = 'id';

    public $incrementing = true;

    protected $keyType = 'integer';

    protected $fillable = [
        'titulo',
        'descricao',
        'caminho',
        'upload_feito_por',
        'data_upload',
        'etapa_id',
        'tipo',
    ];

    protected $casts = [
        'data_upload' => 'datetime',
    ];

    public function etapa(): BelongsTo
    {
        return $this->belongsTo(Etapa::class, 'etapa_id', 'id');
    }

    public function getUrlAttribute(): string
    {
        return asset($this->caminho);
    }

    public function arquivoExiste(): bool
    {
        return file_exists(public_path($this->caminho));
    }

    public function getNomeOriginalAttribute(): string
    {
        return $this->titulo . ($this->descricao ? '.' . $this->descricao : '');
    }

    public function getTipoDisplayAttribute(): string
    {
        return match ($this->tipo) {
            'pdf_sei' => 'PDF do SEI',
            'relatorio_juizo' => 'Relatório do Juízo',
            'relatorio_pp' => 'Relatório do PP',
            'minuta_acpp' => 'Minuta do ACPP',
            'versao_final_acpp' => 'Versão Final do ACPP',
            'versao_assinada_acpp' => 'Versão Assinada do ACPP',
            'diligencia' => 'Diligência',
            'prova_documental' => 'Prova Documental',
            'prova_testemunhal' => 'Prova Testemunhal',
            'prova_pericial' => 'Prova Pericial',
            'relatorio_parcial_pae' => 'Relatório Parcial do PAE',
            'defesa_investigado' => 'Defesa do Investigado',
            'alegacoes_finais' => 'Alegações Finais',
            'reconsideracao' => 'Reconsideração',
            'outro' => 'Outro',
            default => $this->tipo ?? 'Desconhecido',
        };
    }
}

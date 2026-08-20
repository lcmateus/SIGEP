<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Etapa extends Model
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $table = 'etapa';

    protected $primaryKey = 'id';

    public $incrementing = true;

    protected $keyType = 'integer';

    protected $fillable = [
        'ordem',
        'tipo',
        'status',
        'resultado',
        'relatorio_texto',
        'data_inicio',
        'data_envio_votacao',
        'data_encerramento',
    ];

    protected $casts = [
        'data_inicio' => 'datetime',
        'data_envio_votacao' => 'datetime',
        'data_encerramento' => 'datetime',
    ];

    // Relacionamentos
    public function processo()
    {
        return $this->belongsTo(Processo::class);
    }

    /*
    public function rodadaVotacao()
    {
        return $this->hasOne(RodadaVotacao::class);
    }
    */

    // Métodos auxiliares
    public function isEmElaboracao()
    {
        return $this->status === 'em_elaboracao';
    }

    public function isEmVotacao()
    {
        return $this->status === 'em_votacao';
    }

    public function isAguardandoMinerva()
    {
        return $this->status === 'aguardando_minerva';
    }

    public function isFinalizada()
    {
        return $this->status === 'finalizada';
    }

    public function getTipoDisplayAttribute()
    {
        return match($this->tipo) {
            'juizo' => 'Juízo de Admissibilidade',
            'procedimento_preliminar' => 'Procedimento Preliminar',
            'acpp' => 'ACPP',
            'pae' => 'PAE',
            default => $this->tipo
        };
    }

    public function getStatusDisplayAttribute()
    {
        return match($this->status) {
            'em_elaboracao' => 'Em elaboração',
            'em_votacao' => 'Em votação',
            'aguardando_minerva' => 'Aguardando Minerva',
            'finalizada' => 'Finalizada',
            default => $this->status
        };
    }

    public function getResultadoDisplayAttribute()
    {
        $map = [
            'aprovado' => 'Aprovado',
            'reprovado' => 'Reprovado',
            'propor_acpp' => 'Propor ACPP',
            'instaurar_pae' => 'Instaurar PAE',
            'arquivar' => 'Arquivar',
        ];
        return $map[$this->resultado] ?? $this->resultado;
    }
}
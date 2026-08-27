<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Etapa extends Model
{
    public const TIPO_JUIZO = 'Juízo de Admissibilidade';
    public const TIPO_PROCEDIMENTO_PRELIMINAR = 'Processo Preliminar';
    public const TIPO_ACPP = 'Acordo de Conduta';
    public const TIPO_PAE = 'Processo de Apuração';

    public const TIPOS = [
        self::TIPO_JUIZO,
        self::TIPO_PROCEDIMENTO_PRELIMINAR,
        self::TIPO_ACPP,
        self::TIPO_PAE,
    ];

    public const STATUS_EM_ELABORACAO = 'Em Elaboração';
    public const STATUS_EM_VOTACAO = 'Em Votação';
    public const STATUS_AGUARDANDO_MINERVA = 'Aguardando Minerva';
    public const STATUS_FINALIZADO = 'Finalizado';
    public const STATUS_DEVOLVIDO = 'Devolvido';
    public const STATUS_ARQUIVADO = 'Arquivado';

    public const STATUSES = [
        self::STATUS_EM_ELABORACAO,
        self::STATUS_EM_VOTACAO,
        self::STATUS_AGUARDANDO_MINERVA,
        self::STATUS_FINALIZADO,
        self::STATUS_DEVOLVIDO,
        self::STATUS_ARQUIVADO,
    ];

    protected $table = 'etapa';

    protected $primaryKey = 'id';

    public $incrementing = true;

    protected $keyType = 'integer';

    protected $fillable = [
        'numero_sei_processo',
        'ordem',
        'tipo',
        'status',
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
        return $this->belongsTo(Processo::class, 'numero_sei_processo', 'numero_sei');
    }

    // Métodos auxiliares
    public function isEmElaboracao()
    {
        return $this->status === self::STATUS_EM_ELABORACAO;
    }

    public function isEmVotacao()
    {
        return $this->status === self::STATUS_EM_VOTACAO;
    }

    public function isAguardandoMinerva()
    {
        return $this->status === self::STATUS_AGUARDANDO_MINERVA;
    }

    public function isFinalizada()
    {
        return $this->status === self::STATUS_FINALIZADO;
    }

    public function isDevolvido()
    {
        return $this->status === self::STATUS_DEVOLVIDO;
    }

    public function isArquivado()
    {
        return $this->status === self::STATUS_ARQUIVADO;
    }

    public function getTipoDisplayAttribute()
    {
        return $this->tipo;
    }

    public function getStatusDisplayAttribute()
    {
        return $this->status ?? 'Sem status';
    }
}

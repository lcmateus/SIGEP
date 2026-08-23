<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RodadaVotacao extends Model
{
    protected $table = 'rodada_votacao';

    public $timestamps = false;

    protected $primaryKey = 'id';

    public $incrementing = true;

    protected $keyType = 'integer';

    protected $fillable = [
        'id_etapa',
        'status',
        'snapshot_votantes',
        'resultado',
        'data_abertura',
        'data_encerramento',
    ];

    protected $casts = [
        'snapshot_votantes' => 'array',
        'data_abertura' => 'datetime',
        'data_encerramento' => 'datetime',
    ];

    // Revisar métodos e relacionamentos

    // Relacionamentos
    public function etapa()
    {
        return $this->belongsTo(Etapa::class, 'id_etapa', 'id');
    }

    public function votos()
    {
        return $this->hasMany(Voto::class, 'rodada_votacao_id');
    }

    // Métodos auxiliares
    public function isAberta()
    {
        return $this->status === 'aberta';
    }

    public function isEncerrada()
    {
        return $this->status === 'encerrada';
    }

    public function getVotosComRelator()
    {
        return $this->votos()->where('tipo', 'com_relator')->count();
    }

    public function getVotosDesaprova()
    {
        return $this->votos()->where('tipo', 'desaprova')->count();
    }

    public function getVotosRessalva()
    {
        return $this->votos()->where('tipo', 'ressalva')->count();
    }

    public function hasEmpate()
    {
        return $this->getVotosComRelator() == $this->getVotosDesaprova();
    }

    public function podeVotar($siape)
    {
        $snapshot = $this->snapshot_votantes ?? [];
        return in_array($siape, $snapshot);
    }

    public function jaVotou($siape)
    {
        return $this->votos()->where('usuario_id', $siape)->exists();
    }
}
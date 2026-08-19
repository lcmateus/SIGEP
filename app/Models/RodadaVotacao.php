<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RodadaVotacao extends Model
{
    protected $table = 'rodadas_votacao';

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
        return $this->belongsTo(Etapa::class);
    }

    public function votos()
    {
        return $this->hasMany(Voto::class, 'rodada_id');
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

    public function podeVotar(User $user)
    {
        $snapshot = $this->snapshot_votantes ?? [];
        return in_array($user->siape, $snapshot);
    }

    public function jaVotou(User $user)
    {
        return $this->votos()->where('usuario_id', $user->siape)->exists();
    }
}
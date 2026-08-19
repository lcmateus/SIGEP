<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class UsuarioMembro extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $table = 'usuario_membro';

    protected $fillable = [
        'nome',
        'email',
        'siape',
        'password',
        'data_ativacao',
        'ativado_por',
        'is_presidente'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    //Casts

    protected $casts = [
        'data_ativacao' => 'datetime'
    ];

    //Relacionamentos

    public function ativadoPor(){
        return $this->belongsTo(UsuarioAdministrador::class, 'siape');
    }
    
    public function processosComoRelator()
    {
        return $this->hasMany(Processo::class, 'relator_id', 'siape');
    }

    public function votos()
    {
        return $this->hasMany(Voto::class, 'usuario_id', 'siape');
    }

    public function rodadasVotacao()
    {
        return $this->belongsToMany(RodadaVotacao::class, 'rodada_votacao_membros', 'membro_id', 'rodada_id');
    }

    // Escopos locais

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isTitular()
    {
        return $this->tipo_membro === 'titular' || $this->tipo_membro === 'presidente';
    }

    public function isPresidente()
    {
        return $this->tipo_membro === 'presidente';
    }

    public function isAtivo()
    {
        return $this->status === 'ativo';
    }


}

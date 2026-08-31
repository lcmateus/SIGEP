<?php

namespace App\Models;

use App\Support\Concerns\EncryptsRouteKey;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class UsuarioMembro extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, EncryptsRouteKey, Notifiable;

    protected $table = 'usuario_membro';

    protected $primaryKey = 'siape';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'siape',
        'nome',
        'email',
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
        return $this->belongsTo(UsuarioAdministrador::class, 'ativado_por', 'siape');
    }
    
    public function processosComoRelator()
    {
        return $this->hasMany(Processo::class, 'id_relator', 'siape');
    }

    public function votos()
    {
        return $this->hasMany(Voto::class, 'id_membro', 'siape');
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

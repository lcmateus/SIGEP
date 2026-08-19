<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class UsuarioAdministrador extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $table = 'usuario_administrador';

    protected $fillable = [
        'siape',
        'nome',
        'email',
        'password'
    ];

    protected $hidden = [
        'password'
    ];

    //Relacionamentos

    public function ativacao(){
        return $this->hasMany(UsuarioMembro::class, 'siape', 'siape');
    }

    public function inserirProcesso(){
        return $this->hasMany(Processo::class, 'insert_by', 'siape');
    }


    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
}

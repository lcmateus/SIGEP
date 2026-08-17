<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */

}

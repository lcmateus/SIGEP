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
    
    public function processosComoRelator()
    {
        return $this->hasMany(Processo::class, 'id_relator', 'siape');
    }

    // Escopos locais

    public function isAtivo()
    {
        return $this->data_ativacao !== null;
    }


}

<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'siape',
        'password',
        'role',
        'status',
        'tipo_membro',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function processosCriados(): HasMany
    {
        return $this->hasMany(Processo::class, 'created_by');
    }

    public function votos(): HasMany
    {
        return $this->hasMany(Voto::class, 'usuario_id');
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isAtivo(): bool
    {
        return $this->status === 'ativo';
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}

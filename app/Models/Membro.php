<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\UserStatus;
use App\Enums\UserTipo;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'siape',
        'nome',
        'email',
        'password',
        'tipo',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function processosAdministrados(): HasMany
    {
        return $this->hasMany(Processo::class, 'id_administrador');
    }

    public function processosRelatados(): HasMany
    {
        return $this->hasMany(Processo::class, 'id_relator');
    }

    public function votos(): HasMany
    {
        return $this->hasMany(Voto::class, 'membro_id');
    }

    public function rodadasPresididas(): HasMany
    {
        return $this->hasMany(RodadaVotacao::class, 'presidente_id');
    }

    public function notificacoes(): HasMany
    {
        return $this->hasMany(Notificacao::class);
    }

    public function relatoriosSistema(): HasMany
    {
        return $this->hasMany(RelatorioSistema::class);
    }

    public function isAdmin(): bool
    {
        return $this->tipo === UserTipo::Admin;
    }

    public function isMembro(): bool
    {
        return $this->tipo === UserTipo::Membro;
    }

    public function isAtivo(): bool
    {
        return $this->status === UserStatus::Ativo;
    }

    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => $attributes['nome'] ?? null,
            set: fn (string $value) => ['nome' => $value],
        );
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
            'tipo' => UserTipo::class,
            'status' => UserStatus::class,
        ];
    }
}

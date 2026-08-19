<?php

namespace App\Auth;

use App\Models\UsuarioAdministrador;
use App\Models\UsuarioMembro;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Support\Facades\Hash;

class DualUserProvider implements UserProvider
{
    public function retrieveById($identifier): ?Authenticatable
    {
        return UsuarioMembro::find($identifier) ?? UsuarioAdministrador::find($identifier);
    }

    public function retrieveByToken($identifier, $token): ?Authenticatable
    {
        $membro = UsuarioMembro::where('siape', $identifier)
            ->where('remember_token', $token)
            ->first();

        return $membro
            ?? UsuarioAdministrador::where('siape', $identifier)
                ->where('remember_token', $token)
                ->first();
    }

    public function updateRememberToken(Authenticatable $user, $token): void
    {
        $user->setRememberToken($token);
        $user->save();
    }

    public function retrieveByCredentials(array $credentials): ?Authenticatable
    {
        $siape = $credentials['siape'] ?? null;

        return UsuarioMembro::where('siape', $siape)->first()
            ?? UsuarioAdministrador::where('siape', $siape)->first();
    }

    public function validateCredentials(Authenticatable $user, array $credentials): bool
    {
        return Hash::check($credentials['password'], $user->getAuthPassword());
    }

    public function rehashPasswordIfRequired(Authenticatable $user, array $credentials, bool $force = false): void
    {
        if (! $force && ! Hash::needsRehash($user->getAuthPassword())) {
            return;
        }

        $user->forceFill([
            'password' => Hash::make($credentials['password']),
        ])->save();
    }
}
<?php

namespace Database\Factories;

use App\Enums\UserStatus;
use App\Enums\UserTipo;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'siape' => fake()->unique()->numerify('#######'),
            'nome' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'tipo' => UserTipo::Membro,
            'status' => UserStatus::Ativo,
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'tipo' => UserTipo::Admin,
            'status' => UserStatus::Ativo,
        ]);
    }

    public function pendente(): static
    {
        return $this->state(fn (array $attributes) => [
            'tipo' => UserTipo::Membro,
            'status' => UserStatus::Pendente,
        ]);
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}

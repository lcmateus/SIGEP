<?php

namespace Database\Factories;

use App\Enums\VotoOpcao;
use App\Models\RodadaVotacao;
use App\Models\User;
use App\Models\Voto;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Voto>
 */
class VotoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'opcao' => fake()->randomElement(VotoOpcao::cases()),
            'justificativa' => fake()->optional()->sentence(),
            'is_minerva' => false,
            'membro_id' => User::factory(),
            'rodada_id' => RodadaVotacao::factory(),
        ];
    }
}

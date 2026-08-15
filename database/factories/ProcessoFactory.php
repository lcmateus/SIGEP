<?php

namespace Database\Factories;

use App\Enums\ProcessoStatus;
use App\Models\Processo;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Processo>
 */
class ProcessoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'numero_sei' => fake()->unique()->numerify('23172.######/2026-##'),
            'data_admissao' => null,
            'data_devolucao' => null,
            'status' => ProcessoStatus::EmElaboracao,
            'id_administrador' => User::factory()->admin(),
            'id_relator' => null,
        ];
    }
}

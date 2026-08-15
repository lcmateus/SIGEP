<?php

namespace Database\Factories;

use App\Enums\EtapaTipo;
use App\Enums\ProcessoStatus;
use App\Models\Etapa;
use App\Models\Processo;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Etapa>
 */
class EtapaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'tipo' => fake()->randomElement(EtapaTipo::cases()),
            'status' => ProcessoStatus::EmElaboracao,
            'processo_id' => Processo::factory(),
        ];
    }
}

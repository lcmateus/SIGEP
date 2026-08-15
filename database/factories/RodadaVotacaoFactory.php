<?php

namespace Database\Factories;

use App\Enums\ResultadoVotacao;
use App\Models\Etapa;
use App\Models\RodadaVotacao;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RodadaVotacao>
 */
class RodadaVotacaoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'data_abertura' => now(),
            'data_encerramento' => null,
            'resultado' => ResultadoVotacao::Pendente,
            'etapa_id' => Etapa::factory(),
        ];
    }
}

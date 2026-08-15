<?php

namespace Database\Factories;

use App\Enums\DocumentoCondicao;
use App\Enums\DocumentoTipo;
use App\Models\Documento;
use App\Models\Etapa;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Documento>
 */
class DocumentoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'tipo' => fake()->randomElement(DocumentoTipo::cases()),
            'condicao' => fake()->optional(0.7)->randomElement(DocumentoCondicao::cases()),
            'data_insercao' => now(),
            'anexo' => 'documentos/' . fake()->uuid . '.pdf',
            'etapa_id' => Etapa::factory(),
        ];
    }
}

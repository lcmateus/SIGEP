<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const TIPOS_COM_MEDIACAO = [
        'Juízo de Admissibilidade',
        'Procedimento Preliminar',
        'Acordo de Conduta',
        'Processo de Apuração',
        'Mediação',
    ];

    private const TIPOS_SEM_MEDIACAO = [
        'Juízo de Admissibilidade',
        'Procedimento Preliminar',
        'Acordo de Conduta',
        'Processo de Apuração',
    ];

    public function up(): void
    {
        Schema::table('etapa', function (Blueprint $table) {
            $table->enum('tipo', self::TIPOS_COM_MEDIACAO)
                ->default('Juízo de Admissibilidade')
                ->change();
        });
    }

    public function down(): void
    {
        if (DB::table('etapa')->where('tipo', 'Mediação')->exists()) {
            throw new RuntimeException('Nao e possivel reverter a migration: existem etapas de Mediacao cadastradas.');
        }

        Schema::table('etapa', function (Blueprint $table) {
            $table->enum('tipo', self::TIPOS_SEM_MEDIACAO)
                ->default('Juízo de Admissibilidade')
                ->change();
        });
    }
};

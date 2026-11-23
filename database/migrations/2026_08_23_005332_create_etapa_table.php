<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('etapa', function (Blueprint $table) {
            $table->id();
            $table->string('numero_sei_processo')->nullable();
            $table->integer('ordem')->default(1);
            $table->enum('tipo', [
                'Juízo de Admissibilidade',
                'Procedimento Preliminar',
                'Acordo de Conduta',
                'Processo de Apuração',
            ])->default('Juízo de Admissibilidade');
            $table->enum('status', [
                'Em Elaboração',
                'Em Votação',
                'Aguardando Minerva',
                'Devolvido',
                'Arquivado',
                'Finalizado',
            ])->default('Em Elaboração');
            $table->longText('relatorio_texto')->nullable();
            $table->timestamp('data_inicio')->useCurrent();
            $table->timestamp('data_envio_votacao')->nullable();
            $table->timestamp('data_encerramento')->nullable();
            $table->timestamps();

            $table->foreign('numero_sei_processo')
                  ->references('numero_sei')
                  ->on('processo')
                  ->cascadeOnUpdate()
                  ->cascadeOnDelete();

            $table->unique(['numero_sei_processo', 'ordem']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('etapa');
    }
};

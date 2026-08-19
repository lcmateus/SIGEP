<?php
// database/migrations/YYYY_MM_DD_create_rodadas_votacao_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rodada_votacao', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->timestamps('data_abertura')->useCurrent();
            $table->timestamps('data_encerramento')->nullable();
            $table->integer('id_etapa')->nullable();
            
            $table->enum('status', [
                'aberta',
                'encerrada',
                'cancelada'
            ])->default('aberta');
            
            $table->enum('resultado', [ //pensar em melhores possibilidades de resultado.
                'aprovado',             // Acho que tem algumas aqui que não serão necessárias
                'reprovado',
                'propor_acpp',
                'instaurar_pae',
                'arquivar',
                'cumprido',
                'descumprido',
                'pune_censura',
                'pune_suspensao',
                'pune_demissao',
                'absolvido'
            ])->nullable();

            $table->json('snapshot_votantes')->nullable();
            
            $table->foreign('id_etapa')
                  ->references('id')
                  ->on('etapa')
                  ->cascadeOnUpdate()
                  ->cascadeOnDelete();
            
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rodadas_votacao');
    }
};
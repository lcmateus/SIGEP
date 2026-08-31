<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rodada_votacao', function (Blueprint $table) {
            $table->id();
            $table->timestamp('data_abertura')->useCurrent();
            $table->timestamp('data_encerramento')->nullable();

            $table->enum('resultado', [
                'aprovado',
                'reprovado',
            ])->nullable();

            $table->unsignedBigInteger('id_etapa')->nullable();

            $table->foreign('id_etapa')
                  ->references('id')
                  ->on('etapa')
                  ->cascadeOnUpdate()
                  ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rodada_votacao');
    }
};

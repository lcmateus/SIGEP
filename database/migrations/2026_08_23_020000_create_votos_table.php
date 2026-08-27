<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('voto', function (Blueprint $table) {
            $table->integer('id');
            $table->string('usuario_id',7)->nullable();
            $table->integer('rodada_votacao_id')->nullable();
            $table->string('tipo');
            $table->text('justificativa')->nullable();
            $table->timestamps();

            $table->foreign('usuario_id')
                  ->references('siape')
                  ->on('usuario_membro')
                  ->cascadeOnDelete();

            $table->foreign('rodada_votacao_id')
                  ->references('id')
                  ->on('rodada_votacao')
                  ->cascadeOnDelete();

            

            $table->unique(['usuario_id', 'rodada_votacao_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('voto');
    }
};

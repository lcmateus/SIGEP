<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('votos', function (Blueprint $table) {
            $table->id();
            $table->string('usuario_id');
            $table->foreign('usuario_id')->references('siape')->on('usuario_membro')->cascadeOnDelete();
            $table->foreignId('processo_id')->constrained('processos')->cascadeOnDelete();
            $table->string('tipo');
            $table->text('justificativa')->nullable();
            $table->timestamps();

            $table->unique(['usuario_id', 'processo_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('votos');
    }
};

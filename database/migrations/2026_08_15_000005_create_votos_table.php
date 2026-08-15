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
            $table->string('opcao');
            $table->text('justificativa')->nullable();
            $table->boolean('is_minerva')->default(false);
            $table->foreignId('membro_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('rodada_id')->constrained('rodadas_votacao')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['membro_id', 'rodada_id', 'is_minerva']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('votos');
    }
};

<?php

use App\Enums\ResultadoVotacao;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rodadas_votacao', function (Blueprint $table) {
            $table->id();
            $table->dateTime('data_abertura')->nullable();
            $table->dateTime('data_encerramento')->nullable();
            $table->string('resultado')->default(ResultadoVotacao::Pendente->value);
            $table->foreignId('etapa_id')->constrained('etapas')->cascadeOnDelete();
            $table->foreignId('presidente_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rodadas_votacao');
    }
};

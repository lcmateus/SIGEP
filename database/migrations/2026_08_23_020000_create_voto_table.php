<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('voto', function (Blueprint $table) {
            $table->id();
            $table->enum('opcao', [
                'aprova',
                'desaprova',
                'aprova com resalva',
                'abstenho',
            ]);
            $table->text('justificativa')->nullable();
            $table->boolean('is_minerva')->default(false);
            $table->string('id_membro', 7)->nullable();
            $table->unsignedBigInteger('id_rodada')->nullable();

            $table->foreign('id_membro')
                  ->references('siape')
                  ->on('usuario_membro')
                  ->cascadeOnUpdate()
                  ->cascadeOnDelete();

            $table->foreign('id_rodada')
                  ->references('id')
                  ->on('rodada_votacao')
                  ->cascadeOnUpdate()
                  ->cascadeOnDelete();

            $table->unique(['id_membro', 'id_rodada']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('voto');
    }
};

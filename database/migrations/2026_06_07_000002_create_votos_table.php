<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('votos', function (Blueprint $table) {
            $table->integer('id');
            $table->string('usuario_id',7)->nullable();
            $table->integer('processo_id')->nullable();
            $table->string('tipo');
            $table->text('justificativa')->nullable();
            $table->timestamps();

            $table->foreign('usuario_id')
                  ->references('siape')
                  ->on('membro')
                  ->cascadeOnDelete();

            $table->foreign('processo_id')
                  ->references('numero_SEI')
                  ->on('processo')
                  ->cascadeOnDelete();

            

            $table->unique(['usuario_id', 'processo_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('votos');
    }
};

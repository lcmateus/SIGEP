<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::dropIfExists('etapa');

        Schema::enableForeignKeyConstraints();

        Schema::create('etapa', function (Blueprint $table) {
            $table->id();
            $table->string('numero_sei_processo')->nullable();
            $table->integer('ordem')->default(1);
            $table->string('tipo');
            $table->string('status')->nullable();
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
        Schema::disableForeignKeyConstraints();

        Schema::dropIfExists('etapa');

        Schema::enableForeignKeyConstraints();

        Schema::create('etapa', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->string('numero_sei_processo')->nullable();
            $table->integer('ordem')->default(1);
            $table->enum('tipo', ['juizo', 'procedimento_preliminar', 'acpp', 'pae'])->default('juizo');
            $table->enum('status', ['em_elaboracao', 'em_votacao', 'aguardando_minerva', 'devolvido', 'arquivado'])->default('em_elaboracao');
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
};

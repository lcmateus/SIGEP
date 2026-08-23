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
        Schema::dropIfExists('processo');

        Schema::enableForeignKeyConstraints();

        Schema::create('processo', function (Blueprint $table) {
            $table->string('numero_sei')->primary();
            $table->date('data_admissao');
            $table->date('data_devolucao')->nullable();
            $table->string('id_administrador', 7)->nullable();
            $table->string('id_relator', 7)->nullable();
            $table->timestamps();

            $table->foreign('id_administrador')
                  ->references('siape')
                  ->on('usuario_administrador')
                  ->cascadeOnUpdate()
                  ->nullOnDelete();

            $table->foreign('id_relator')
                  ->references('siape')
                  ->on('usuario_membro')
                  ->cascadeOnUpdate()
                  ->nullOnDelete();
        });

        Schema::create('etapa', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->string('numero_sei_processo')->nullable();
            $table->integer('ordem')->default(1);
            $table->enum('tipo', [
                'juizo',
                'procedimento_preliminar',
                'acpp',
                'pae'
            ])->default('juizo');
            $table->enum('status', [
                'em_elaboracao',
                'em_votacao',
                'aguardando_minerva',
                'finalizada'
            ])->default('em_elaboracao');

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
        Schema::dropIfExists('processo');

        Schema::enableForeignKeyConstraints();

        Schema::create('processo', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->string('titulo');
            $table->text('descricao');
            $table->string('status')->default('ativa');
            $table->dateTime('data_inicio')->nullable();
            $table->dateTime('data_fim')->nullable();
            $table->string('desfecho')->nullable();
            $table->string('siape_relator', 7);
            $table->string('insert_by', 7);
            $table->timestamps();

            $table->foreign('siape_relator')
                  ->references('siape')
                  ->on('usuario_membro')
                  ->cascadeOnUpdate()
                  ->cascadeOnDelete();

            $table->foreign('insert_by')
                  ->references('siape')
                  ->on('usuario_administrador')
                  ->cascadeOnUpdate()
                  ->cascadeOnDelete();
        });

        Schema::create('etapa', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->integer('processo_id')->nullable();
            $table->integer('ordem')->default(1);
            $table->enum('tipo', ['juizo', 'procedimento_preliminar', 'acpp', 'pae'])->default('juizo');
            $table->enum('status', ['em_elaboracao', 'em_votacao', 'aguardando_minerva', 'finalizada'])->default('em_elaboracao');
            $table->longText('relatorio_texto')->nullable();
            $table->timestamp('data_inicio')->useCurrent();
            $table->timestamp('data_envio_votacao')->nullable();
            $table->timestamp('data_encerramento')->nullable();
            $table->timestamps();

            $table->foreign('processo_id')
                  ->references('id')
                  ->on('processo')
                  ->cascadeOnUpdate()
                  ->cascadeOnDelete();

            $table->unique(['processo_id', 'ordem']);
        });
    }
};

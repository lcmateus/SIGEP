<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('etapa', function (Blueprint $table) {
            $table->id();
            $table->integer('processo_id')->nullable();
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
            
            $table->foreign('processo_id')
                  ->references('id')
                  ->on('processo')
                  ->updateOnCascade()
                  ->deleteOnCascade();

            $table->unique(['processo_id', 'ordem']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('etapa');
    }
};
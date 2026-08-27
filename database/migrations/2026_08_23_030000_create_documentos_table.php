<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documento', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->string('descricao')->nullable();
            $table->string('caminho', 255);
            $table->string('upload_feito_por', 7)->nullable();
            $table->timestamp('data_upload')->useCurrent();

            $table->unsignedBigInteger('etapa_id');
            $table->foreign('etapa_id')
                  ->references('id')
                  ->on('etapa')
                  ->cascadeOnDelete();

            $table->enum('tipo', [
                'pdf_sei',
                'relatorio_juizo',
                'relatorio_pp',
                'minuta_acpp',
                'versao_final_acpp',
                'versao_assinada_acpp',
                'diligencia',
                'prova_documental',
                'prova_testemunhal',
                'prova_pericial',
                'relatorio_parcial_pae',
                'defesa_investigado',
                'alegacoes_finais',
                'reconsideracao',
                'outro',
            ])->default('outro');

            $table->timestamps();

            $table->index('tipo');
            $table->index('upload_feito_por');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documento');
    }
};

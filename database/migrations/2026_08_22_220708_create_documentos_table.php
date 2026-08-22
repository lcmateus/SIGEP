<?php
// database/migrations/2025_06_20_110000_create_documentos_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documento', function (Blueprint $table) {

            $table->integer('id')->primary();
            $table->string('titulo', 255);
            $table->text('descricao')->nullable();
            $table->string('caminho', 255);
            $table->string('upload_feito_por', 7)->nullable(); // SIAPE
            $table->timestamp('data_upload')->useCurrent();

            // Relacionamento polimórfico (aponta para processo ou etapa)
            $table->enum('referencia_tipo', ['processo', 'etapa'])->default('processo');
            $table->unsignedBigInteger('referencia_id');

            // Tipo do documento (categoria)
            $table->enum('tipo', [
                'pdf_sei',           // Documento original do SEI (já no processo)
                'relatorio_juizo',   // Relatório do Juízo (relator escreve)
                'relatorio_pp',      // Relatório do PP (relator escreve)
                'minuta_acpp',       // Minuta do ACPP
                'versao_final_acpp', // Versão final do ACPP
                'versao_assinada_acpp', // Versão assinada do ACPP
                'diligencia',        // Diligência do PAE
                'prova_documental',  // Prova documental
                'prova_testemunhal', // Prova testemunhal
                'prova_pericial',    // Prova pericial
                'relatorio_parcial_pae', // Relatório parcial do PAE
                'defesa_investigado', // Defesa do investigado
                'alegacoes_finais',  // Alegações finais
                'reconsideracao',    // Pedido de reconsideração
                'outro'
            ])->default('outro');

            $table->timestamps();

            // Índices
            $table->index(['referencia_tipo', 'referencia_id']);
            $table->index('tipo');
            $table->index('upload_feito_por');

            // Chave estrangeira
            $table->foreign('upload_feito_por')
                  ->references('siape')
                  ->on('usuario_administrador')
                  ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documentos');
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('processos', function (Blueprint $table) {
            $table->string('numero_sei')->primary();
            $table->date('data_admissao')->nullable();
            $table->date('data_devolucao')->nullable();
            $table->string('id_administrador')->nullable();
            $table->foreign('id_administrador')->references('siape')->on('usuario_administrador')->onDelete('set null');
            $table->string('id_relator')->nullable();
            $table->foreign('id_relator')->references('siape')->on('usuario_membro')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('processos');
    }
};
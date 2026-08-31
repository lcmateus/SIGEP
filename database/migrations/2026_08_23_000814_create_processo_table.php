<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
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
    }

    public function down(): void
    {
        Schema::dropIfExists('processo');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('membro', function (Blueprint $table) {
            
            $table->string('siape', 7)->primary();
           
            $table->timestamp('data_ativacao')->nullable();
            $table->string('ativado_por',7)->nullable();
            $table->boolean('is_presidente')->default(false);

            $table->foreign('siape')
                  ->references('siape')
                  ->on('usuario')
                  ->cascadeOnUpdate()
                  ->cascadeOnDelete();

            $table->foreign('ativado_por')
                  ->references('siape')
                  ->on('administrador')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('membro');
    }
};
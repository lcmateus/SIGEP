<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('processo', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->string('titulo');
            $table->text('descricao');
            $table->string('status')->default('ativa');
            $table->dateTime('data_inicio')->nullable();
            $table->dateTime('data_fim')->nullable();
            $table->string('desfecho')->nullable();
            $table->string('siape_relator',7);
            $table->string('insert_by',7);
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
    }

    public function down(): void
    {
        Schema::dropIfExists('processo');
    }
};

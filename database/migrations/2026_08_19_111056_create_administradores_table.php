<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('administrador', function (Blueprint $table) {
            
            $table->string('siape', 7)->primary();

            $table->foreign('siape')
                  ->references('siape')
                  ->on('usuario')
                  ->cascadeOnUpdate()
                  ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('administrador');
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usuario_membro', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('email')->unique();
            $table->string('siape')->unique();
            $table->string('password');
            $table->timestamp('data_ativacao')->nullable();
            $table->foreignId('ativado_por')->nullable()->constrained('usuario_administradors')->onDelete('set null');
            $table->boolean('is_presidente')->default(false);
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuario_membro');
    }
};
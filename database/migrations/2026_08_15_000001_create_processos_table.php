<?php

use App\Enums\ProcessoStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('processos', function (Blueprint $table) {
            $table->id();
            $table->string('numero_sei')->unique();
            $table->date('data_admissao')->nullable();
            $table->date('data_devolucao')->nullable();
            $table->string('status')->default(ProcessoStatus::EmElaboracao->value);
            $table->foreignId('id_administrador')->constrained('users')->restrictOnDelete();
            $table->foreignId('id_relator')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('processos');
    }
};

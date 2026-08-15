<?php

use App\Enums\DocumentoTipo;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documentos', function (Blueprint $table) {
            $table->id();
            $table->string('tipo')->default(DocumentoTipo::Outro->value);
            $table->dateTime('data_insercao')->nullable();
            $table->string('anexo');
            $table->foreignId('etapa_id')->constrained('etapas')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documentos');
    }
};

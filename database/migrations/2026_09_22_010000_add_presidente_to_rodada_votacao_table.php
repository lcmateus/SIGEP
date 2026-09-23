<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rodada_votacao', function (Blueprint $table) {
            $table->string('id_presidente', 7)->nullable()->after('id_etapa');

            $table->foreign('id_presidente')
                ->references('siape')
                ->on('usuario_membro')
                ->cascadeOnUpdate()
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('rodada_votacao', function (Blueprint $table) {
            $table->dropForeign(['id_presidente']);
            $table->dropColumn('id_presidente');
        });
    }
};
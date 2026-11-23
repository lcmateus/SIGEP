<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $presidenteSiape = DB::table('usuario_membro')
            ->where('is_presidente', true)
            ->whereNotNull('data_ativacao')
            ->value('siape');

        if ($presidenteSiape !== null) {
            DB::table('rodada_votacao')
                ->whereNull('resultado')
                ->whereNull('id_presidente')
                ->update(['id_presidente' => $presidenteSiape]);
        }
    }

    public function down(): void
    {
        // The assigned president is valid data and should not be removed on rollback.
    }
};

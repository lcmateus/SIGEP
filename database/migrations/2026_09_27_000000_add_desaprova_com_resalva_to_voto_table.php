<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('voto', function (Blueprint $table) {
            $table->enum('opcao', [
                'aprova',
                'desaprova',
                'aprova com resalva',
                'desaprova com resalva',
                'abstenho',
            ])->change();
        });
    }

    public function down(): void
    {
        Schema::table('voto', function (Blueprint $table) {
            $table->enum('opcao', [
                'aprova',
                'desaprova',
                'aprova com resalva',
                'abstenho',
            ])->change();
        });
    }
};
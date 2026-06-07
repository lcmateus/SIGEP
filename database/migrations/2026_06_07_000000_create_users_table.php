<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('users')) {
            if (! Schema::hasColumn('users', 'siape')) {
                Schema::table('users', function (Blueprint $table) {
                    $table->string('siape')->nullable()->unique();
                });
            }

            if (! Schema::hasColumn('users', 'role')) {
                Schema::table('users', function (Blueprint $table) {
                    $table->string('role')->default('membro');
                });
            }

            if (! Schema::hasColumn('users', 'status')) {
                Schema::table('users', function (Blueprint $table) {
                    $table->string('status')->default('pendente');
                });
            }

            if (! Schema::hasColumn('users', 'tipo_membro')) {
                Schema::table('users', function (Blueprint $table) {
                    $table->string('tipo_membro')->nullable();
                });
            }

            return;
        }

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('siape')->unique();
            $table->string('password');
            $table->string('role')->default('membro');
            $table->string('status')->default('pendente');
            $table->string('tipo_membro')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};

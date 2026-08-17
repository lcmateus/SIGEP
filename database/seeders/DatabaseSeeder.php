<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::query()->firstOrCreate([
            'email' => 'secretario@ifpi.edu.br',
        ], [
            'name' => 'Secretario Geral',
            'siape' => '0000001',
            'password' => 'password',
            'role' => 'admin',
            'status' => 'ativo',
        ]);
    }
}

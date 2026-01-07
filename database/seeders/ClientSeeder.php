<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\{DB, Hash};

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        // امسح البيانات القديمة
        DB::table('clients')->truncate();

        // إضافة client نشط واحد
        DB::table('clients')->insert([
            'name' => 'M Mohamed',
            'email' => 'm@test.com',
            'password' => Hash::make('123123'),
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $clients = [];
        for ($i = 1; $i <= 9; $i++) {
            $clients[] = [
                'name' => fake()->name(),
                'email' => fake()->unique()->safeEmail(),
                'password' => Hash::make('123123'),
                'status' => 'inactive',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('clients')->insert($clients);
    }
}
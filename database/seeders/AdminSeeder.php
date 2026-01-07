<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\{DB,Hash};

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void {
        DB::table('admins')->insert([
            [
                'name' => 'Super Admin',
                'email' => 'admin@test.com',
                'password' => Hash::make('123123'),
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
        \App\Models\Admin::factory()->active()->create();
        \App\Models\Admin::factory()->count(9)->inactive()->create();
    }
}
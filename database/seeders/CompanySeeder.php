<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Company;
class CompanySeeder extends Seeder
{
    public function run(): void
    {
        Company::truncate();

        for ($i = 0; $i < 10; $i++) {
            Company::create([
                'name' => 'Company ' . fake()->company(),
                'email' => 'company' . $i . '@test.com',
                'phone' => '+20' . rand(1000000000, 9999999999),
                'address' => 'Address ' . $i,
                'website' => 'www.company' . $i . '.com',
                'status' => fake()->randomElement(['active', 'inactive']),
            ]);
        }
    }
}

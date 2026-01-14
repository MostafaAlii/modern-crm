<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Enums\Admin\{AdminStatus, AdminType};
use App\Models\Company;
class AdminFactory extends Factory {
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => Hash::make('password'),
            'status' => AdminStatus::IN_ACTIVE,
            'type' => AdminType::ADMIN,
            'company_id' => null,
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => AdminStatus::ACTIVE,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => AdminStatus::IN_ACTIVE,
        ]);
    }

    public function owner(): static
    {
        return $this->state(fn(array $attributes) => [
            'type' => 'owner',
        ]);
    }

    public function admin(): static
    {
        return $this->state(fn(array $attributes) => [
            'type' => 'admin',
        ]);
    }

    public function withActiveCompany(): static
    {
        return $this->state(fn(array $attributes) => [
            'company_id' => Company::where('status', 'active')->inRandomOrder()->first()?->id,
        ]);
    }
}

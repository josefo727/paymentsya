<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Client>
 */
class ClientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'contact_person' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'phone' => $this->faker->phoneNumber,
            'account' => $this->faker->userName,
            'store_name' => $this->faker->company,
            'vtex_domain' => $this->faker->url,
            'store_domain' => $this->faker->url,
            'is_production' => $this->faker->boolean(50),
        ];
    }
}

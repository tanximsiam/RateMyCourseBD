<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\University>
 */
class UniversityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            //
            'name'     => $this->faker->unique()->company . ' University',
            'location' => $this->faker->city,
            'domain'   => $this->faker->domainName,
            'website'  => $this->faker->url,
        ];
    }
}

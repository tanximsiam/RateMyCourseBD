<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\University;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Department>
 */
class DepartmentFactory extends Factory
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
            'university_id' => University::inRandomOrder()->first()->id,
            'name'          => $this->faker->randomElement(['Computer Science', 'Electrical Engineering', 'Business', 'Architecture']),
            'code'          => strtoupper($this->faker->unique()->lexify('???')),
        ];
    }
}

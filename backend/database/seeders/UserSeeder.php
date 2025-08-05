<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Department;
use App\Models\Student;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create one test user (fixed email)
        $test_user = User::factory()->create([
            'name'  => 'Tanzim Ahmmed',
            'email' => 'tanzim.ahmmed@g.bracu.ac.bd',
            'password' => Hash::make('stdpass'),
        ]);

        Student::create([
            'user_id'       => $test_user->id,
            'university_id' => 1,
            'department_id' => 1,
        ]);

        // Create one admin (fixed email)
        User::create([
            'name'     => 'Admin User',
            'email'    => 'admin@example.com',
            'password' => Hash::make('adminpass'),
            'role'     => 'admin',
        ]);
        //
        $faker = \Faker\Factory::create();

        $departments = Department::with('university')->get();

        foreach (range(1, 20) as $i) {
            $dept = $departments->random();
            $university = $dept->university;

            $localPart = Str::slug($faker->unique()->userName, '.');
            $domain = $university->domain ?: 'example.edu';

            $email = $localPart . '@' . $domain;

            $user = User::create([
                'name'     => $faker->name,
                'email'    => $email,
                'password' => Hash::make('password'),
                'role'     => 'student',
            ]);

            Student::create([
                'user_id'       => $user->id,
                'university_id' => $university->id,
                'department_id' => $dept->id,
            ]);
        }

    }
}

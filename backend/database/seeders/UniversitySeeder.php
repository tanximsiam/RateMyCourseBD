<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\University;

class UniversitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        University::factory()->create([
            'name'     => 'BRAC University',
            'location' => 'Dhaka',
            'domain'   => 'g.bracu.ac.bd',
            'website'  => 'https://www.bracu.ac.bd',
        ]);
        University::factory()->count(5)->create();
    }
}

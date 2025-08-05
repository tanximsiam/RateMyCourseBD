<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Department;
use App\Models\University;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $deptList = [
            ['Computer Science and Engineering', 'CSE'],
            ['Electrical and Electronic Engineering', 'EEE'],
            ['Business Administration', 'BBA'],
            ['Architecture', 'ARC'],
            ['English', 'ENG'],
            ['Marketing', 'MKT'],
            ['Biology', 'BIO'],
        ];;

        foreach (University::all() as $university) {
            foreach ($deptList as [$name, $code]) {
                Department::create([
                    'university_id' => $university->id,
                    'name' => $name,
                    'code' => $code,
                ]);
            }
        }
    }
}

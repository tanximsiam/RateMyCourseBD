<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Course;
use App\Models\Department;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $courseMap = [
            'CSE' => [
                ['Structured Programming', 'CSE110'],
                ['Data Structures', 'CSE220'],
                ['Algorithms', 'CSE230'],
                ['Operating Systems', 'CSE310'],
                ['Database Systems', 'CSE320'],
                ['Software Engineering', 'CSE340'],
            ],
            'EEE' => [
                ['Digital Logic Design', 'EEE210'],
                ['Microprocessors', 'EEE220'],
                ['Signals and Systems', 'EEE230'],
            ],
            'BBA' => [
                ['Principles of Management', 'BUS101'],
                ['Business Communication', 'BUS220'],
            ],
            'ARC' => [
                ['Architectural Design Basics', 'ARC101'],
                ['Building Materials and Construction', 'ARC110'],
            ],
            'ENG' => [
                ['English Composition', 'ENG100'],
                ['Academic Reading and Writing', 'ENG110'],
            ],
            'MKT' => [
                ['Marketing Principles', 'MKT101'],
                ['Brand Management', 'MKT202'],
            ],
            'BIO' => [
                ['Introduction to Biology', 'BIO101'],
                ['Genetics', 'BIO120'],
            ],
        ];


        $departments = Department::with('university')->get();

        foreach ($departments as $department) {
            $code = $department->code;

            if (!isset($courseMap[$code])) {
                continue;
            }

            foreach ($courseMap[$code] as [$title, $courseCode]) {
                Course::create([
                    'department_id' => $department->id,
                    'university_id' => $department->university_id,
                    'title'         => $title,
                    'code'          => $courseCode,
                    'credits'       => rand(2, 4),
                    'description'   => null,
                    'outline_pdf'   => null,
                ]);
            }
        }
    }
}

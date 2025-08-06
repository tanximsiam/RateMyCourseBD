<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Review;
use App\Models\Student;
use App\Models\University;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //

        $universities = University::with('courses')->get();


        foreach ($universities as $university) {
            $students = Student::where('university_id', $university->id)->get();
            $courses = $university->courses;

            foreach ($courses as $course) {
                foreach ($students as $student) {
                    if ($student->user_id === 2) continue;
                    Review::create([
                        'user_id' => $student->user_id,
                        'course_id' => $course->id,
                        'rating' => rand(3, 5),
                        'review_text' => 'Review by student from university ' . $university->name,
                        'tags' => collect(['easy', 'difficult', 'fun', 'boring', 'project-heavy', 'groupwork', 'well-structured', 'theory-based'])
                                ->random(rand(2, 8))
                                ->values()
                                ->all(),
                        'is_anonymous' => false,
                    ]);
                }
            }
        }
    }
}

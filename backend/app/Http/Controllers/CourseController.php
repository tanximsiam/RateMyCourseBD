<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use Illuminate\Validation\Rule;

class CourseController extends Controller
{
    //
    public function index()
    {
        return Course::with('department', 'university')->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'university_id' => ['required', 'exists:universities,id'],
            'department_id' => ['required', 'exists:departments,id'],
            'code'          => [
                'required', 'string',
                Rule::unique('courses')->where(fn ($q) =>
                    $q->where('university_id', $request->integer('university_id'))
                ),
            ],
            'title'         => ['required', 'string'],
            'credits'       => ['nullable', 'numeric', 'between:0,99.99'],
            'description'   => ['nullable', 'string'],
            'outline_pdf'   => ['nullable', 'file', 'mimetypes:application/pdf', 'max:10240'], // 10MB
        ]);

        $outlinePath = null;
        if ($request->hasFile('outline_pdf')) {
            $outlinePath = $request->file('outline_pdf')->store('outlines', 'public'); // storage/app/public/outlines/...
        }

        $course = Course::create([
            'university_id' => $data['university_id'],
            'department_id' => $data['department_id'],
            'code'          => $data['code'],
            'title'         => $data['title'],
            'credits'       => $data['credits'] ?? null,
            'description'   => $data['description'] ?? null,
            'outline_pdf'   => $outlinePath,
        ]);

        return response()->json([
            'message' => 'Course created.',
            'course'  => $course->loadMissing(['university:id,name', 'department:id,name']),
        ], 201);
    }
}

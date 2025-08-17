<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CourseOutline;

class CourseOutlineController extends Controller
{
    //
    public function store(Request $request, $courseId)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf|max:2048',
        ]);

        // Ensure user hasn't already uploaded an outline for this course
        $exists = CourseOutline::where('course_id', $courseId)
            ->where('user_id', $request->user()->id)
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'You have already uploaded an outline for this course.'], 409);
        }

        // Store file
        $path = $request->file('file')->store('outlines', 'public');

        // Create record
        $outline = CourseOutline::create([
            'course_id' => $courseId,
            'user_id' => $request->user()->id,
            'file_path' => $path,
            'approved' => true,
        ]);

        return response()->json([
            'message' => 'Outline uploaded successfully',
            'outline' => $outline
        ], 201);
    }

    /**
     * Fetch all approved outlines for a course
     */
    public function index($courseId)
    {
        $outlines = CourseOutline::where('course_id', $courseId)
            ->where('approved', true)
            ->latest()
            ->get();

        return response()->json($outlines);
    }
}

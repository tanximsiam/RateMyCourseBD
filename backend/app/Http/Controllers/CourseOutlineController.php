<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CourseOutline;

class CourseOutlineController extends Controller
{
    //
    public function store(Request $request, $courseId)
    {
        $user = $request->user()->role;
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

        if ($user=='admin') {
            $approved = true;
        } else {
            $approved = false;
        }

        // Create record
        $outline = CourseOutline::create([
            'course_id' => $courseId,
            'user_id' => $request->user()->id,
            'file_path' => $path,
            'approved' => $approved,
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

    public function showStatus($id)
    {
        $outline = CourseOutline::where('course_id', $id)
            ->first();

        if (!$outline) {
            return response()->json(['status' => 'not_uploaded'], 200);
        }

        if (!$outline->approved) {
            return response()->json(['status' => 'pending'], 200);
        }

        return response()->json([
            'status' => 'approved',
            'file_url' => asset('storage/' . $outline->file_path),
            'outline_id' => $outline->id,
            'obsolete'   => (bool) ($outline->obsolete ?? false),
        ], 200);
    }

    public function reportOld(Request $request, $id)
    {
        // Mark the CURRENT USER'S approved outline for this course as obsolete=true
        $outline = CourseOutline::where('course_id', $id)
            ->where('user_id', $request->user()->id)
            ->where('approved', true)
            ->first();

        if (!$outline) {
            return response()->json(['message' => 'No approved outline found for this user/course.'], 404);
        }

        $outline->obsolete = true;
        $outline->save();

        return response()->json(['message' => 'Outline marked as obsolete.']);
    }
}

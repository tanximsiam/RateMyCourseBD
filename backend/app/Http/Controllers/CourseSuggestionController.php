<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CourseSuggestion;
use App\Models\Course;

class CourseSuggestionController extends Controller
{
    //
    public function index()
    {
        return CourseSuggestion::with(['university', 'department', 'user'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'university_id' => 'required|exists:universities,id',
            'department_id' => 'required|exists:departments,id',
            'code' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'credits' => 'nullable|numeric',
            'description' => 'nullable|string',
        ]);

        $validated['user_id'] = $request->user()->id;

        // Prevent duplicate suggestion
        $exists = CourseSuggestion::where([
            ['university_id', $validated['university_id']],
            ['department_id', $validated['department_id']],
            ['code', $validated['code']],
        ])->exists();

        if ($exists) {
            return response()->json(['message' => 'This course has already been suggested.'], 409);
        }

        $suggestion = CourseSuggestion::create($validated);

        return response()->json($suggestion, 201);
    }

    public function approve($id)
    {
        $suggestion = CourseSuggestion::findOrFail($id);

        if ($suggestion->approved) {
            return response()->json(['message' => 'Course already approved'], 400);
        }

        // Create a new course
        Course::create([
            'university_id' => $suggestion->university_id,
            'department_id' => $suggestion->department_id,
            'code' => $suggestion->code,
            'title' => $suggestion->title,
            'credits' => $suggestion->credits,
            'description' => $suggestion->description,
        ]);

        // Mark as approved
        $suggestion->approved = true;
        $suggestion->save();

        return response()->json(['message' => 'Course approved and added successfully']);
    }

    public function destroy($id)
    {
        $suggestion = CourseSuggestion::findOrFail($id);
        $suggestion->delete();

        return response()->json(['message' => 'Suggestion deleted']);
    }
}

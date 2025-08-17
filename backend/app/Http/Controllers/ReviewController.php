<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\Student;
use App\Models\Course;


class ReviewController extends Controller
{
    //
    public function courseReviews($id)
    {
        $course = Course::with(['department', 'university'])->findOrFail($id);
        $reviews = Review::with('user')
                ->where('course_id', $id)
                ->orderByDesc('created_at')
                ->get();

        return response()->json([
            'course' => $course,
            'reviews' => $reviews
        ]);
    }


    public function index(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
        ]);

        $reviews = Review::where('course_id', $request->course_id)->get();

        return response()->json($reviews);
    }


    public function store(Request $request)
    {
        $request->validate([
            'course_id'     => 'required|exists:courses,id',
            'rating'        => 'required|integer|min:1|max:5',
            'review_text'   => 'nullable|string',
            'tags'          => 'nullable|array',
            'tags.*'        => 'string',
            'is_anonymous'  => 'boolean',
        ]);

        $user = $request->user();

        if ($user->role !== 'student') {
            return response()->json(['message' => 'Only students can submit reviews'], 403);
        }

        $student = Student::where('user_id', $user->id)->first();

        if (!$student) {
            return response()->json(['message' => 'Student profile not found'], 403);
        }

        $course = Course::findOrFail($request->course_id);

        if (
            $student->university_id !== $course->university_id
        ) {
            return response()->json(['message' => 'You are not allowed to review this course'], 403);
        }

        $data = $request->only(['course_id', 'rating', 'review_text', 'tags', 'is_anonymous']);
        $data['user_id'] = $user->id;

        $review = Review::create($data);

        return response()->json(['message' => 'Review submitted', 'review' => $review], 201);
    }
    public function getVotes(Review $review)
    {
        return response()->json([
            'total_votes' => $review->upvotes - $review->downvotes,
            'upvotes'     => $review->upvotes,
            'downvotes'   => $review->downvotes,
        ]);
    }

    public function report(Review $review)
    {
        $review->update(['is_reported' => true]);

        return response()->json(['message' => 'Review reported']);
    }
}

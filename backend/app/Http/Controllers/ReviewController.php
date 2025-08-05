<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;


class ReviewController extends Controller
{
    //
    public function store(Request $request)
    {
        $data = $request->validate([
            'course_id'     => 'required|exists:courses,id',
            'rating'        => 'required|integer|min:1|max:5',
            'review_text'   => 'nullable|string',
            'tags'          => 'nullable|array',
            'tags.*'        => 'string',
            'is_anonymous'  => 'boolean',
        ]);

        $data['user_id'] = $request->user()->id;

        $review = Review::create($data);

        return response()->json(['message' => 'Review submitted', 'review' => $review], 201);
    }

    public function vote(Request $request, Review $review)
    {
        $request->validate([
            'type' => 'required|in:up,down',
        ]);

        if ($request->type === 'up') {
            $review->increment('upvotes');
        } else {
            $review->increment('downvotes');
        }

        return response()->json(['message' => 'Vote recorded']);
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

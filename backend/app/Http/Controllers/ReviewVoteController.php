<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ReviewVote;
use App\Models\Review;

class ReviewVoteController extends Controller
{
    //
    public function store(Request $request)
    {
        $request->validate([
            'review_id' => 'required|exists:reviews,id',
            'vote' => 'required|in:1,-1',
        ]);

        $userId = $request->user()->id();
        $reviewId = $request->review_id;
        $voteValue = $request->vote;

        $existing = ReviewVote::where('user_id', $userId)
            ->where('review_id', $reviewId)
            ->first();

        $review = Review::findOrFail($reviewId);

        if ($existing) {
            if ($existing->vote === $voteValue) {
                // Clicked same vote again → remove vote
                $existing->delete();
                if ($voteValue === 1) $review->decrement('upvotes');
                if ($voteValue === -1) $review->decrement('downvotes');

                return response()->json(['message' => 'Vote removed']);
            } else {
                // Change vote direction
                if ($existing->vote === 1) $review->decrement('upvotes');
                if ($existing->vote === -1) $review->decrement('downvotes');

                $existing->vote = $voteValue;
                $existing->save();

                if ($voteValue === 1) $review->increment('upvotes');
                if ($voteValue === -1) $review->increment('downvotes');

                return response()->json(['message' => 'Vote updated']);
            }
        } else {
            // New vote
            ReviewVote::create([
                'user_id' => $userId,
                'review_id' => $reviewId,
                'vote' => $voteValue,
            ]);

            // Apply new vote
            if ($voteValue === 1) $review->increment('upvotes');
            if ($voteValue === -1) $review->increment('downvotes');

            return response()->json(['message' => 'Vote recorded']);
        }
    }
}

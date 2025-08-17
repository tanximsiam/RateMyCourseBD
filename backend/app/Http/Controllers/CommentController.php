<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\Review;
use Symfony\Component\HttpFoundation\Response;

class CommentController extends Controller
{
    //
    public function index(Review $review)
    {
        $comments = Comment::with('user:id,name')
            ->where('review_id', $review->id)
            ->latest()
            ->get();

        return response()->json($comments);
    }

    public function store(Request $request, Review $review)
    {
        $data = $request->validate([
            'content' => ['required','string','max:2000'],
        ]);

        $userId = $request->user()->id;

        try {
            // Attempt to create — DB unique index prevents duplicates
            $comment = Comment::create([
                'review_id' => $review->id,
                'user_id'   => $userId,
                'content'   => $data['content'],
            ]);

            return response()->json($comment, Response::HTTP_CREATED);

        } catch (\Illuminate\Database\QueryException $e) {
            // Handle duplicate (unique constraint violation)
            if ($e->errorInfo[0] === '23000') {
                return response()->json([
                    'message' => 'You have already commented on this review.'
                ], Response::HTTP_CONFLICT);
            }

            // Any other DB error
            throw $e;
        }
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;

class ModerationController extends Controller
{
    //
    public function reported(Request $request)
    {
        $reviews = Review::with(['user:id,name', 'course:id,title'])
            ->where('is_reported', true)
            ->orderByDesc('created_at')
            ->get();

        return response()->json($reviews);
    }

    public function keep($id)
    {
        $review = Review::findOrFail($id);

        if ($review->is_reported) {
            $review->is_reported = false;
            $review->save();
        }

        return response()->json([
            'message' => 'Review marked as safe',
            'review'  => $review->only(['id', 'is_reported']),
        ]);
    }
}

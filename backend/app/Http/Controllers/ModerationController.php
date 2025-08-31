<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\CourseOutline;

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


    public function adminReviewList()
    {
        $unapproved = CourseOutline::with(['course.department.university'])
            ->where('approved', false)
            ->where(function ($q) {
                $q->whereNull('obsolete')->orWhere('obsolete', false);
            })
            ->latest()
            ->get()
            ->map(function ($o) {
                return [
                    'id'         => $o->id,
                    'course_id'  => $o->course_id,
                    'course'     => $o->course?->title,
                    'university' => $o->course?->department?->university?->name,
                    'status'     => 'unapproved',
                    'file_path'  => $o->file_path,
                ];
            });

        $obsolete = CourseOutline::with(['course.department.university'])
            ->where('obsolete', true)
            ->latest()
            ->get()
            ->map(function ($o) {
                return [
                    'id'         => $o->id,
                    'course_id'  => $o->course_id,
                    'course'     => $o->course?->title,
                    'university' => $o->course?->department?->university?->name,
                    'status'     => 'obsolete',
                    'file_path'  => $o->file_path,
                ];
            });

        return response()->json([
            'unapproved' => $unapproved,
            'obsolete'   => $obsolete,
        ]);
    }

    // ---------- ADMIN: keep (approve + unobsolete) ----------
    public function adminKeep($id)
    {
        $outline = CourseOutline::find($id);
        if (!$outline) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $outline->approved = true;
        $outline->obsolete = false;
        $outline->save();

        return response()->json(['message' => 'Outline kept (approved=true, obsolete=false).']);
    }

    // ---------- ADMIN: delete ----------
    public function adminDestroy($id)
    {
        $outline = CourseOutline::find($id);
        if (!$outline) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $outline->delete();

        return response()->json(['message' => 'Outline deleted.']);
    }
}

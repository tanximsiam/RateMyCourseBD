<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use App\Models\User;
use App\Models\Course;

class Review extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'user_id',
        'course_id',
        'rating',
        'review_text',
        'tags',
        'is_anonymous',
        'upvotes',
        'downvotes',
        'is_reported',
    ];

    protected $casts = [
        'tags' => 'array',
        'is_anonymous' => 'boolean',
        'is_reported' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function votes()
    {
        return $this->hasMany(ReviewVote::class);
    }

}

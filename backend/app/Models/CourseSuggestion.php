<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CourseSuggestion extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'university_id',
        'department_id',
        'user_id',
        'code',
        'title',
        'credits',
        'description',
        'approved',
    ];

    public function university() {
        return $this->belongsTo(University::class);
    }

    public function department() {
        return $this->belongsTo(Department::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}

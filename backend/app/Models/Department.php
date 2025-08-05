<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use App\Models\University;
use App\Models\Course;

class Department extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'university_id',
        'name',
        'code'
    ];

    public function university()
    {
        return $this->belongsTo(University::class);
    }

    public function courses()
    {
        return $this->hasMany(Course::class);
    }
}

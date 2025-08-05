<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use App\Models\Department;
use App\Models\Course;

class University extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'name',
        'location',
        'domain',
        'website'
    ];

    public function departments()
    {
        return $this->hasMany(Department::class);
    }

    public function courses()
    {
        return $this->hasMany(Course::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use App\Models\University;
use App\Models\Department;

class Course extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'university_id',
        'department_id',
        'code',
        'title',
        'credits',
        'description',
        'outline_pdf'
    ];

    public function university()
    {
        return $this->belongsTo(University::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}

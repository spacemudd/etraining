<?php

namespace App\Models;

use App\Models\Back\Company;
use App\Models\Back\Course;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Quiz extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $perPage = 50;

    protected $fillable = [
        'course_id',
        'name_ar',
        'created_by_id',
    ];

    public function scopeAttending($q)
    {
        $course_id = optional(auth()->user()->trainee)->course_id;
        return $q->where('course_id', $course_id);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class)->withTrashed();
    }
}

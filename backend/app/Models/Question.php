<?php

namespace App\Models;

use App\Models\Back\Company;
use App\Models\Back\Course;
use App\Models\Back\MaxNumber;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Question extends Model
{
    use HasFactory;
    use SoftDeletes;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $perPage = 50;

    protected $fillable = [
        'course_id',
        'quiz_id',
        'answer_id',
        'description',
        'created_by_id',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class)->withTrashed();
    }
    public function quizzes(): BelongsTo
    {
        return $this->belongsTo(Quiz::class)->withTrashed();
    }
    public function answer(): BelongsTo
    {
        return $this->belongsTo(Answer::class)->withTrashed();
    }
}

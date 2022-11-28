<?php

namespace App\Models;

use App\Models\Back\Company;
use App\Models\Back\Course;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quiz extends Model
{
    use HasFactory;
    use SoftDeletes;


    public $incrementing = false;

    protected $keyType = 'string';

    protected $perPage = 50;

    protected $fillable = [
        'course_id',
        'name_ar',
        'created_by_id',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class)->withTrashed();
    }

    public function questions(): BelongsTo
    {
        return $this->belongsTo(Question::class)->withTrashed();
    }
}

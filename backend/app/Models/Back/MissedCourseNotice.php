<?php

namespace App\Models\Back;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MissedCourseNotice extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_id',
        'trainee_id',
        'course_batch_session_id',
    ];
}

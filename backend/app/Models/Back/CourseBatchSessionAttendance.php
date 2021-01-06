<?php

namespace App\Models\Back;

use App\Scope\TeamScope;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Str;

class CourseBatchSessionAttendance extends Model
{
    use HasFactory;
    use HasUuid;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'course_batch_session_id',
        'course_batch_id',
        'course_id',
        'trainee_id',
        'trainee_user_id',
        'trainee_user_id',
        'session_starts_at',
        'session_ends_at',
        'attended_at',
        'physical_attendance',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::addGlobalScope(new TeamScope());
        static::creating(function ($model) {
            $model->{$model->getKeyName()} = (string) Str::uuid();
            if (auth()->user()) {
                $model->team_id = $model->team_id = auth()->user()->current_team_id;
            }
        });
    }

    public function trainee()
    {
        return $this->belongsTo(Trainee::class);
    }
}

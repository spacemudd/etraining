<?php

namespace App\Models\Back;

use App\Scope\TeamScope;
use App\Traits\HasUuid;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Str;

class CourseBatchSession extends Model
{
    use HasFactory;
    use HasUuid;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'course_id',
        'course_batch_id',
        'trainee_group_id',
        'starts_at',
        'ends_at',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::addGlobalScope(new TeamScope());
        static::creating(function ($model) {
            $model->{$model->getKeyName()} = (string) Str::uuid();
            if (auth()->user()) {
                $model->team_id = $model->team_id = auth()->user()->currentTeam()->first()->id;
            }
        });
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function course_batch()
    {
        return $this->belongsTo(CourseBatch::class);
    }

    public function trainee_group()
    {
        return $this->belongsTo(TraineeGroup::class);
    }

    public function attendances()
    {
        return $this->hasMany(CourseBatchSessionAttendance::class);
    }

    public function setStartsAtAttribute($value)
    {
        $this->attributes['starts_at'] = $value ? Carbon::parse($value) : null;
    }

    public function setEndsAtAttribute($value)
    {
        $this->attributes['ends_at'] = $value ? Carbon::parse($value) : null;
    }
}

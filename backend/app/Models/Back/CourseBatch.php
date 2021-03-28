<?php

namespace App\Models\Back;

use App\Scope\TeamScope;
use App\Traits\HasUuid;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use JamesMills\LaravelTimezone\Facades\Timezone;
use OwenIt\Auditing\Contracts\Auditable;
use Str;

class CourseBatch extends Model implements Auditable
{
    use HasFactory;
    use HasUuid;
    use \OwenIt\Auditing\Auditable;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'trainee_group_id',
        'course_id',
        'starts_at',
        'ends_at',
        'location_at',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    protected $appends = [
        'starts_at_timezone',
        'ends_at_timezone',
        'location_at_display',
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

    public function course_batch_sessions()
    {
        return $this->hasMany(CourseBatchSession::class);
    }

    public function trainee_group()
    {
        return $this->belongsTo(TraineeGroup::class);
    }

    public function setStartsAtAttribute($value)
    {
        $this->attributes['starts_at'] = $value ? Carbon::parse($value, optional(auth()->user())->timezone ?: config('app.timezone'))->setTimezone('UTC') : null;
    }

    public function setEndsAtAttribute($value)
    {
        $this->attributes['ends_at'] = $value ? Carbon::parse($value, optional(auth()->user())->timezone ?: config('app.timezone'))->setTimezone('UTC') : null;
    }

    public function getStartsAtTimezoneAttribute()
    {
        if ($this->starts_at) {
            return Timezone::convertToLocal($this->starts_at, 'Y-m-d\TH:i:s\Z');
        }
    }

    public function getEndsAtTimezoneAttribute()
    {
        if ($this->ends_at) {
            return Timezone::convertToLocal($this->ends_at, 'Y-m-d\TH:i:s\Z');
        }
    }

    public function getLocationAtDisplayAttribute()
    {
        if ($this->location_at === 'online') {
            return __('words.online');
        }

        return $this->location_at;
    }
}

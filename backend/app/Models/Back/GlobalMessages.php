<?php

namespace App\Models\Back;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use JamesMills\LaravelTimezone\Facades\Timezone;

class GlobalMessages extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'body',
        'starts_at',
        'expires_at',
        'created_by_id',
    ];

    protected $appends = [
        'starts_at_timezone',
        'expires_at_timezone',
    ];

    protected $dates = [
        'starts_at',
        'expires_at',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function ($model) {
            if (auth()->user()) {
                $model->created_by_id = auth()->user()->id;
            }
        });
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     *
     * @return string|void
     */
    public function getStartsAtTimezoneAttribute()
    {
        if ($this->starts_at) {
            return Timezone::convertToLocal($this->starts_at, 'Y-m-d h:i A (T)');
        }
    }

    /**
     *
     * @return string|void
     */
    public function getExpiresAtTimezoneAttribute()
    {
        if ($this->expires_at) {
            return Timezone::convertToLocal($this->expires_at, 'Y-m-d h:i A (T)');
        }
    }

    public function scopeAvailable($q)
    {
        return $q->where('starts_at', '<=', now())
            ->where('expires_at', '>=', now())
            ->orWhereNull('expires_at');
    }
}

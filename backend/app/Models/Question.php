<?php

namespace App\Models;

use App\Models\Back\Company;
use App\Models\Back\MaxNumber;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Question extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $perPage = 50;

    protected $fillable = [
        'quiz_id',
        'answer_id',
        'created_by_id',
        'description',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            $model->{$model->getKeyName()} = (string)Str::uuid();
            $model->number = MaxNumber::generatePrefixForInvoice();

            if (auth()->check()) {
                $model->created_by_id = auth()->id();
            }
        });
    }

    public function scopeAttending($q)
    {
        $course_id = optional(auth()->user()->trainee)->course_id;
        return $q->where('course_id', $course_id);
    }

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class)->withTrashed();
    }
}

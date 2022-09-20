<?php

namespace App\Models\Back;

use App\Models\User;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class TraineesComplaint extends Model
{
    use HasFactory;
    use HasUuid;

    public $incrementing = false;
    protected $keyType = 'string';

    const COMPLAINTS_STATUS_NEW = 0;
    const COMPLAINTS_STATUS_IN_PROGRESS = 1;
    const COMPLAINTS_STATUS_DONE = 2;

    protected $fillable = [
        'company_id',
        'trainee_id',
        'created_by_id',
        'number',
        'order_date',
        'complaints_status',
        'contact_way',
        'complaints',
        'actions',
        'reply',
        'note',
        'results',
    ];

    protected $appends = [
        'complaints_number_formatted'
    ];

    protected $dates = [
        'order_date',
    ];

    public $casts = [
        'created_at'  => 'datetime',
        'order_date' => 'datetime',
    ];
    /**
     * @var mixed
     */

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function ($model) {
            $model->{$model->getKeyName()} = (string) Str::uuid();
            $model->number = MaxNumber::generatePrefixForComplaints();

        });
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class)->withTrashed();
    }

    public function trainee(): BelongsTo
    {
        return $this->belongsTo(Trainee::class)->withTrashed();
    }

    public function created_by(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function getComplaintsNumberFormattedAttribute(): string
    {
        return $this->created_at->year
            . str_pad($this->created_at->month, 2, "0", STR_PAD_LEFT)
            . "-"
            . $this->number;
    }

}

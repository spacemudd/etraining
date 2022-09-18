<?php

namespace App\Models;

use App\Models\Back\Company;
use App\Models\Back\Trainee;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TraineesComplaint extends Model
{
    use HasFactory;

    const COMPLAINTS_STATUS_NEW = 0;
    const COMPLAINTS_STATUS_IN_PROGRESS = 1;
    const COMPLAINTS_STATUS_DONE = 2;

    protected $fillable = [
        'company_id',
        'trainee_id',
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

    protected $dates = [
        'order_date',
    ];

    public $casts = [
        'created_at'  => 'datetime',
    ];

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
}

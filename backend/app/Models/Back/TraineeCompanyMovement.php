<?php

namespace App\Models\Back;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TraineeCompanyMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'trainee_id',
        'trainee_name',
        'trainee_identity_number',
        'trainee_phone_number',
        'in_date',
        'out_date',
    ];

    protected $appends = [
        'in_date_ksa',
        'out_date_ksa',
    ];

    protected $casts = [
        'in_date' => 'datetime',
        'out_date' => 'datetime',
    ];

    public function getInDateKsaAttribute()
    {
        return optional(optional($this->in_date)->setTimezone('Asia/Riyadh'))->toDateTimeString();
    }

    public function getOutDateKsaAttribute()
    {
        return optional(optional($this->out_date)->setTimezone('Asia/Riyadh'))->toDateTimeString();
    }

    public function trainee()
    {
        return $this->belongsTo(Trainee::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}

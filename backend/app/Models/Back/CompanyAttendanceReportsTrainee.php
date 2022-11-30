<?php

namespace App\Models\Back;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyAttendanceReportsTrainee extends Model
{
    use HasFactory;

    protected $casts = [
        'active' => 'boolean',
        'start_date' => 'datetime',
        'end_date' => 'datetime'
    ];

    protected $fillable = [
        'status',
        'comment',
        'active',
        'start_date',
        'end_date',
    ];

    public function trainee()
    {
        return $this->belongsTo(Trainee::class)->withTrashed();
    }
}

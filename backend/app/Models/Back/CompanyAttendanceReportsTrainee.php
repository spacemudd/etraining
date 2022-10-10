<?php

namespace App\Models\Back;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyAttendanceReportsTrainee extends Model
{
    use HasFactory;

    protected $casts = [
        'active' => 'boolean',
    ];
}

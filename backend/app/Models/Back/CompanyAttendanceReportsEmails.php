<?php

namespace App\Models\Back;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyAttendanceReportsEmails extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'type',
        'clicked_confirmed_at',
        'delivered_at',
        'opened_at',
        'failed_at',
        'failed_reason',
    ];
}

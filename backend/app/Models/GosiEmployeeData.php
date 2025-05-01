<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;

class GosiEmployeeData extends Model implements \OwenIt\Auditing\Contracts\Auditable
{
    use HasFactory;
    use Auditable;

    protected $table = 'gosi_employee_data';

    protected $fillable = [
        'nin_or_iqama',
        'data',
        'reason_employment_office',
        'reason_collection',
        'reason_trainee_affairs',
        'reason_sales',
        'reason_other',
    ];

    protected $casts = [
        'data' => 'array',
    ];
}

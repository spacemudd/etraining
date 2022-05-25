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
        'in_date',
        'out_date',
    ];
}

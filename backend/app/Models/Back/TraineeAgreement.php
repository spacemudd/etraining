<?php

namespace App\Models\Back;

use App\Models\Back\Trainee;
use App\Models\EducationalLevel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class TraineeAgreement extends Model
{
    use HasFactory;

    protected $fillable = [
        'trainee_id',
        'rejected_at',
        'accepted_at',
    ];

    public function trainee()
    {
        return $this->belongsTo(Trainee::class);
    }

}

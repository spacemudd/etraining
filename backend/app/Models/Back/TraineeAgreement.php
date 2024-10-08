<?php

namespace App\Models\Back;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\EducationalLevel;


class TraineeAgreement extends Model
{
    use HasFactory;

    public function educational_level()
    {
        return $this->belongsTo(EducationalLevel::class);
    }
    
}

<?php

namespace App\Models\Back;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyTraineeLinkAudit extends Model
{
    use HasFactory;

    protected $casts = [
        'created_at' => 'date:Y-m-d',
        'updated_at' => 'datetime',
    ];

    public function trainee()
    {
        return $this->belongsTo(Trainee::class)->withTrashed();
    }
}

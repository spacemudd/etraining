<?php

namespace App\Models\Back;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComplaintsSettings extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'emails' => 'array',
    ];
}

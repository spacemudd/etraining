<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $perPage = 50;

    protected $fillable = [
        'value',
        'is_right',
    ];
}

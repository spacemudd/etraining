<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_id',
        'created_by_id',
        'course_name',
        'course_instructor',
        'message',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceReportDueDates extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'filename', 'course_name', 'company_id', 'status'];

}

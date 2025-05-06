<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class AppSetting extends Model implements Auditable, HasMedia
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
    use InteractsWithMedia;

    protected $fillable = [
        'name',
        'value',
    ];
}

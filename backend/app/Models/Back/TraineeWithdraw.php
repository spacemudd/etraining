<?php

namespace App\Models\Back;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Str;

class TraineeWithdraw extends Model implements HasMedia, Auditable
{
    use HasFactory;
    use InteractsWithMedia;
    use \OwenIt\Auditing\Auditable;
    use SoftDeletes;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'trainee_id',
        'company_id',
        'reason',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function ($model) {
            $model->{$model->getKeyName()} = (string) Str::uuid();
            $model->number = MaxNumber::generateForPrefix('WR', 20);
        });
    }

    public function trainee()
    {
        return $this->belongsTo(Trainee::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function approved_by()
    {
        return $this->belongsTo(User::class);
    }
}

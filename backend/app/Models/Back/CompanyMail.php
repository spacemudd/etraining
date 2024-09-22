<?php

namespace App\Models\Back;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class CompanyMail extends Model implements Auditable, HasMedia
{
    use HasFactory;
    use HasUuid;
    use \OwenIt\Auditing\Auditable;
    use InteractsWithMedia;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'company_id',
        'from',
        'sender',
        'subject',
        'body_text',
        'body_html',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function ($model) {
            $model->{$model->getKeyName()} = (string) Str::uuid();
        });
    }

    public function attachments($folder = 'attachments')
    {
        return $this->media()->where('collection_name', $folder);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

}

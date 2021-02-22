<?php

namespace App\Models\Back;

use App\Scope\TeamScope;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Str;

class Payment extends Model implements HasMedia, Auditable
{
    const STATUS_UNDER_REVIEW = 0;

    use HasFactory;
    use HasUuid;
    use \OwenIt\Auditing\Auditable;
    use InteractsWithMedia;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $appends = [
        'created_at_display',
        'status_css',
        'short_code',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::addGlobalScope(new TeamScope());
        static::creating(function ($model) {
            $model->{$model->getKeyName()} = (string) Str::uuid();
            if (auth()->user()) {
                $model->team_id = $model->team_id = auth()->user()->current_team_id;
            }
        });
    }

    public function sale_invoice()
    {
        return $this->belongsTo(SaleInvoice::class);
    }

    /**
     * Upload scan(s) of the documents.
     *
     * @param $file
     * @param $folder
     * @return \Spatie\MediaLibrary\MediaCollections\Models\Media
     * @throws \Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist
     * @throws \Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig
     */
    public function uploadToFolder($file, $folder)
    {
        return $this->addMedia($file)
            ->withAttributes([
                'team_id' => $this->team_id,
            ])
            ->toMediaCollection($folder);
    }

    public function getCreatedAtDisplayAttribute()
    {
        return $this->created_at->toDateTimeString();
    }

    public function getStatusCssAttribute()
    {
        if ($this->status === self::STATUS_UNDER_REVIEW) {
            return '<span class="bg-yellow-200 text-black p-2 rounded">'.__('words.under-manual-review').'</span>';
        }
    }

    public function getShortCodeAttribute()
    {
        return substr($this->id, 0, 6);
    }
}

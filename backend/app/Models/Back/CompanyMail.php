<?php

namespace App\Models\Back;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\MediaLibrary\InteractsWithMedia;

class CompanyMail extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
    use InteractsWithMedia;

    protected $fillable = [
        'company_id',
        'from',
        'sender',
        'body_text',
        'body_html',
    ];

        /**
     * Upload scan(s) of the documents.
     *
     * @param $file
     * @param $folder
     * @return \Spatie\MediaLibrary\MediaCollections\Models\Media
     * @throws \Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist
     * @throws \Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig
     */
    public function storeToFolder($file, $folder)
    {
        return $this->addMedia($file)
            ->usingFileName($file->hashName())
            ->toMediaCollection($folder);
    }

    public function attachments($folder = 'attachments')
    {
        return $this->media()->where('collection_name', $folder);
    }
}

<?php

namespace App\Models\Back;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;

class SaleInvoice extends Model implements \OwenIt\Auditing\Contracts\Auditable
{
    use HasFactory;
    use HasUuid;
    use Auditable;

    public $incrementing = false;

    protected $keyType = 'string';

    const STATUS_DRAFT = 0;
    const STATUS_ISSUED = 1;
    const STATUS_VOID = 2;

    protected $fillable = [
        'issued_at',
    ];
}

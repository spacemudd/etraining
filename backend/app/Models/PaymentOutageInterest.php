<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Back\Invoice;
use App\Models\Back\Trainee;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentOutageInterest extends Model
{
    use HasUuid;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $table = 'payment_outage_interest';

    protected $fillable = [
        'trainee_id',
        'invoice_id',
        'last_seen_at',
        'notified_at',
    ];

    protected $casts = [
        'last_seen_at' => 'datetime',
        'notified_at' => 'datetime',
    ];

    public static function remember(?Trainee $trainee, ?Invoice $invoice = null): void
    {
        if ($trainee === null) {
            return;
        }

        $row = static::query()->firstOrNew(['trainee_id' => $trainee->id]);
        $row->invoice_id = $invoice->id ?? $row->invoice_id;
        $row->last_seen_at = now();
        $row->save();
    }

    public function trainee(): BelongsTo
    {
        return $this->belongsTo(Trainee::class)->withTrashed();
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }
}

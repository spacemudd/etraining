<?php

namespace App\Models\Back;

use App\Notifications\AssignedToCompanyTraineeNotification;
use App\Scope\TeamScope;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use OwenIt\Auditing\Auditable;

class AccountingLedgerBook extends Model implements \OwenIt\Auditing\Contracts\Auditable
{
    use HasFactory;
    use Auditable;

    protected $guarded = ['id'];

    protected static function boot(): void
    {
        parent::boot();
        static::addGlobalScope(new TeamScope());
        static::creating(function ($model) {
            if (auth()->user()) {
                $model->team_id = $model->team_id = auth()->user()->currentTeam()->first()->id;
            }
        });
    }

    static public function getBalanceForTrainee($trainee_id)
    {
        $q = AccountingLedgerBook::where('trainee_id', $trainee_id);
        $total_credit = $q->sum('credit');
        $total_debit = $q->sum('debit');
        return $total_debit - $total_credit;
    }
}

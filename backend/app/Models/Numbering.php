<?php

namespace App\Models;

use App\Scope\TeamScope;
use App\Traits\HasUuid;
use DB;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use RuntimeException;
use Str;

class Numbering extends Model
{
    use HasFactory, HasUuid;

    protected $table = 'numbering';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'name',
        'value',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::addGlobalScope(new TeamScope());
        static::creating(function ($model) {
            $model->{$model->getKeyName()} = (string) Str::uuid();
            if (auth()->user()) {
                $model->team_id = auth()->user()->personalTeam()->id;
            }
        });
    }

    /**
     *
     * @param $prefix
     * @return mixed
     * @throws \Exception
     */
    public static function getNewNumber($prefix)
    {
        if (! DB::transactionLevel()) {
            throw new RuntimeException('This command only runs when inside a transaction.');
        }

        $numbering = Numbering::firstOrCreate([
            'name' => $prefix,
        ], [
            'name' => $prefix,
            'value' => 1000,
        ]);

        $numbering->value++;
        $numbering->save();

        return $numbering->name.$numbering->value;
    }
}

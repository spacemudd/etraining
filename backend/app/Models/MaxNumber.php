<?php

namespace App\Models;

use App\Scope\TeamScope;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class MaxNumber extends Model
{
    use HasFactory;
    use HasUuid;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'team_id',
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
                $model->team_id = auth()->user()->current_team_id;
            }
        });
    }

    /**
     * Start a new prefix.
     * You're required to update
     *
     * @param string $prefix The prefix string.
     * @param string $team_id Uses logged in user's team ID if null.
     * @param int $initial_value The starting number.
     * @return string
     */
    public static function generateForPrefix(string $prefix, string $team_id, int $initial_value=1000): string
    {
        $maxNumber = MaxNumber::firstOrCreate([
            'team_id' => $team_id,
            'name' => $prefix,
        ], [
            'team_id' => $team_id,
            'name' => $prefix,
            'value' => $initial_value,
        ]);

        $number = ++$maxNumber->value;
        $maxNumber->value = $number;
        $maxNumber->save();

        return $maxNumber->name.$number;
    }

}

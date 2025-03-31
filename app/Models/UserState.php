<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\UserState
 *
 * @property int $id
 * @property int $user_id
 * @property string $state
 * @property string $command
 * @property array $data
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|UserState newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserState newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserState query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserState whereCommand($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserState whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserState whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserState whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserState whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserState whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserState whereUserId($value)
 * @mixin \Eloquent
 */
class UserState extends Model
{
    protected $fillable = ['user_id', 'state', 'command', 'data'];
    protected $casts = [
        'data' => 'array'
    ];
}

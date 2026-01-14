<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Work_times
 *
 * @property int $id
 * @property int $user_id
 * @property int $club_id
 * @property string $check_in Вермя прихода
 * @property string|null $check_out Время ухода
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Work_times newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Work_times newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Work_times query()
 * @method static \Illuminate\Database\Eloquent\Builder|Work_times whereCheckIn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Work_times whereCheckOut($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Work_times whereClubId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Work_times whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Work_times whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Work_times whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Work_times whereUserId($value)
 * @mixin \Eloquent
 */
class Work_times extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'club_id',
        'check_in',
        'check_out'
    ];
}

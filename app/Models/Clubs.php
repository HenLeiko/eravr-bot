<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Clubs
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Clubs newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Clubs newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Clubs query()
 * @property int $id
 * @property string $name
 * @property string|null $address
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Clubs whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Clubs whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Clubs whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Clubs whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Clubs whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Clubs extends Model
{
    use HasFactory;
}

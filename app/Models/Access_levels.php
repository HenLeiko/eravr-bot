<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Access_levels
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Access_levels newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Access_levels newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Access_levels query()
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property int $access_index
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Access_levels whereAccessIndex($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Access_levels whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Access_levels whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Access_levels whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Access_levels whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Access_levels whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Access_levels extends Model
{
    use HasFactory;
}

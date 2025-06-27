<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\ContestReward
 *
 * @method static \Database\Factories\ContestRewardFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|ContestReward newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ContestReward newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ContestReward query()
 * @property-read ContestReward|null $contest
 * @property int $id
 * @property string|null $name Название приза
 * @property string|null $description Описание приза
 * @property int $ref_amount Кол-во рефералов для приза
 * @property bool $is_active Статус активности приза
 * @property int|null $contest_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|ContestReward whereContestId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContestReward whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContestReward whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContestReward whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContestReward whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContestReward whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContestReward whereRefAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContestReward whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ContestReward extends Model
{
    use HasFactory;
    protected $table = 'contest_rewards';

    protected $fillable = [
        'name',
        'description',
        'ref_amount',
        'is_active',
        'contest_id',
    ];
    protected $casts = [
        'ref_amount' => 'integer',
        'is_active' => 'boolean'
    ];

    /**
     * @return BelongsTo
     */
    public function contest(): BelongsTo
    {
        return $this->belongsTo(Contests::class);
    }

    /**
     * @param $query
     * @return mixed
     */
    public function getOnlyActive($query): mixed
    {
        return $query->where('is_active', true);
    }

    /**
     * @param $query
     * @param $amount
     * @return mixed
     */
    public function getByRefCountAmount($query, int $amount): mixed
    {
        return $query->where('ref_amount', '>=', $amount);
    }
}

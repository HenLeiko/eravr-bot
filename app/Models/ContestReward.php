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

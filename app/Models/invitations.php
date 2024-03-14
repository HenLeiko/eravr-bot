<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\invitations
 *
 * @property mixed|string|null $title
 * @property mixed|string|null $user_id
 * @property int $id
 * @property string|null $code
 * @property string|null $club
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\TelegramUser $user
 * @method static \Illuminate\Database\Eloquent\Builder|invitations newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|invitations newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|invitations query()
 * @method static \Illuminate\Database\Eloquent\Builder|invitations whereClub($value)
 * @method static \Illuminate\Database\Eloquent\Builder|invitations whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|invitations whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|invitations whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|invitations whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|invitations whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|invitations whereUserId($value)
 * @mixin \Eloquent
 */
class invitations extends Model
{
    use HasFactory;

    /**
     * @var mixed|string|null
     */
    /**
     * @var mixed|string|null
     */

    /**
     * @var mixed|string|null
     */
    protected $table = 'invitations';
    protected $guarded = false;

    public function user(): BelongsTo
    {
        return $this->belongsTo(TelegramUser::class);
    }
}

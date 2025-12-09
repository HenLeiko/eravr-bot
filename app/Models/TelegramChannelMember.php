<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * App\Models\TelegramChannelMember
 *
 * @property-read TelegramChannelMember|null $referrer
 * @method static \Illuminate\Database\Eloquent\Builder|TelegramChannelMember newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TelegramChannelMember newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TelegramChannelMember query()
 * @property int $id
 * @property int|null $chat_id ID чата или канала
 * @property int $user_id ID пользователя
 * @property string $status Статус пользователя: member|administrator|restricted|left|kicked
 * @property string $ref_code Реферальный код
 * @property int|null $ref_by
 * @property \Illuminate\Support\Carbon|null $timeout Ограничение на участие в конкурсах
 * @property bool $is_participating Метка участия в конкурсе
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|TelegramChannelMember whereChatId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TelegramChannelMember whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TelegramChannelMember whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TelegramChannelMember whereIsParticipating($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TelegramChannelMember whereRefBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TelegramChannelMember whereRefCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TelegramChannelMember whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TelegramChannelMember whereTimeout($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TelegramChannelMember whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TelegramChannelMember whereUserId($value)
 * @mixin \Eloquent
 */
class TelegramChannelMember extends Model
{
    protected $table = 'telegram_channel_members';

    protected $fillable = [
        'chat_id',
        'user_id',
        'status',
        'ref_code',
        'ref_by',
        'timeout',
        'is_participating'
    ];
    protected $casts = [
        'timeout' => 'datetime',
        'is_participating' => 'boolean'
    ];

    public function referrer(): BelongsTo
    {
        return $this->belongsTo(self::class, 'ref_by');
    }

    public function referrals(): HasMany
    {
        return $this->hasMany(self::class, 'ref_by')->where(['is_participating' => true]);
    }

    public function canParticipate(): bool
    {
        return $this->is_participating && ($this->timeout === null || $this->timeout->isPast());
    }

    public function isTimeout(): bool
    {
        return $this->timeout === null || $this->timeout->isPast();
    }

    public static function generateRefCode(): string
    {
        do {
            $code = Str::random(8);
        } while (self::where('ref_code', $code)->exists());
        return $code;
    }

    public function scopeCanParticipate($query)
    {
        return $query
            ->where('is_participating', 1)
            ->where(function ($q) {
                $q->whereNull('timeout')
                ->orWhere('timeout', '<', now());
            });
    }
}

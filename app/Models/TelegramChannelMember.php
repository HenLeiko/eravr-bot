<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    public function canParticipate(): bool
    {
        return $this->is_participating && ($this->timeout === null || $this->timeout->isPast());
    }

}

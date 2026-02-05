<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\PaymentHistroy
 *
 * @property-read mixed $calendar_event_id
 * @property-read mixed $calendar_id
 * @property-read mixed $client_name
 * @property-read mixed $client_phone
 * @property-read mixed $club_name
 * @property-read mixed $hours
 * @property-read mixed $vr_headsets
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentHistroy newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentHistroy newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentHistroy query()
 * @mixin \Eloquent
 */
class PaymentHistroy extends Model
{
    use HasFactory;

    protected $fillable = [
        'tinkoff_payment_id',
        'tinkoff_order_id',
        'amount',
        'status',
        'payment_url',
        'metadata',
        'paid_at'
    ];

    protected $casts = [
        'metadata' => 'array',
        'paid_at' => 'datetime',
    ];

    protected $attributes = [
        'status' => 'new'
    ];
    const STATUS_NEW = 'new';
    const STATUS_PENDING = 'pending';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_FAILED = 'failed';
    const STATUS_REFUNDED = 'refunded';

//    геттеры для метадаты
    public function getCalendarEventIdAttribute()
    {
        return $this->metadata['calendar']['event_id'] ?? null;
    }

    public function getCalendarIdAttribute()
    {
        return $this->metadata['calendar']['calendar_id'] ?? null;
    }

    public function getClientNameAttribute()
    {
        return $this->metadata['client']['name'] ?? null;
    }

    public function getClientPhoneAttribute()
    {
        return $this->metadata['client']['phone'] ?? null;
    }

    public function getVrHeadsetsAttribute()
    {
        return $this->metadata['booking']['vr_headsets'] ?? null;
    }

    public function getHoursAttribute()
    {
        return $this->metadata['booking']['hours'] ?? null;
    }

    public function getClubNameAttribute()
    {
        return $this->metadata['booking']['club'] ?? null;
    }

    public static function findByPaymentId(string $paymentId): ?self
    {
        return self::where('tinkoff_payment_id', $paymentId)->first();
    }

    public static function findByOrderId(string $orderId): ?self
    {
        return self::where('tinkoff_order_id', $orderId)->first();
    }

    public static function findByEventId(string $eventId): ?self
    {
        return self::whereEventId($eventId)->first();
    }

    public function markAsPaid(): self
    {
        $this->update([
            'status' => self::STATUS_CONFIRMED,
            'paid_at' => now(),
        ]);

        return $this;
    }

    public function markAsPending(string $paymentUrl): self
    {
        $this->update([
            'status' => self::STATUS_PENDING,
            'payment_url' => $paymentUrl,
        ]);

        return $this;
    }

    public function markAsRefunded(string $paymentUrl): self
    {
        $this->update([
            'status' => self::STATUS_REFUNDED,
            'payment_url' => $paymentUrl
        ]);

        return $this;
    }

    public function markAsFailed(string $errorCode): self
    {
        $metadata = $this->metadata ?? [];
        $metadata['error'] = [
            'code' => $errorCode,
            'time' => now()->toISOString(),
        ];

        $this->update([
            'status' => self::STATUS_FAILED,
            'metadata' => $metadata,
        ]);

        return $this;
    }

    public function isPaid(): bool
    {
        return $this->status === self::STATUS_CONFIRMED;
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isFailed(): bool
    {
        return $this->status === self::STATUS_FAILED;
    }

    public function getAmountInRubles(): float
    {
        return $this->amount / 100;
    }

    public static function createFromTinkoffResponse(array $tinkoffData, array $metadata = []): self
    {
        return self::create([
            'tinkoff_payment_id' => $tinkoffData['PaymentId'] ?? null,
            'tinkoff_order_id' => $tinkoffData['OrderId'] ?? null,
            'amount' => $tinkoffData['Amount'] ?? 0,
            'payment_url' => $tinkoffData['PaymentURL'] ?? null,
            'status' => self::STATUS_PENDING,
            'metadata' => $metadata,
        ]);
    }
}

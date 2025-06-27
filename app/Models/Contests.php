<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Contests
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Contests newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Contests newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Contests query()
 * @property int $id
 * @property string $title Оглавление конкурса
 * @property string $description Описание конкурса
 * @property string|null $image Картинка/гифка к посту конкурса
 * @property \Illuminate\Support\Carbon $started_at Дата и время начала конкурса
 * @property \Illuminate\Support\Carbon $ended_at Дата и время конца конкурса
 * @property string|null $tiny_description Сокращённое описание конкурса для личных сообщений
 * @property bool $is_active Статус проведения конкурса
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Contests whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contests whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contests whereEndedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contests whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contests whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contests whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contests whereStartedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contests whereTinyDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contests whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contests whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Contests extends Model
{
    protected $table = 'contests';
    protected $fillable = [
        'title',
        'description',
        'image',
        'tiny_description',
        'is_active',
        'started_at',
        'ended_at'
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'is_active' => 'boolean'
    ];

    public function isActive(): bool
    {
        return $this->is_active && $this->ended_at->isPast();
    }
}

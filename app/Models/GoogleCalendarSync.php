<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\GoogleCalendarSync
 *
 * @method static \Illuminate\Database\Eloquent\Builder|GoogleCalendarSync newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GoogleCalendarSync newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GoogleCalendarSync query()
 * @property int $id
 * @property string $calendar_id id календаря
 * @property string|null $sync_token Токен синхронизации Эвентов
 * @property string $channel_id id канала нотиф вебхука
 * @property string $resource_id id ресурса от гугла
 * @property string $channel_expiration Дата-время смерти вебхука
 * @property string|null $last_sync_at Дата-время последний синхронизации эвентов
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|GoogleCalendarSync whereCalendarId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoogleCalendarSync whereChannelExpiration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoogleCalendarSync whereChannelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoogleCalendarSync whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoogleCalendarSync whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoogleCalendarSync whereLastSyncAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoogleCalendarSync whereResourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoogleCalendarSync whereSyncToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoogleCalendarSync whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class GoogleCalendarSync extends Model
{
    use HasFactory;

    protected $fillable = [
        'calendar_id',
        'sync_token',
        'channel_id',
        'resource_id',
        'channel_expiration',
        'last_sync_at',
        'timeMin'
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\GoogleTokens
 *
 * @method static \Illuminate\Database\Eloquent\Builder|GoogleTokens newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GoogleTokens newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GoogleTokens query()
 * @property int $id
 * @property string $access_token Access token для гугла
 * @property string $refresh_token Токен для рефреша токена доступа
 * @property string $expires_at Дата-время смерти токена
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|GoogleTokens whereAccessToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoogleTokens whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoogleTokens whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoogleTokens whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoogleTokens whereRefreshToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoogleTokens whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class GoogleTokens extends Model
{
    use HasFactory;

    protected $fillable = [
        'access_token',
        'refresh_token',
        'expires_at'
    ];
}

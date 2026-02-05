<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * App\Models\Certificate
 *
 * @property int $id
 * @property int $user_id
 * @property int|null $value
 * @property string|null $code
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Certificate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Certificate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Certificate query()
 * @method static \Illuminate\Database\Eloquent\Builder|Certificate whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Certificate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Certificate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Certificate whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Certificate whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Certificate whereValue($value)
 * @mixin \Eloquent
 */
	class Certificate extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ContestReward
 *
 * @method static \Database\Factories\ContestRewardFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|ContestReward newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ContestReward newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ContestReward query()
 * @mixin \Eloquent
 */
	class ContestReward extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Contests
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Contests newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Contests newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Contests query()
 * @mixin \Eloquent
 */
	class Contests extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Invitation
 *
 * @property int $id
 * @property int $user_id
 * @property string|null $title
 * @property string|null $code
 * @property string|null $club
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Invitation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Invitation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Invitation query()
 * @method static \Illuminate\Database\Eloquent\Builder|Invitation whereClub($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invitation whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invitation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invitation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invitation whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invitation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invitation whereUserId($value)
 * @mixin \Eloquent
 */
	class Invitation extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\TelegramChannelMember
 *
 * @property-read TelegramChannelMember|null $referrer
 * @method static \Illuminate\Database\Eloquent\Builder|TelegramChannelMember newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TelegramChannelMember newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TelegramChannelMember query()
 * @mixin \Eloquent
 */
	class TelegramChannelMember extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\TelegramUser
 *
 * @property int $id
 * @property int $user_id
 * @property string|null $first_name
 * @property string|null $last_name
 * @property string|null $username
 * @property string|null $language_code
 * @property int|null $is_premium
 * @property int|null $is_bot
 * @property string $role
 * @property string|null $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Invitation> $invitations
 * @property-read int|null $invitations_count
 * @method static \Illuminate\Database\Eloquent\Builder|TelegramUser newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TelegramUser newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TelegramUser query()
 * @method static \Illuminate\Database\Eloquent\Builder|TelegramUser whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TelegramUser whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TelegramUser whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TelegramUser whereIsBot($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TelegramUser whereIsPremium($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TelegramUser whereLanguageCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TelegramUser whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TelegramUser whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TelegramUser whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TelegramUser whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TelegramUser whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TelegramUser whereUsername($value)
 * @mixin \Eloquent
 */
	class TelegramUser extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\User
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property mixed $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class User extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\UserState
 *
 * @property int $id
 * @property int $user_id
 * @property string $state
 * @property string $command
 * @property array $data
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|UserState newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserState newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserState query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserState whereCommand($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserState whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserState whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserState whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserState whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserState whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserState whereUserId($value)
 * @mixin \Eloquent
 */
	class UserState extends \Eloquent {}
}


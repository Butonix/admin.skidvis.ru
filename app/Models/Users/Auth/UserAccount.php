<?php
/**
 * Created by PhpStorm.
 * User: Pavel Onokhov
 * Date: 13.06.2019
 * Time: 12:51
 */

namespace App\Models\Users\Auth;


use App\Models\Users\Auth\AuthProvider;
use App\Models\Users\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Users\UserAccount
 *
 * @property int                                      $id
 * @property string                                   $provider_user_id
 * @property int                                      $auth_provider_id
 * @property int                                      $user_id
 * @property string                                   $payload
 * @property \Illuminate\Support\Carbon|null          $created_at
 * @property \Illuminate\Support\Carbon|null          $updated_at
 * @property \Illuminate\Support\Carbon|null          $deleted_at
 * @property-read \App\Models\Users\Auth\AuthProvider $authProvider
 * @property-read \App\Models\Users\User              $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\Auth\UserAccount newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\Auth\UserAccount newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\Auth\UserAccount query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\Auth\UserAccount whereAuthProviderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\Auth\UserAccount whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\Auth\UserAccount whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\Auth\UserAccount whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\Auth\UserAccount wherePayload($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\Auth\UserAccount whereProviderUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\Auth\UserAccount whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\Auth\UserAccount whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Users\Auth\UserAccount withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Users\Auth\UserAccount withoutTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Users\Auth\UserAccount onlyTrashed()
 * @method static bool|null forceDelete()
 * @method static bool|null restore()
 * @mixin \Eloquent
 */
class UserAccount extends Model {
    use SoftDeletes;

    /**
     * @var array
     */
    protected $fillable = ['provider_user_id', 'auth_provider_id', 'user_id', 'payload'];

    /**
     * @var array
     */
    protected $casts = [
        'payload' => 'array',
    ];

    /**
     * @return string
     */
    public function getProviderUserId(): ?string {
        return $this->{'provider_user_id'};
    }

    /**
     * @param int $providerUserId
     */
    public function setProviderUserId(int $providerUserId): void {
        $this->{'provider_user_id'} = $providerUserId;
    }

    /**
     * @return BelongsTo
     */
    public function authProvider(): BelongsTo {
        return $this->belongsTo(AuthProvider::class);
    }

    /**
     * @return AuthProvider
     */
    public function getAuthProvider(): AuthProvider {
        return $this->authProvider;
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    /**
     * @return User
     */
    public function getUser(): User {
        return $this->user;
    }
}

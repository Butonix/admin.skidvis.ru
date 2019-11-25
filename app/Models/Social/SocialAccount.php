<?php
/**
 * Created by PhpStorm.
 * User: Pavel Onokhov
 * Date: 21.06.2019
 * Time: 18:15
 */

namespace App\Models\Social;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Social\SocialAccount
 *
 * @property int                                                                              $id
 * @property string                                                                           $link
 * @property int                                                                              $social_network_id
 * @property string|null                                                                      $social_user_id
 * @property int                                                                              $social_account_id
 * @property string                                                                           $social_account_type
 * @property \Illuminate\Support\Carbon|null                                                  $created_at
 * @property \Illuminate\Support\Carbon|null                                                  $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Social\SocialAccount[] $socialAccount
 * @property-read \App\Models\Social\SocialNetwork                                            $socialNetwork
 * @property \Illuminate\Support\Carbon|null                                                  $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Social\SocialAccount newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Social\SocialAccount newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Social\SocialAccount query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Social\SocialAccount whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Social\SocialAccount whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Social\SocialAccount whereLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Social\SocialAccount whereSocialAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Social\SocialAccount whereSocialAccountType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Social\SocialAccount whereSocialNetworkId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Social\SocialAccount whereSocialUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Social\SocialAccount whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Social\SocialAccount whereDeletedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Social\SocialAccount onlyTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Social\SocialAccount withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Social\SocialAccount withoutTrashed()
 * @method static bool|null forceDelete()
 * @method static bool|null restore()
 * @mixin \Eloquent
 */
class SocialAccount extends Model {
    use SoftDeletes;

    /**
     * @var array
     */
    protected $fillable = ['link', 'social_network_id', 'social_user_id', 'social_account_id', 'social_account_type'];

    /**
     * @return string
     */
    public function getLink(): string {
        return $this->{'link'};
    }

    /**
     * @return BelongsTo
     */
    public function socialNetwork(): BelongsTo {
        return $this->belongsTo(SocialNetwork::class);
    }

    /**
     * @return SocialNetwork
     */
    public function getSocialNetwork(): SocialNetwork {
        return $this->socialNetwork;
    }

    /**
     * @return null|string
     */
    public function getSocialNetworkName(): ?string {
        return $this->getSocialNetwork()->getName();
    }

    /**
     * @return null|string
     */
    public function getSocialNetworkIcon(): ?string {
        return $this->getSocialNetwork()->getIconUrl();
    }

    /**
     * @return null|string
     */
    public function getSocialUserId(): ?string {
        return $this->{'social_user_id'};
    }

    /**
     * @param string $id
     */
    public function setSocialUserId(string $id): void {
        $this->{'social_user_id'} = $id;
    }

    /**
     * @return MorphTo
     */
    public function socialAccount(): MorphTo {
        return $this->morphTo();
    }
}

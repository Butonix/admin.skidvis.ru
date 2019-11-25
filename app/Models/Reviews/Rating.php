<?php
/**
 * Created by PhpStorm.
 * User: Pavel Onokhov
 * Date: 02.08.2019
 * Time: 15:11
 */

namespace App\Models\Reviews;


use App\Models\Organizations\Organization;
use App\Models\Users\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

/**
 * App\Models\Reviews\Rating
 *
 * @property int                                              $id
 * @property int|null                                         $user_id
 * @property int|null                                         $organization_id
 * @property int|null                                         $rating
 * @property-read \App\Models\Organizations\Organization|null $organization
 * @property-read \App\Models\Users\User|null                 $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Reviews\Rating newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Reviews\Rating newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Reviews\Rating query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Reviews\Rating whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Reviews\Rating whereOrganizationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Reviews\Rating whereRating($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Reviews\Rating whereUserId($value)
 * @mixin \Eloquent
 */
class Rating extends Model {
    protected $fillable = ['organization_id', 'user_id', 'rating'];

    public $timestamps = false;

    /**
     * @return int|null
     */
    public function getRating(): ?int {
        return $this->{'rating'};
    }

    /**
     * @param int|null $rating
     */
    public function setRating(?int $rating): void {
        $this->{'rating'} = $rating;
    }

    /**
     * @return BelongsTo
     */
    public function organization(): BelongsTo {
        return $this->belongsTo(Organization::class);
    }

    /**
     * @return Organization|null
     */
    public function getOrganization(): ?Organization {
        return $this->organization;
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    /**
     * @return User|null
     */
    public function getUser(): ?User {
        return $this->user;
    }
}

<?php
/**
 * Created by PhpStorm.
 * User: Pavel Onokhov
 * Date: 25.07.2019
 * Time: 12:07
 */

namespace App\Models\Reviews;


use App\Models\Users\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * App\Models\Reviews\Like
 *
 * @property int                                                                      $id
 * @property int|null                                                                 $user_id
 * @property string|null                                                              $likeable_id
 * @property string|null                                                              $likeable_type
 * @property \Illuminate\Support\Carbon|null                                          $created_at
 * @property \Illuminate\Support\Carbon|null                                          $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Reviews\Like[] $likeable
 * @property-read \App\Models\Users\User|null                                         $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Reviews\Like newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Reviews\Like newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Reviews\Like query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Reviews\Like whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Reviews\Like whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Reviews\Like whereLikeableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Reviews\Like whereLikeableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Reviews\Like whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Reviews\Like whereUserId($value)
 * @mixin \Eloquent
 */
class Like extends Model {
    /**
     * @var array
     */
    protected $fillable = ['user_id', 'likeable_id', 'likeable_type'];

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

    /**
     * @return MorphTo
     */
    public function likeable(): MorphTo {
        return $this->morphTo();
    }
}

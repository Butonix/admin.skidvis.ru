<?php
/**
 * Created by PhpStorm.
 * User: Pavel Onokhov
 * Date: 24.07.2019
 * Time: 16:40
 */

namespace App\Models\Reviews;

use App\Models\Organizations\Organization;
use App\Models\Products\Product;
use App\Models\Users\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Reviews\Review
 *
 * @property int                                                                        $id
 * @property int|null                                                                   $user_id
 * @property int|null                                                                   $reviewable_id
 * @property string|null                                                                $reviewable_type
 * @property string|null                                                                $text
 * @property string|null                                                                $pros
 * @property string|null                                                                $cons
 * @property int|null                                                                   $rating
 * @property int|null                                                                   $likes_count
 * @property \Illuminate\Support\Carbon|null                                            $created_at
 * @property \Illuminate\Support\Carbon|null                                            $updated_at
 * @property \Illuminate\Support\Carbon|null                                            $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Reviews\Like[]   $likes
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Reviews\Review[] $reviewable
 * @property-read \App\Models\Users\User|null                                           $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Reviews\Review whereCons($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Reviews\Review whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Reviews\Review whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Reviews\Review whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Reviews\Review whereLikesCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Reviews\Review wherePros($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Reviews\Review whereRating($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Reviews\Review whereReviewableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Reviews\Review whereReviewableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Reviews\Review whereText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Reviews\Review whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Reviews\Review whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Reviews\Review filter($frd)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Reviews\Review ordering($frd)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Reviews\Review popularityOrdering($orderingDir)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Reviews\Review createdAtOrdering($orderingDir)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Reviews\Review organizationReviews($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Reviews\Review productReviews($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Reviews\Review ratingOrdering($orderingDir)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Reviews\Review newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Reviews\Review newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Reviews\Review query()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Reviews\Review withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Reviews\Review withoutTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Reviews\Review onlyTrashed()
 * @method static bool|null forceDelete()
 * @method static bool|null restore()
 * @mixin \Eloquent
 */
class Review extends Model {
    use SoftDeletes;

    /**
     * @var array
     */
    protected $fillable = [
        'user_id',
        'reviewable_id',
        'reviewable_type',
        'text',
        'pros',
        'cons',
        'rating',
        'likes_count',
    ];

    /**
     * @var string
     */
    protected $defaultOrderingDir = 'DESC';

    /**
     * @var string
     */
    protected $defaultOrdering = 'created_at';

    /**
     * @var int
     */
    protected $minutesForEdit = 30;

    /**
     * @var int
     */
    protected $perPage = 5;

    /**
     * @var array
     */
    protected static $rulesForArticles = [
        'text' => 'required|string',
    ];

    /**
     * @var array
     */
    protected static $messagesForArticles = [
        'text.required' => 'Общий текст отзыва обязателен',
    ];

    /**
     * @var array
     */
    protected static $rulesForProducts = [
        'text' => 'required|string',
        'pros' => 'nullable|string',
        'cons' => 'nullable|string',
    ];

    /**
     * @var array
     */
    protected static $messagesForProducts = [
        'text.required' => 'Общий текст отзыва обязателен',
    ];

    /**
     * @var array
     */
    protected static $rulesForOrganizations = [
        'text'   => 'nullable|string|max:255',
        'rating' => 'required|integer',
    ];

    /**
     * @var array
     */
    protected static $messagesForOrganizations = [
        'rating.required' => 'Укажите рейтинг для организации',
    ];

    /**
     * @return array
     */
    public static function getRulesForArticles(): array {
        return self::$rulesForArticles;
    }

    /**
     * @return array
     */
    public static function getMessagesForArticles(): array {
        return self::$messagesForArticles;
    }

    /**
     * @return array
     */
    public static function getRulesForProducts(): array {
        return self::$rulesForProducts;
    }

    /**
     * @return array
     */
    public static function getMessagesForProducts(): array {
        return self::$messagesForProducts;
    }

    /**
     * @return array
     */
    public static function getRulesForOrganizations(): array {
        return self::$rulesForOrganizations;
    }

    /**
     * @return array
     */
    public static function getMessagesForOrganizations(): array {
        return self::$messagesForOrganizations;
    }

    /**
     * @return null|string
     */
    public function getText(): ?string {
        return $this->text;
    }

    /**
     * @param null|string $text
     */
    public function setText(?string $text): void {
        $this->text = $text;
    }

    /**
     * @return null|string
     */
    public function getPros(): ?string {
        return $this->pros;
    }

    /**
     * @param null|string $pros
     */
    public function setPros(?string $pros): void {
        $this->pros = $pros;
    }

    /**
     * @return null|string
     */
    public function getCons(): ?string {
        return $this->cons;
    }

    /**
     * @param null|string $cons
     */
    public function setCons(?string $cons): void {
        $this->cons = $cons;
    }

    /**
     * @return int|null
     */
    public function getRating(): ?int {
        return $this->rating;
    }

    /**
     * @param int|null $rating
     */
    public function setRating(?int $rating): void {
        $this->rating = $rating;
    }

    /**
     * @return int|null
     */
    public function getLikesCount(): ?int {
        return $this->likes_count;
    }

    /**
     * @param int|null $likesCount
     */
    public function setLikes(?int $likesCount): void {
        $this->likes = $likesCount;
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

    /**
     * @return int|null
     */
    public function getUserId(): ?int {
        $user = $this->getUser();

        if (is_null($user)) {
            return null;
        }

        return $user->getKey();
    }

    /**
     * @return array
     */
    public function getUserShortInfo(): array {
        $user = $this->getUser();

        if (is_null($user)) {
            return [];
        }

        return [
            'id'       => $this->getUserId(),
            'initials' => $user->getInitials(),
            'f_name'   => $user->getFirstName(),
            'l_name'   => $user->getLastName(),
            'm_name'   => $user->getMiddleName(),
            'avatar'   => [
                'id'  => $user->getAvatarId(),
                'src' => $user->getAvatarLink(),
            ],
        ];
    }

    /**
     * @return MorphTo
     */
    public function reviewable(): MorphTo {
        return $this->morphTo();
    }

    /**
     * @param Builder $query
     * @param string  $orderingDir
     *
     * @return Builder
     */
    public function scopeRatingOrdering(Builder $query, string $orderingDir): Builder {
        return $query->orderBy('rating', $orderingDir);
    }

    /**
     * @param Builder $query
     * @param string  $orderingDir
     *
     * @return Builder
     */
    public function scopeCreatedAtOrdering(Builder $query, string $orderingDir): Builder {
        return $query->orderBy('created_at', $orderingDir);
    }

    /**
     * @param Builder $query
     * @param int     $value
     *
     * @return Builder
     */
    public function scopeOrganizationReviews(Builder $query, int $value): Builder {
        return $query->where(function (Builder $query) use ($value) {
            $query->whereHasMorph('reviewable', Organization::class, function (Builder $query) use ($value) {
                $query->whereKey($value);
            });
        });
    }

    /**
     * @param Builder $query
     * @param int     $value
     *
     * @return Builder
     */
    public function scopeProductReviews(Builder $query, int $value): Builder {
        return $query->where(function (Builder $query) use ($value) {
            $query->whereHasMorph('reviewable', Product::class, function (Builder $query) use ($value) {
                $query->whereKey($value);
            });
        });
    }

    /**
     * @return string
     */
    public function getDefaultOrderingDir(): string {
        return $this->defaultOrderingDir;
    }

    /**
     * @return string
     */
    public function getDefaultOrdering(): string {
        return $this->defaultOrdering;
    }

    /**
     * @param Builder $query
     * @param array   $frd
     *
     * @return Builder
     */
    public function scopeFilter(Builder $query, array $frd): Builder {
        $fillable = $this->fillable;

        foreach ($frd as $key => $value) {
            if (is_null($value)) {
                continue;
            }

            switch ($key) {
                case 'organization_id':
                    {
                        $query->organizationReviews($value);
                    }
                    break;
                case 'product_id':
                    {
                        $query->productReviews($value);
                    }
                default:
                    {
                        if (in_array($key, $fillable)) {
                            $query->where($key, $value);
                        }
                    }
                    break;
            }
        }

        return $query;
    }

    /**
     * @param Builder $query
     * @param array   $frd
     *
     * @return Builder
     */
    public function scopeOrdering(Builder $query, array $frd): Builder {
        $ordering = (isset($frd['ordering']))
            ? $frd['ordering']
            : $this->getDefaultOrdering();
        $orderingDir = (isset($frd['orderingDir']))
            ? $frd['orderingDir']
            : $this->getDefaultOrderingDir();

        if ($ordering === 'rating') {
            $query->ratingOrdering($orderingDir);
        } elseif ($ordering === 'created_at') {
            $query->createdAtOrdering($orderingDir);
        }

        return $query;
    }

    /**
     * @return MorphMany
     */
    public function likes(): MorphMany {
        return $this->morphMany(Like::class, 'likeable');
    }

    /**
     * @return Collection
     */
    public function getLikes(): Collection {
        return $this->likes;
    }

    /**
     * @param int $userId
     *
     * @return bool
     */
    public function isLiked(int $userId): bool {
        return $this->likes()->where('user_id', $userId)->exists();
    }

    /**
     * @return int
     */
    public function getMinutesForEdit(): int {
        return $this->minutesForEdit;
    }

    /**
     * @return bool
     */
    public function isEditable(): bool {
        //$minutesForEdit = $this->getMinutesForEdit();
        //$pastMinutesAfterCreated = Carbon::now()->diffInMinutes($this->created_at);
        //return ($pastMinutesAfterCreated < $minutesForEdit);

        //TODO: Исправить метод в зависимости от того, будут ли отзывы редактируемы или нет

        return false;
    }
}

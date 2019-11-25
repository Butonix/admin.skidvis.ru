<?php
/**
 * Created by PhpStorm.
 * User: Pavel Onokhov
 * Date: 21.06.2019
 * Time: 15:50
 */

namespace App\Models\Organizations;


use App\Models\Files\Image;
use App\Models\Products\Product;
use App\Models\Reviews\Rating;
use App\Models\Reviews\Review;
use App\Models\Social\SocialAccount;
use App\Models\Users\User;
use App\Traits\EmailsTrait;
use App\Traits\ImagesTrait;
use App\Traits\PhonesTrait;
use App\Traits\SchedulesTrait;
use App\Traits\SocialsTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;


/**
 * App\Models\Organizations\Organization
 *
 * @property int                                                                                                 $id
 * @property string|null                                                                                         $name
 * @property string|null                                                                                         $description
 * @property int|null                                                                                            $avatar_id
 * @property int|null                                                                                            $cover_id
 * @property int|null                                                                                            $mini_logo_id
 * @property float                                                                                               $rating
 * @property int|null                                                                                            $phone_id
 * @property int|null                                                                                            $email_id
 * @property \Illuminate\Support\Carbon|null                                                                     $created_at
 * @property \Illuminate\Support\Carbon|null                                                                     $updated_at
 * @property \Illuminate\Support\Carbon|null                                                                     $deleted_at
 * @property-read \App\Models\Files\Image|null                                                                   $avatar
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Organizations\Point[]                     $points
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Organizations\OrganizationPointSchedule[] $schedules
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Products\Product[]                        $products
 * @property string|null                                                                                         $inn
 * @property array|null                                                                                          $payload
 * @property string|null                                                                                         $avatar_color
 * @property string|null                                                                                         $link
 * @property int|null                                                                                            $creator_id
 * @property-read \App\Models\Users\User|null                                                                    $creator
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Users\User[]                              $users
 * @property string|null                                                                                         $short_description
 * @property int                                                                                                 $is_published
 * @property int                                                                                                 $wishlist_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Reviews\Review[]                          $reviews
 * @property-read \App\Models\Communications\Email|null                                                          $email
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Communications\Email[]                    $emails
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Files\Image[]                             $images
 * @property-read \App\Models\Communications\Phone|null                                                          $phone
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Communications\Phone[]                    $phones
 * @property-read \App\Models\Organizations\OrganizationPointSchedule                                            $schedule
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Social\SocialAccount[]                    $socialAccounts
 * @property-read \App\Models\Files\Image|null                                                                   $cover
 * @property-read \App\Models\Files\Image|null                                                                   $miniLogo
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Reviews\Rating[]                          $ratings
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Files\Image[]                             $sliderImages
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Organizations\Organization onlyTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Organizations\Organization withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Organizations\Organization withoutTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organizations\Organization filter($frd)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organizations\Organization newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organizations\Organization newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organizations\Organization query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organizations\Organization whereAvatarId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organizations\Organization whereCoverId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organizations\Organization whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organizations\Organization whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organizations\Organization whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organizations\Organization whereEmailId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organizations\Organization whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organizations\Organization whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organizations\Organization wherePhoneId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organizations\Organization whereRating($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organizations\Organization whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organizations\Organization whereInn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organizations\Organization wherePayload($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organizations\Organization whereAvatarColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organizations\Organization whereCreatorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organizations\Organization whereLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organizations\Organization ordering($frd)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organizations\Organization popularityOrdering($orderingDir)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organizations\Organization search($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organizations\Organization sortingByPoints($orderingDir)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organizations\Organization sortingByProducts($orderingDir)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organizations\Organization whereIsPublished($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organizations\Organization whereShortDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organizations\Organization whereWishlistCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organizations\Organization ratingOrdering($orderingDir)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organizations\Organization reviewsOrdering($orderingDir)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organizations\Organization categoriesFilter($categories)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organizations\Organization cityFilter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organizations\Organization tagsFilter($tags)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organizations\Organization organizationsByUser($userId)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organizations\Organization organizationsWithout($ids)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organizations\Organization publicOrganizations()
 * @method static bool|null forceDelete()
 * @method static bool|null restore()
 * @mixin \Eloquent
 */
class Organization extends Model {
    use SoftDeletes;
    use PhonesTrait;
    use EmailsTrait;
    use ImagesTrait;
    use SocialsTrait;
    use SchedulesTrait;

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'short_description',
        'inn',
        'avatar_id',
        'avatar_color',
        'cover_id',
        'link', //Веб-сайт
        'rating', //Средний рейтинг
        'phone_id',
        'email_id',
        'payload',
        'creator_id',
        'is_published',
        'is_caption',
        'is_all_similar_disabled',
        'is_advertisement',
        'wishlist_count', //Кол-во добавлений в избранное
        'mini_logo_id',
        'type_map_point' //Отвечает за тип отдаваемого значка (1 - значок первой категории, 2 - мини-лого)
    ];

    //Параметр для получения значка из первой категории
    const TYPE_MAP_POINT_CATEGORY = 1;

    //Параметр для получения значка из мини-лого
    const TYPE_MAP_POINT_MINI_LOGO = 2;

    /**
     *
     */
    public static function boot() {
        parent::boot();
        self::deleting(function (Organization $organization) {

            foreach ($organization->products as $product) {
                $product->delete();
            }
        });
    }

    /**
     * @var string
     */
    protected $defaultOrderingDir = 'ASC';

    /**
     * @var array
     */
    protected $casts = [
        'payload' => 'array',
    ];

    /**
     * @var array
     */
    protected static $rules = [
        'name'              => 'required|string',
        'description'       => 'nullable|string',
        'short_description' => 'nullable|string',
        'inn'               => 'nullable|string|max:255',
        'link'              => 'nullable|string|max:255',
        'phone'             => 'nullable|string',
        'email'             => 'nullable|email',
        'timezone'          => 'nullable|integer',
        'images'            => 'required|array|min:1',
    ];

    /**
     * @var array
     */
    protected static $messages = [
        'name.required'   => 'Укажите название организации',
        'images.required' => 'Наличие хотя бы 1 изображения обязательно',
        'images.min'      => 'Необходимо добавить минимум :min изображение',
    ];

    /**
     * @return array
     */
    public static function getRules(): array {
        return self::$rules;
    }

    /**
     * @return array
     */
    public static function getMessages(): array {
        return self::$messages;
    }

    /**
     * @return array|null
     */
    public function getPayload(): ?array {
        return $this->payload;
    }

    /**
     * @param array|null $payload
     */
    public function setPayload(?array $payload): void {
        $this->payload = $payload;
    }

    /**
     * @return array
     */
    public static function getOrganizationsList(): array {
        return self::orderBy('name', 'ASC')->pluck('organizations.name', 'organizations.id')->toArray();
    }

    /**
     * @return null|string
     */
    public function getLink(): ?string {
        return $this->{'link'};
    }

    /**
     * @param null|string $link
     */
    public function setLink(?string $link): void {
        $this->{'link'} = $link;
    }

    /**
     * @return null|string
     */
    public function getInn(): ?string {
        return $this->inn;
    }

    /**
     * @param null|string $inn
     */
    public function setInn(?string $inn): void {
        $this->inn = $inn;
    }

    /**
     * @return HasMany
     */
    public function points(): HasMany {
        return $this->hasMany(Point::class);
    }

    /**
     * @return Collection
     */
    public function getPoints(): Collection {
        return $this->points;
    }

    /**
     * @param array $filter
     *
     * @return int
     */
    public function getPointsCount(array $filter = []): int {
        if (!empty($filter)) {
            return $this->points()->filter($filter)->get()->count();
        } else {
            return $this->getPoints()->count();
        }
    }

    /**
     * @return array
     */
    public function getPointsList(): array {
        return $this->points()->orderBy('name', 'ASC')->pluck('points.name', 'points.id')->toArray();
    }

    /**
     * @param array $filter
     *
     * @return int
     */
    public function getPointsWithProductsCount(array $filter = []): int {
        if (!empty($filter)) {
            return $this->points()->filter($filter)->whereHas('products')->count();
        } else {
            return $this->points()->whereHas('products')->count();
        }
    }

    /**
     * @return Collection
     */
    public function getPointsWithDifferentTime(): Collection {
        return $this->points()->whereHas('schedule', function (Builder $query) {
            $query->where('is_different', true);
        })->get();
    }

    /**
     * @return int|null
     */
    public function getWishlistCount(): ?int {
        return $this->{'wishlist_count'};
    }

    /**
     * @param int|null $count
     */
    public function setWishlistCount(?int $count): void {
        $this->{'wishlist_count'} = $count;
    }

    /**
     * @return bool
     */
    public function isPublished(): ?bool {
        return $this->{'is_published'};
    }

    /**
     * @return bool
     */
    public function isAllSimilarDisabled(): ?bool {
        return $this->{'is_all_similar_disabled'};
    }

    /**
     * @return bool
     */
    public function isAdvertisement(): ?bool {
        return $this->{'is_advertisement'};
    }

    /**
     * @return bool
     */
    public function isCaption(): ?bool {
        return $this->{'is_caption'};
    }

    /**
     * @return bool
     */
    public function isUnpublished(): ?bool {
        return !$this->{'is_published'};
    }

    /**
     * @param bool $isPublished
     */
    public function setPublished(bool $isPublished): void {
        $this->{'is_published'} = $isPublished;
    }

    /**
     * @param bool $isCaption
     */
    public function setCaption(bool $isCaption): void {
        $this->{'is_caption'} = $isCaption;
    }

    /**
     * @param bool $isAllSimilarDisabled
     */
    public function setAllSimilarDisabled(bool $isAllSimilarDisabled): void {
        $this->{'is_all_similar_disabled'} = $isAllSimilarDisabled;
    }

    /**
     * @param bool $isAdvertisement
     */
    public function setAdvertisement(bool $isAdvertisement): void {
        $this->{'is_advertisement'} = $isAdvertisement;
    }

    /**
     * @return null|string
     */
    public function getName(): ?string {
        return $this->name;
    }

    /**
     * @param null|string $name
     */
    public function setName(?string $name): void {
        $this->name = $name;
    }

    /**
     * @return null|string
     */
    public function getDescription(): ?string {
        return $this->{'description'};
    }

    /**
     * @param null|string $description
     */
    public function setDescription(?string $description): void {
        $this->{'description'} = $description;
    }

    /**
     * @return null|string
     */
    public function getShortDescription(): ?string {
        return $this->{'short_description'};
    }

    /**
     * @param null|string $shortDescription
     */
    public function setShortDescription(?string $shortDescription): void {
        $this->{'short_description'} = $shortDescription;
    }

    /**
     * @return int
     */
    public function getDiscountForAll(): int {
        return $this->{'discount_for_all'};
    }

    /**
     * @param int $discount_for_all
     */
    public function setDiscountForAll(int $discount_for_all): void {
        $this->{'discount_for_all'} = $discount_for_all;
    }

    /**
     * @param array $logo
     *
     * @throws \Exception
     */
    public function updateAvatar(array $logo): void {
        $oldAvatar = $this->getAvatar();

        if (!empty($logo)) {
            (isset($logo['color']))
                ? $this->setAvatarColor($logo['color'])
                : $this->setAvatarColor(null);

            if (isset($logo['id'])) {
                $newAvatarId = $logo['id'];

                if (!is_null($oldAvatar) && $oldAvatar->getKey() !== $newAvatarId) {
                    $this->deleteAvatar($oldAvatar);
                    /**
                     * @var Image $newAvatar
                     */
                    $newAvatar = Image::whereKey($newAvatarId)->first();

                    if (!is_null($newAvatar)) { //Сохраняем только если аватар найден
                        $this->saveAvatar($newAvatar);
                    }
                } elseif (is_null($oldAvatar)) {
                    /**
                     * @var Image $newAvatar
                     */
                    $newAvatar = Image::whereKey($newAvatarId)->first();

                    if (!is_null($newAvatar)) { //Сохраняем только если аватар найден
                        $this->saveAvatar($newAvatar);
                    }
                }
            } else {
                if (!is_null($oldAvatar)) {
                    $this->deleteAvatar($oldAvatar);
                }
            }
        } else {
            $this->setAvatarColor(null);

            if (!is_null($oldAvatar)) {
                $this->deleteAvatar($oldAvatar);
            }
        }
    }

    /**
     * @param Image $newAvatar
     */
    public function saveAvatar(Image $newAvatar): void {
        $newAvatar = $this->images()->save($newAvatar);
        $this->avatar()->associate($newAvatar);
    }

    /**
     * @param Image $oldAvatar
     *
     * @throws \Exception
     */
    public function deleteAvatar(Image $oldAvatar): void {
        $oldAvatar->delete();
        $this->avatar()->dissociate();
    }

    /**
     * @return BelongsTo
     */
    public function avatar(): BelongsTo {
        return $this->belongsTo(Image::class);
    }

    /**
     * @return null|string
     */
    public function getAvatarColor(): ?string {
        return $this->avatar_color;
    }

    /**
     * @param null|string $avatar_color
     */
    public function setAvatarColor(?string $avatar_color): void {
        $this->avatar_color = $avatar_color;
    }

    /**
     * @return Image|null
     */
    public function getAvatar(): ?Image {
        return $this->avatar;
    }

    /**
     * @return null|string
     */
    public function getAvatarLink(): ?string {
        $avatar = $this->getAvatar();

        if (is_null($avatar)) {
            return '';
        }

        return $avatar->getPublishPath();
    }

    /**
     * @return int|null
     */
    public function getAvatarId(): ?int {
        $avatar = $this->getAvatar();

        if (is_null($avatar)) {
            return null;
        }

        return $avatar->getKey();
    }

    /**
     * @return bool
     */
    public function hasAvatar(): bool {
        return !is_null($this->getAvatar());
    }

    /**
     * @return null|string
     */
    public function getFirstSliderImageLink(): ?string {
        $sliderImages = $this->getSliderImages();

        if (empty($sliderImages)) {
            return null;
        }

        /**
         * @var Image $firstSliderImage
         */
        $firstSliderImage = $sliderImages->first();

        return $firstSliderImage->getPublishPath();
    }

    /**
     * @return MorphMany
     */
    public function sliderImages(): MorphMany {
        return $this->morphMany(Image::class, 'fileable')
                    ->where('public_path', 'like', '%' . 'cover' . '%')
                    ->where('file_parent_id', null);
    }

    /**
     * @return Collection
     */
    public function getCoversCollection(): Collection {
        return $this->getSliderImages();
    }

    /**
     * @return array
     */
    public function getCoversLinks(): array {
        $covers = $this->getSliderImages();
        $result = [];

        foreach ($covers as $cover) {
            /**
             * @var Image $cover
             */
            $coverLinks = [];
            $coverLinks['src'] = $cover->getPublishPath();
            $coverLinks['id'] = $cover->getKey();

            if ($cover->getMime() !== 'image/svg+xml') {
                $coverLinks[1920]['src'] = $cover->getPublishPath();
                $coverLinks[1920]['id'] = $cover->getKey();

                $children = $cover->getChildren();

                foreach ($children as $child) {
                    /**
                     * @var Image $child
                     */
                    $coverWidth = $child->getWidth();
                    $coverLinks[$coverWidth]['src'] = $child->getPublishPath();
                    $coverLinks[$coverWidth]['id'] = $child->getKey();
                }
            }

            $result[] = $coverLinks;
        }

        return $result;
    }

    /**
     * @return int
     */
    public function countCovers(): int {
        return $this->getSliderImages()->count();
    }

    /**
     * @return float
     */
    public function getRating(): ?float {
        return $this->{'rating'};
    }

    /**
     * @param float $rating
     */
    public function setRating(float $rating): void {
        $this->{'rating'} = $rating;
    }

    /**
     * @return int|null
     */
    public function getTimezoneId(): ?int {
        if (!$this->hasSchedule()) {
            return null;
        }

        return $this->getSchedule()->getTimezoneId();
    }

    /**
     * @return null|string
     */
    public function getScheduleText(): ?string {
        $schedule = $this->getSchedule();

        if (is_null($schedule)) {
            return null;
        }

        return $schedule->getTextTime();
    }

    /**
     * @return HasMany
     */
    public function products(): HasMany {
        return $this->hasMany(Product::class);
    }

    /**
     * @return Collection
     */
    public function getProducts(): Collection {
        return $this->products;
    }

    /**
     * @return int
     */
    public function getProductsCount(): int {
        return $this->getProducts()->count();
    }

    /**
     * @param Builder $query
     * @param string  $value
     *
     * @return Builder
     */
    public function scopeSearch(Builder $query, string $value): Builder {
        return $query->where(function (Builder $query) use ($value) {
            $query->orWhere('name', 'like', '%' . $value . '%')
                  ->orWhereHas('email', function (Builder $query) use ($value) {
                      $query->where('email', 'like', '%' . $value . '%');
                  })
                  ->orWhereHas('phone', function (Builder $query) use ($value) {
                      $query->where('full_phone', 'like', '%' . $value . '%');
                  })
                  ->orWhere('short_description', 'like', '%' . $value . '%');
        });
    }

    /**
     * @param Builder $query
     * @param string  $orderingDir
     *
     * @return Builder
     */
    public function scopeSortingByPoints(Builder $query, string $orderingDir): Builder {
        return $query->withCount('points')->orderBy('points_count', $orderingDir);
    }

    /**
     * @param Builder $query
     * @param string  $orderingDir
     *
     * @return Builder
     */
    public function scopeSortingByProducts(Builder $query, string $orderingDir): Builder {
        return $query->withCount('products')->orderBy('products_count', $orderingDir);
    }

    /**
     * @param Builder $query
     * @param string  $orderingDir
     *
     * @return Builder
     */
    public function scopePopularityOrdering(Builder $query, string $orderingDir): Builder {
        return $query->orderBy('wishlist_count', $orderingDir);
    }

    /**
     * @param Builder $query
     * @param string  $orderingDir
     *
     * @return Builder
     */
    public function scopeReviewsOrdering(Builder $query, string $orderingDir): Builder {
        return $query->withCount('reviews')->orderBy('reviews_count', $orderingDir);
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
     * @param         $value
     *
     * @return Builder
     */
    public function scopeCityFilter(Builder $query, $value): Builder {
        return $query->where(function (Builder $query) use ($value) {
            $query->whereHas('points', function (Builder $query) use ($value) {
                $query->whereHas('city', function (Builder $query) use ($value) {
                    $query->whereKey((int)$value);
                });
            });
        });
    }

    /**
     * @param Builder  $query
     * @param array    $categories
     * @param int|null $cityId
     *
     * @return Builder
     */
    public function scopeCategoriesFilter(Builder $query, array $categories, ?int $cityId): Builder {
        $resultArray = [];
        foreach ($categories as $each_number) {
            if (is_null($each_number)) {
                continue;
            }

            $resultArray[] = (int)$each_number;
        }

        if (!empty($resultArray)) {
            $query->where(function (Builder $query) use ($resultArray, $cityId) {
                $query->whereHas('products', function (Builder $query) use ($resultArray, $cityId) {
                    $query->publicProducts()->where(function (Builder $query) use ($resultArray, $cityId) {
                        $query->whereHas('categories', function (Builder $query) use ($resultArray) {
                            $query->whereIn('categories.id', $resultArray);
                        });

                        if (isset($cityId)) {
                            $query->whereHas('points', function (Builder $query) use ($cityId) {
                                $query->whereHas('city', function (Builder $query) use ($cityId) {
                                    $query->whereKey((int)$cityId);
                                });
                            });
                        }
                    });
                });
            });
        }


        return $query;
    }

    /**
     * @param Builder  $query
     * @param array    $tags
     * @param int|null $cityId
     *
     * @return Builder
     */
    public function scopeTagsFilter(Builder $query, array $tags, ?int $cityId): Builder {
        $resultArray = [];
        foreach ($tags as $each_number) {
            if (is_null($each_number)) {
                continue;
            }

            $resultArray[] = (int)$each_number;
        }

        if (!empty($resultArray)) {
            $query->where(function (Builder $query) use ($resultArray, $cityId) {
                $query->whereHas('products', function (Builder $query) use ($resultArray, $cityId) {
                    $query->publicProducts()->where(function (Builder $query) use ($resultArray, $cityId) {
                        $query->whereHas('tags', function (Builder $query) use ($resultArray) {
                            $query->whereIn('tags.id', $resultArray)->orderBy('tags.id');
                        });

                        if (isset($cityId)) {
                            $query->whereHas('points', function (Builder $query) use ($cityId) {
                                $query->whereHas('city', function (Builder $query) use ($cityId) {
                                    $query->whereKey((int)$cityId);
                                });
                            });
                        }
                    });
                });
            });
        }


        return $query;
    }

    /**
     * @param Builder $query
     * @param array   $ids
     *
     * @return Builder
     */
    public function scopeOrganizationsWithout(Builder $query, array $ids): Builder {
        return $query->whereNotIn('id', $ids);
    }

    /**
     * @param Builder $query
     * @param array   $frd
     *
     * @return Builder
     */
    public function scopeFilter(Builder $query, array $frd): Builder {
        $cityId = (isset($frd['city_id']))
            ? $frd['city_id']
            : null;
        $hasTagCategoryFilter = (!empty($frd['categories']) || !empty($frd['tags']));

        foreach ($frd as $key => $value) {
            if (is_null($value)) {
                continue;
            }

            switch ($key) {
                case 'search':
                    {
                        $query->search($value);
                    }
                    break;
                case 'city_id':
                    {
						/**
						 * На будущее, когда будет учитываться город
						 */
//                        if (!$hasTagCategoryFilter) {
//                            $query->cityFilter($value);
//                        }
                    }
                    break;
                case 'tags' :
                    {
                        $query->tagsFilter($value, $cityId);
                    }
                    break;
                case 'categories' :
                    {
                        $query->categoriesFilter($value, $cityId);
                    }
                    break;
                default:
                    {
                        if (in_array($key, $this->fillable)) {
                            $query->where($key, $value);
                        }
                    }
                    break;
            }
        }

        return $query;
    }

    /**
     * @return string
     */
    public function getDefaultOrderingDir(): string {
        return $this->defaultOrderingDir;
    }

    /**
     * @param Builder $query
     * @param array   $frd
     *
     * @return Builder
     */
    public function scopeOrdering(Builder $query, array $frd): Builder {
        $orderingDir = (isset($frd['orderingDir']))
            ? $frd['orderingDir']
            : $this->getDefaultOrderingDir();

        foreach ($frd as $key => $value) {
            if (is_null($value)) {
                continue;
            }

            if ($key === 'ordering') {
                if ($value === 'points') {
                    $query->sortingByPoints($orderingDir);
                } elseif ($value === 'products') {
                    $query->sortingByProducts($orderingDir);
                } elseif ($value === 'popularity') {
                    $query->popularityOrdering($orderingDir);
                } elseif ($value === 'reviews') {
                    $query->reviewsOrdering($orderingDir);
                } elseif ($value === 'rating') {
                    $query->ratingOrdering($orderingDir);
                } elseif ($value === 'random') {
                    $query->inRandomOrder();
                }
            }
        }

        return $query;
    }

    /**
     * @return BelongsTo
     */
    public function creator(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    /**
     * @return string
     */
    public function getCreatorName(): string {
        $creator = $this->getCreator();
        if (is_null($creator)) {
            return '';
        }

        return $creator->getName();
    }

    /**
     * @return User|null
     */
    public function getCreator(): ?User {
        return $this->creator;
    }

    /**
     * @return BelongsToMany
     */
    public function users(): BelongsToMany {
        return $this->belongsToMany(User::class, 'organization_user', 'organization_id', 'user_id');
    }

    /**
     * @return Collection
     */
    public function getUsers(): Collection {
        return $this->users;
    }

    /**
     * @return MorphMany
     */
    public function reviews(): MorphMany {
        return $this->morphMany(Review::class, 'reviewable');
    }

    /**
     * @return Collection
     */
    public function getReviews(): Collection {
        return $this->reviews;
    }

    /**
     * @return int
     */
    public function getReviewsCount(): int {
        return $this->getReviews()->count();
    }

    /**
     * @param int $userId
     *
     * @return bool
     */
    public function isUserLeftReview(int $userId): bool {
        return $this->reviews()->where('user_id', $userId)->exists();
    }

    /**
     * @return HasMany
     */
    public function ratings(): HasMany {
        return $this->hasMany(Rating::class);
    }

    /**
     * @return Collection
     */
    public function getRatings(): Collection {
        return $this->ratings;
    }

    /**
     * @return int|null
     */
    public function getRatingOfUser(): ?int {
        if (\Auth::guard('api')->guest()) {
            return null;
        }

        $userId = \Auth::guard('api')->id();
        /**
         * @var Rating $ratingOfUser
         */
        $ratingOfUser = $this->ratings()->where('user_id', $userId)->first();

        if (is_null($ratingOfUser)) {
            return null;
        }

        return $ratingOfUser->getRating();
    }

    /**
     *
     */
    public function calculateRating(): void {
        $ratingOrganization = $this->ratings()->avg('rating');
        $this->setRating($ratingOrganization);
    }

    /**
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopePublicOrganizations(Builder $query): Builder {
        return $query->where(function (Builder $query) {
            $query->where('is_published', true);
        });
    }

    /**
     * @param Builder $query
     * @param int     $userId
     *
     * @return Builder
     */
    public function scopeOrganizationsByUser(Builder $query, int $userId): Builder {
        return $query->where(function (Builder $query) use ($userId) {
            $query->orWhereHas('creator', function (Builder $query) use ($userId) {
                $query->whereKey($userId);
            })->orWhereHas('users', function (Builder $query) use ($userId) {
                $query->whereKey($userId);
            });
        });
    }

    public function exportPhoneAndEmailFromPoint(): string {
    	$point = $this->points()->first();

		$res = 'no';

    	if(isset($point)){
			$phone = $point->getPhone();
			$email = $point->getEmail();


			if(!empty($phone) && empty($this->getPhone())){
				$this->updatePhone($phone);
				$res = 'phone-';
			}

			if(!empty($email) && empty($this->getEmail())){
				$this->updateEmail($email);
				$res .= 'email-';
			}
		}

    	return $res;
	}

    /**
     * @param array $frd
     * @param User  $user
     *
     * @throws \Exception
     */
    public function updateOrganization(array $frd, User $user): void {
        $payload = [
            'orgnip'    => $frd['orgnip'] ?? null,
            'okved'     => $frd['okved'] ?? null,
            'address'   => $frd['address'] ?? null,
            'latitude'  => $frd['latitude'] ?? null,
            'longitude' => $frd['longitude'] ?? null,
        ];
        $this->setPayload($payload);

        $this->updateTypeMapPoint($frd['type_map_point'], $user);
        $this->updatePhone($frd['phone'] ?? null);
        $this->updateEmail($frd['email'] ?? null);
        $this->updateAvatar($frd['logo'] ?? []);
        $this->updateMiniLogo($frd['mini_logo'] ?? []);
        $this->updateSliderImages($frd['images']);
        $this->updateSocialAccounts($frd['socials'] ?? []);
        $this->updateSchedule($frd['operationMode'] ?? [], false, $frd['timezone'] ?? null, null);

        $this->save();
    }

    /**
     * @param array $frd
     * @param User  $user
     *
     * @throws \Exception
     */
    public function updateServices(Request $request): void {

		$frd = $request->only(['type_map_point', 'is_caption', 'is_all_similar_disabled', 'is_advertisement']);

        $this->update($frd);
    }

    /**
     * @param bool $isPublished
     */
    public function unpublishOrganization(bool $isPublished): void {
        if ($isPublished) {
            $isPublished = false;
        }

        $this->setPublished($isPublished);
        $this->save();
    }

    /**
     * @return BelongsTo
     */
    public function miniLogo(): BelongsTo {
        return $this->belongsTo(Image::class);
    }

    /**
     * @return Image|null
     */
    public function getMiniLogo(): ?Image {
        return $this->miniLogo;
    }

    /**
     * @return int|null
     */
    public function getMiniLogoId(): ?int {
        $miniLogo = $this->getMiniLogo();

        if (is_null($miniLogo)) {
            return null;
        }

        return $miniLogo->getKey();
    }

    /**
     * @return null|string
     */
    public function getMiniLogoLink(): ?string {
        $miniLogo = $this->getMiniLogo();

        if (is_null($miniLogo)) {
            return null;
        }

        return $miniLogo->getPublishPath();
    }

    /**
     * @return bool
     */
    public function hasMiniLogo(): bool {
        return (!is_null($this->getMiniLogo()));
    }

    /**
     * @param array $newMiniLogo
     *
     * @throws \Exception
     */
    public function updateMiniLogo(array $newMiniLogo): void {
        $oldMiniLogo = $this->getMiniLogo();

        if (!empty($newMiniLogo)) {
            $newMiniLogoId = $newMiniLogo['id'] ?? null;

            if (!is_null($oldMiniLogo)) {
                $this->deleteMiniLog($oldMiniLogo);
            }

            $newIcon = Image::whereKey($newMiniLogoId)->first();

            if (!is_null($newIcon)) {
                $this->images()->save($newIcon);
                $this->miniLogo()->associate($newIcon);
            }
        } else {
            if (!is_null($oldMiniLogo)) {
                $this->deleteMiniLog($oldMiniLogo);
            }
        }
    }

    /**
     * @param Image $oldMiniLogo
     *
     * @throws \Exception
     */
    public function deleteMiniLog(Image $oldMiniLogo): void {
        $oldMiniLogo->delete();
        $this->miniLogo()->dissociate();
    }

    /**
     * @return int|null
     */
    public function getTypeMapPoint(): ?int {
        return $this->{'type_map_point'};
    }

    /**
     * @param int $typeMapPoint
     */
    public function setTypeMapPoint(int $typeMapPoint): void {
        $this->{'type_map_point'} = $typeMapPoint;
    }

    /**
     * @param int  $typeMapPoint
     * @param User $user
     */
    public function updateTypeMapPoint(int $typeMapPoint, User $user): void {
        if ($user->isSuperAdministrator()) {
            $this->setTypeMapPoint($typeMapPoint);
        }
    }
}

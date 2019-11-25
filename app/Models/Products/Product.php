<?php
/**
 * Created by PhpStorm.
 * User: Pavel Onokhov
 * Date: 26.06.2019
 * Time: 15:37
 */

namespace App\Models\Products;


use App\Http\Resources\Products\AuditoryCollection;
use App\Http\Resources\Products\CategoryCollection;
use App\Http\Resources\Products\HolidayCollection;
use App\Http\Resources\Products\TagCollection;
use App\Models\Bookmarks\Bookmark;
use App\Models\Files\Image;
use App\Models\Organizations\Organization;
use App\Models\Organizations\Point;
use App\Models\Reviews\Review;
use App\Models\Users\User;
use App\Traits\ImagesTrait;
use App\Traits\SocialsTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Validator;

/**
 * App\Models\Products\Product
 *
 * @property int                                                                              $id
 * @property int|null                                                                         $organization_id
 * @property \Illuminate\Support\Carbon|null                                                  $start_at
 * @property \Illuminate\Support\Carbon|null                                                  $end_at
 * @property float|null                                                                       $origin_price
 * @property float|null                                                                       $value
 * @property string|null                                                                      $description
 * @property string|null                                                                      $conditions
 * @property \Illuminate\Support\Carbon|null                                                  $created_at
 * @property \Illuminate\Support\Carbon|null                                                  $updated_at
 * @property string|null                                                                      $name
 * @property string|null                                                                      $caption
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Files\Image[]          $images
 * @property-read \App\Models\Organizations\Organization|null                                 $organization
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Organizations\Point[]  $points
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Products\Tag[]         $tags
 * @property \Illuminate\Support\Carbon|null                                                  $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Products\Category[]    $categories
 * @property int|null                                                                         $creator_id
 * @property-read \App\Models\Users\User|null                                                 $creator
 * @property string|null                                                                      $short_description
 * @property int|null                                                                         $currency_id
 * @property int                                                                              $is_published
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Reviews\Review[]       $reviews
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Users\User[]           $usersFromWishlist
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Social\SocialAccount[] $socialAccounts
 * @property int                                                                              $views
 * @property-read \App\Models\Files\Image                                                     $cover
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Files\Image[]          $sliderImages
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Bookmarks\Bookmark[]   $bookmarks
 * @property int|null                                                                         $miniature_id
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Products\Auditory[]    $auditories
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Products\Holiday[]     $holidays
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products\Product newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products\Product newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products\Product query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products\Product whereConditions($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products\Product whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products\Product whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products\Product whereEndAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products\Product whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products\Product whereOrganizationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products\Product whereOriginPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products\Product whereStartAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products\Product whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products\Product whereValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products\Product filter($frd)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products\Product whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Products\Product onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products\Product whereDeletedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Products\Product withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Products\Product withoutTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products\Product whereCreatorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products\Product categoriesFilter($categories)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products\Product search($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products\Product tagsFilter($tags)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products\Product whereShortDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products\Product ordering($frd)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products\Product categoriesFilterOrdering($orderingDir)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products\Product searchOrdering($orderingDir)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products\Product tagsFilterOrdering($orderingDir)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products\Product organizationRating($orderingDir)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products\Product popularityOrdering($orderingDir)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products\Product tenDaysLeft($orderingDir)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products\Product whereCurrencyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products\Product whereIsPublished($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products\Product cityFilter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products\Product publicProducts()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products\Product productsByCoordinates(array $coordinates)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products\Product productsByPoint(int $pointId)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products\Product createdAtOrdering($orderingDir)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products\Product organizationProducts($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products\Product productsIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products\Product productsReviewsCountOrdering($orderingDir)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products\Product productsStartAtOrdering($orderingDir)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products\Product productsWhereIn($ids)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products\Product productsWith()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products\Product whereViews($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products\Product productsByUser($userId)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products\Product auditoriesFilter($auditories)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products\Product auditoriesFilterOrdering($orderingDir)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products\Product holidaysFilter($holidays)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products\Product holidaysFilterOrdering($orderingDir)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products\Product whereMiniatureId($value)
 * @method static bool|null restore()
 * @method static bool|null forceDelete()
 * @mixin \Eloquent
 */
class Product extends Model {
    use SoftDeletes;
    use ImagesTrait;
    use SocialsTrait;

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'caption',
        'organization_id',
        'main_category_id',
        'start_at',
        'end_at',
        'origin_price', //Изначальная цена
        'value',
        'currency_id', //id используемой валюты
        'description',
        'short_description',
        'conditions', //Условия
        'is_published', //Опубликована или нет акция
        'is_advertisement',
        'is_perpetual', // Бессрочная акция = 1
        'views' //Количество просмотров
    ];

    //Использование данного параметра означает,
    //что все точки акции отдаются в виде массива с их id
    const POINTS_IDS_TYPE = 1;

    //Использование данного параметра означает,
    //что все точки акции возвращаются объектами с некоторыми параметрами точек
    const POINTS_FOR_PUBLIC_PRODUCTS_INDEX_TYPE = 2;

    //Использование данного параметра означает,
    //что все точки акции возвращаются объектами со всеми возможными параметрами точек
    const POINTS_FOR_PUBLIC_PRODUCT_SHOW = 3;

    const PRODUCTS_TYPE_DEFAULT = 4;
    const PRODUCTS_TYPE_FOR_MAP = 5;

    //При использовании данного параметра отправляем собственное значение
    //параметра `is_published`
    const PRODUCT_PUBLISH_INDIVIDUAL = 6;

    //При использовании данного параметра отправляем значение
    //параметра `is_published` в зависимости от параметра `is_published` организации
    const PRODUCT_PUBLISH_WITH_ORGANIZATION = 7;

    /**
     * @var array
     */
    protected $eagerLoadingRelations = [
        'sliderImages',
        'organization',
        'organization.schedule',
        'points',
        'categories',
        'creator',
        'socialAccounts',
        'tags',
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
     * Количество акций по-умолчанию, которые будут выводиться при просмотре организации
     *
     * @var int
     */
    protected $perPageForPublicOrganizations = 6;

    /**
     * Кол-во акций по умолчанию, которые будут выводиться на карте
     *
     * @var int
     */
    protected $perPageForMap = 8;

    /**
     * Кол-во акций на главной странице по умолчанию (не для страниц управления)
     *
     * @var int
     */
    protected $perPageForPublic = 12;

    /**
     * @var array
     */
    protected $dates = ['start_at', 'end_at'];

    /**
     * @var array
     */
    protected static $rules = [
        'name'              => 'required|string|max:255',
        'caption'              => 'nullable|string|max:30',
        'description'       => 'nullable|string',
        'short_description' => 'required|string',
        'categories'            => 'required|array|min:1',
        'images'            => 'required|array|min:1',
        'points'            => 'required|array|min:1',
        'conditions'        => 'nullable|string',
        'start_at'          => 'required|date',
        'end_at'            => 'required|date|after_or_equal:start_at',
        'origin_price'      => 'nullable|integer|max:99999999.99',
    ];

    /**
     * @var array
     */
    protected static $messages = [
        'name.required'              => 'Укажите название акции',
        'name.max'              => 'Максимальная длинна 255 символов',
        'caption.max'              => 'Максимальная длинна 30 символов',
        'short_description.required' => 'Укажите краткое описание для акции',
        'start_at.required'          => 'Укажите время начала акции',
        'end_at.required'            => 'Укажите время окончания акции',
        'end_at.after'               => 'Дата окончания не может быть меньше даты начала',
        'images.required'            => 'Наличие хотя бы 1 изображения обязательно',
        'images.min'                 => 'Необходимо добавить минимум :min изображение',
        'categories.required'            => 'Наличие хотя бы 1 категории обязательно',
        'categories.min'                 => 'Необходимо добавить минимум :min категорию',
        'points.required'            => 'Наличие хотя бы 1 адреса обязательно',
        'points.min'                 => 'Необходимо указать минимум :min адрес',
        'origin_price.max'           => 'Начальная цена не может быть больше чем :max',
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
     * @return int
     */
    public function getPerPageForPublicOrganizations(): int {
        return $this->perPageForPublicOrganizations;
    }

    /**
     * @return int
     */
    public function getPerPageForPublic(): int {
        return $this->perPageForPublic;
    }

    /**
     * @return int
     */
    public function getPerPageForMap(): int {
        return $this->perPageForMap;
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
    public function getCaption(): ?string {
        return $this->caption;
    }

    /**
     * @param null|string $caption
     */
    public function setCaption(?string $caption): void {
        $this->caption = $caption;
    }

    /**
     * @return \Illuminate\Support\Carbon|null
     */
    public function getStartAt(): ?\Illuminate\Support\Carbon {
        return $this->start_at;
    }

    /**
     * @param null|string $start_at
     */
    public function setStartAt(?string $start_at): void {
        $this->start_at = $start_at;
    }

    /**
     * @return \Illuminate\Support\Carbon|null
     */
    public function getEndAt(): ?\Illuminate\Support\Carbon {
        return $this->end_at;
    }

    /**
     * @param null|string $end_at
     */
    public function setEndAt(?string $end_at): void {
        $this->end_at = $end_at;
    }

    /**
     * @return int|null
     */
    public function getViews(): ?int {
        return $this->{'views'};
    }

    /**
     * @param int $views
     */
    public function setViews(int $views): void {
        $this->{'views'} = $views;
    }

    /**
     *
     */
    public function incrementViews(): void {
        $this->views++;
    }

    /**
     * @return null|string
     */
    public function getTimeAction(): ?string {
        $startAt = $this->getStartAt();
        $endAt = $this->getEndAt();

        if (is_null($endAt)) {
            $result = 'С ' . $startAt->formatLocalized('%d %b %Y') . ' и бессрочно';
        } elseif ($startAt->year === $endAt->year) {
            $result = 'С ' . $startAt->formatLocalized('%d %b') . ' по ' . $endAt->formatLocalized('%d %b %Y');
        } else {
            $result = 'С ' . $startAt->formatLocalized('%d %b %Y') . ' по ' . $endAt->formatLocalized('%d %b %Y');
        }

        return $result;
    }

    /**
     * @return float|null
     */
    public function getOriginPrice(): ?float {
        return $this->origin_price;
    }

    /**
     * @param float|null $origin_price
     */
    public function setOriginPrice(?float $origin_price): void {
        $this->origin_price = $origin_price;
    }

    /**
     * @return float|null
     */
    public function getValue(): ?float {
        return $this->value;
    }

    /**
     * @param float|null $value
     */
    public function setValue(?float $value): void {
        $this->value = $value;
    }

    /**
     * @return bool
     */
    public function isPublished(): bool {
        $organization = $this->getOrganization();

        return ($this->getIsPublished() && $organization->isPublished());
    }

    /**
     * @return bool
     */
    public function isUnpublished(): bool {
        $organization = $this->getOrganization();

        return !($this->getIsPublished() && $organization->isPublished());
    }

    /**
     * @return bool|null
     */
    public function getIsPublished(): ?bool {
        return $this->{'is_published'};
    }

    /**
     * @param bool $isPublished
     */
    public function setPublished(bool $isPublished): void {
        $this->{'is_published'} = $isPublished;
    }

    /**
     * @return bool|null
     */
    public function getIsAdvertisement(): ?bool {
        return $this->{'is_advertisement'};
    }

    /**
     * @return bool|null
     */
    public function getIsPerpetual(): ?bool {
        return $this->{'is_perpetual'};
    }

    /**
     * @param bool $isAdvertisement
     */
    public function setIsAdvertisement(bool $isAdvertisement): void {
        $this->{'is_advertisement'} = $isAdvertisement;
    }

    /**
     * @param bool $isPerpetual
     */
    public function setIsPerpetual(bool $isPerpetual): void {
        $this->{'is_perpetual'} = $isPerpetual;
    }

    /**
     * @return int|null
     */
    public function getCurrencyId(): ?int {
        return $this->{'currency_id'};
    }

	/**
	 * @param int|null $value
	 */
	public function setCurrencyId(?int $value): void {
		$this->{'currency_id'} = $value;
	}

    /**
     * @return int|null
     */
    public function getMainCategoryId(): ?int {
        return $this->{'main_category_id'};
    }

	/**
	 * @param int|null $value
	 */
	public function setMainCategoryId(?int $value): void {
		$this->{'main_category_id'} = $value;
	}

    /**
     * @return null|string
     */
    public function getDescription(): ?string {
        return $this->description;
    }

    /**
     * @param null|string $description
     */
    public function setDescription(?string $description): void {
        $this->description = $description;
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
     * @return null|string
     */
    public function getConditions(): ?string {
        return $this->conditions;
    }

    /**
     * @param null|string $conditions
     */
    public function setConditions(?string $conditions): void {
        $this->conditions = $conditions;
    }

    /**
     * @return BelongsToMany
     */
    public function tags(): BelongsToMany {
        return $this->belongsToMany(Tag::class, 'tag_product', 'product_id', 'tag_id');
    }

    /**
     * @return Collection
     */
    public function getTags(): Collection {
        return $this->tags;
    }

    /**
     * @return TagCollection
     */
    public function getTagsShortInfo(): TagCollection {
        $tags = $this->getTags();

        return new TagCollection($tags);
    }

    /**
     * @return array
     */
    public function getTagsList(): array {
        return $this->tags()->orderBy('name', 'ASC')->pluck('tags.name', 'tags.id')->toArray();
    }

    /**
     * @return BelongsToMany
     */
    public function points(): BelongsToMany {
        return $this->belongsToMany(Point::class, 'product_point', 'product_id', 'point_id');
    }

    /**
     * @return Collection
     */
    public function getPointsWithDifferentTime(): Collection {
        return $this->getOrganization()->getPointsWithDifferentTime();
    }

    /**
     * @return Collection
     */
    public function getPoints(): Collection {
        return $this->points;
    }

    /**
     * @param array $frd
     *
     * @return array
     */
    public function getPointsIds(array $frd = []): array {
        if (!empty($frd)) {
            return $this->points()->filter($frd)->get()->keyBy('id')->keys()->toArray();
        } else {
            return $this->getPoints()->keyBy('id')->keys()->toArray();
        }
    }

    /**
     * @param array $frd
     *
     * @return array
     */
    public function getPointsForPublicProductShow(array $frd = []): array {
        if (!empty($frd)) {
            $points = $this->points()->filter($frd)->get();
        } else {
            $points = $this->getPoints();
        }
        $result = [];

        foreach ($points as $point) {
            /**
             * @var Point $point
             */
            $result[] = [
                'id'                => $point->getKey(),
                'name'              => $point->getName(),
                'extension'              => $point->getExtension(),
                'latitude'          => $point->getLatitude(),
                'longitude'         => $point->getLongitude(),
                'street'            => $point->getStreet(),
                'building'          => $point->getBuilding(),
                'full_street'       => $point->getFullStreet(),
                'city_kladr_id'     => $point->getCityKladrId(),
				'metro_line_color'     => $point->getColorMetroStations(),
				'metro_distance'     => $point->getMetroStationDistance(),
				'metro_station_name'     => $point->getNameMetroStation(),
				'metro_line_name'     => $point->getNameMetroLine(),
                'phone'             => $point->getPhone(),
                'email'             => $point->getEmail(),
                'operationModeText' => $point->getScheduleText(),
            ];
        }

        return $result;
    }

    /**
     * @param array $frd
     *
     * @return array
     */
    public function getPointsForPublicProductsIndex(array $frd = []): array {
        if (!empty($frd)) {
            $points = $this->points()->filter($frd)->get();
        } else {
            $points = $this->getPoints();
        }
        $result = [];

        foreach ($points as $point) {
            /**
             * @var Point $point
             */
            $result[] = [
                'id'          => $point->getKey(),
                'name'        => $point->getName(),
                'full_street' => $point->getFullStreet(),
                'latitude'    => $point->getLatitude(),
                'longitude'   => $point->getLongitude(),
                'street'      => $point->getStreet(),
				'metro_line_color'     => $point->getColorMetroStations(),
				'metro_distance'     => $point->getMetroStationDistance(),
				'metro_station_name'     => $point->getNameMetroStation(),
				'metro_line_name'     => $point->getNameMetroLine(),
            ];
        }

        return $result;
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
                    ->where('public_path', 'like', '%' . 'product_image' . '%')
                    ->where('file_parent_id', null);
    }

    /**
     * @param int $index
     *
     * @return Image|null
     */
    public function getImage(int $index): ?Image {
        if (!isset($this->getImages()[$index])) {
            return null;
        }

        return $this->getImages()[$index];
    }

    /**
     * @return int
     */
    public function countImages(): int {
        return $this->getSliderImages()->count();
    }

    /**
     * @return array
     */
    public function getImagesLinks(): array {
        $images = $this->getSliderImages();
        $result = [];

        foreach ($images as $image) {
            /**
             * @var Image $image
             */
            $imageLinks = [];
            $imageLinks['src'] = $image->getPublishPath();
            $imageLinks['id'] = $image->getKey();
            $children = $image->getChildren();

            foreach ($children as $child) {
                /**
                 * @var Image $child
                 */
                $imageWidth = $child->getWidth();
                $imageLinks[$imageWidth]['src'] = $child->getPublishPath();
                $imageLinks[$imageWidth]['id'] = $child->getKey();
            }

            $result[] = $imageLinks;
        }

        return $result;
    }

    /**
     * @return BelongsTo
     */
    public function organization(): BelongsTo {
        return $this->belongsTo(Organization::class);
    }

    /**
     * @return BelongsTo
     */
    public function mainCategory(): BelongsTo {
        return $this->belongsTo(Category::class, 'main_category_id');
    }

    /**
     * @return Organization
     */
    public function getOrganization(): Organization {
        return $this->organization;
    }

    /**
     * @return Organization
     */
    public function getMainCategory():? Category {
        return $this->mainCategory;
    }

    /**
     * @return null|string
     */
    public function getOrganizationAvatarLink(): ?string {
        return $this->getOrganization()->getAvatarLink();
    }

    /**
     * @return null|string
     */
    public function getOrganizationLink(): ?string {
        return $this->getOrganization()->getLink();
    }

    /**
     * @return int
     */
    public function getOrganizationId(): int {
        return $this->getOrganization()->getKey();
    }

    /**
     * @return null|string
     */
    public function getOrganizationAvatarColor(): ?string {
        return $this->getOrganization()->getAvatarColor();
    }

    /**
     * @return null|bool
     */
    public function getOrganizationIsCaption(): ?bool {
        return $this->getOrganization()->isCaption();
    }

    /**
     * @return null|string
     */
    public function getOrganizationPhone(): ?string {
        return $this->getOrganization()->getPhone();
    }

    /**
     * @return null|string
     */
    public function getOrganizationEmail(): ?string {
        return $this->getOrganization()->getEmail();
    }

    /**
     * @return null|bool
     */
    public function getOrganizationIsAdvertisement(): ?bool {
        return $this->getOrganization()->isAdvertisement();
    }

    /**
     * @return null|bool
     */
    public function isAllSimilar(): ?bool {
        return !$this->getOrganization()->isAllSimilarDisabled();
    }

    /**
     * @return string
     */
    public function getDiscountString(): string {
        if (is_null($this->getOriginPrice())) {
            return $this->getValue() . ' %';
        }

        $originPrice = $this->getOriginPrice();
        $value = $this->getValue();
        $discountPrice = $originPrice * $value / 100;
        $priceWithDiscount = $originPrice - ($discountPrice);

        return $priceWithDiscount . ' &#8381;, экономия ' . $discountPrice . ' &#8381;';
    }

    /**
     * @return BelongsToMany
     */
    public function categories(): BelongsToMany {
        return $this->belongsToMany(Category::class, 'product_category', 'product_id', 'category_id');
    }

    /**
     * @return Collection
     */
    public function getCategories(): Collection {
        return $this->categories;
    }

    /**
     * @return Category|null
     */
    public function getFirstCategoryWithImage(): ?Category {
        $categories = $this->getCategories();

        if (empty($categories)) {
            return null;
        }

        foreach ($categories as $category) {
            /**
             * @var Category $category
             */
            if ($category->hasImage()) {
                return $category;
            }
        }

        return $categories->first();
    }

    /**
     * @return Category|null
     */
    public function getMainOrFirstCategoryWithEmptyImage(): ?Category {
        $categories = $this->getCategories();

        if (empty($categories)) {
            return null;
        }

        if($this->getMainCategoryId() !== null){
			$category = $categories->find($this->getMainCategoryId());
			if (isset($category) && $category->hasEmptyImage()) {
				return $category;
			}
		}

        foreach ($categories as $category) {
            /**
             * @var Category $category
             */
            if ($category->hasEmptyImage()) {
                return $category;
            }
        }

        return null;
    }

    /**
     * @return CategoryCollection
     */
    public function getCategoriesShortInfo(): CategoryCollection {
        $categories = $this->getCategories();

        return new CategoryCollection($categories);
    }

    /**
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopePublicProducts(Builder $query): Builder {
        return $query->where(function (Builder $query) {
            $query->whereHas('organization', function (Builder $query) {
                $query->where('is_published', true);
            })->where('is_published', true);
        });
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
                  ->orWhere('short_description', 'like', '%' . $value . '%')
                  ->orWhereHas('tags', function (Builder $query) use ($value) {
                      $query->where('name', 'like', '%' . $value . '%');
                  })
                  ->orWhereHas('organization', function (Builder $query) use ($value) {
                      $query->where('name', 'like', '%' . $value . '%');
                  });
        });
    }

    /**
     * @param Builder $query
     * @param string  $orderingDir
     *
     * @return Builder
     */
    public function scopeSearchOrdering(Builder $query, string $orderingDir): Builder {
        return $query->orderBy('name', $orderingDir);
    }

    /**
     * @param Builder $query
     * @param array   $tags
     *
     * @return Builder
     */
    public function scopeTagsFilter(Builder $query, array $tags): Builder {
        $resultArray = [];
        foreach ($tags as $each_number) {
            if (is_null($each_number)) {
                continue;
            }

            $resultArray[] = (int)$each_number;
        }

        if (count($resultArray) !== 0) {
            $query->where(function (Builder $query) use ($resultArray) {
                $query->whereHas('tags', function (Builder $query) use ($resultArray) {
                    $query->whereIn('tags.id', $resultArray)->orderBy('tags.id');
                });
            });
        }

        return $query;
    }

    /**
     * @param Builder $query
     * @param string  $orderingDir
     *
     * @return Builder
     */
    public function scopeTagsFilterOrdering(Builder $query, string $orderingDir): Builder {
        return $query->select('products.*')
                     ->join('tag_product', 'products.id', '=', 'tag_product.product_id')
                     ->orderBy('product_category.tag_id', $orderingDir);
    }

    /**
     * @param Builder $query
     * @param array   $categories
     *
     * @return Builder
     */
    public function scopeCategoriesFilter(Builder $query, array $categories): Builder {
        $resultArray = [];
        foreach ($categories as $each_number) {
            if (is_null($each_number)) {
                continue;
            }

            $resultArray[] = (int)$each_number;
        }

        if (!empty($resultArray)) {
            $query->where(function (Builder $query) use ($resultArray) {
                $query->whereHas('categories', function (Builder $query) use ($resultArray) {
                    $query->whereIn('categories.id', $resultArray);
                });
            });
        }

        return $query;
    }

    /**
     * @param Builder $query
     * @param string  $orderingDir
     *
     * @return Builder
     */
    public function scopeCategoriesFilterOrdering(Builder $query, string $orderingDir): Builder {
        return $query->select('products.*')
                     ->join('product_category', 'products.id', '=', 'product_category.product_id')
                     ->orderBy('product_category.category_id', $orderingDir);
    }

    /**
     * @param Builder $query
     * @param array   $auditories
     *
     * @return Builder
     */
    public function scopeAuditoriesFilter(Builder $query, array $auditories): Builder {
        $resultArray = [];
        foreach ($auditories as $each_number) {
            if (is_null($each_number)) {
                continue;
            }

            $resultArray[] = (int)$each_number;
        }

        if (!empty($resultArray)) {
            $query->where(function (Builder $query) use ($resultArray) {
                $query->whereHas('auditories', function (Builder $query) use ($resultArray) {
                    $query->whereIn('auditories.id', $resultArray);
                });
            });
        }

        return $query;
    }

    /**
     * @param Builder $query
     * @param string  $orderingDir
     *
     * @return Builder
     */
    public function scopeAuditoriesFilterOrdering(Builder $query, string $orderingDir): Builder {
        return $query->select('products.*')
                     ->join('auditory_product', 'products.id', '=', 'auditory_product.product_id')
                     ->orderBy('auditory_product.auditory_id', $orderingDir);
    }

    /**
     * @param Builder $query
     * @param array   $holidays
     *
     * @return Builder
     */
    public function scopeHolidaysFilter(Builder $query, array $holidays): Builder {
        $resultArray = [];
        foreach ($holidays as $each_number) {
            if (is_null($each_number)) {
                continue;
            }

            $resultArray[] = (int)$each_number;
        }

        if (!empty($resultArray)) {
            $query->where(function (Builder $query) use ($resultArray) {
                $query->whereHas('holidays', function (Builder $query) use ($resultArray) {
                    $query->whereIn('holidays.id', $resultArray);
                });
            });
        }

        return $query;
    }

    /**
     * @param Builder $query
     * @param string  $orderingDir
     *
     * @return Builder
     */
    public function scopeHolidaysFilterOrdering(Builder $query, string $orderingDir): Builder {
        return $query->select('products.*')
                     ->join('holiday_product', 'products.id', '=', 'holiday_product.product_id')
                     ->orderBy('holiday_product.holiday_id', $orderingDir);
    }

    /**
     * @param Builder $query
     * @param string  $orderingDir
     *
     * @return Builder
     */
    public function scopePopularityOrdering(Builder $query, string $orderingDir): Builder {
        return $query->orderBy('views', $orderingDir);
    }

    /**
     * @param Builder $query
     * @param string  $orderingDir
     *
     * @return Builder
     */
    public function scopeTenDaysLeft(Builder $query, string $orderingDir): Builder {
        $now = Carbon::now();

        return $query->whereDate('start_at', '<', $now)
                     ->whereDate('end_at', '>', $now)
                     ->whereDate('end_at', '<', $now->addDays(10))
                     ->orderBy('end_at', $orderingDir);
    }

    /**
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeOrganizationRating(Builder $query, string $orderingDir): Builder {
        return $query->join('organizations', 'products.organization_id', '=', 'organizations.id')
                     ->orderBy('organizations.rating', $orderingDir)
                     ->select('products.*');
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
     * @param Builder $query
     * @param int     $value
     *
     * @return Builder
     */
    public function scopeOrganizationProducts(Builder $query, int $value): Builder {
        return $query->where(function (Builder $query) use ($value) {
            $query->whereHas('organization', function (Builder $query) use ($value) {
                $query->whereKey($value);
            });
        });
    }

    /**
     * @param Builder $query
     * @param array   $ids
     *
     * @return Builder
     */
    public function scopeProductsWhereIn(Builder $query, array $ids): Builder {
        return $query->where(function (Builder $query) use ($ids) {
            $query->whereIn('id', $ids);
        });
    }

    /**
     * @param Builder $query
     * @param array   $ids
     *
     * @return Builder
     */
    public function scopeProductsWhereNotIn(Builder $query, array $ids): Builder {
        return $query->where(function (Builder $query) use ($ids) {
            $query->whereNotIn('id', $ids);
        });
    }

    /**
     * @param Builder $query
     * @param bool    $value
     *
     * @return Builder
     */
    public function scopeProductsIsActive(Builder $query, bool $value): Builder {
        if ($value) {
            return $query->where(function (Builder $query) {
				$query->whereDate('end_at', '>=', Carbon::now())->orWhere('is_perpetual', true);
			});
        } else {
			return $query->where(function (Builder $query) {
				$query->whereDate('end_at', '<', Carbon::now())->where('is_perpetual', false);
			});
        }
    }

    /**
     * @param Builder $query
     * @param bool    $value
     *
     * @return Builder
     */
    public function scopeProductsIsAdvertisement(Builder $query): Builder {
		return $query->where(function (Builder $query) {
			$query->whereHas('organization', function (Builder $query) {
				$query->where('is_advertisement', true);
			})->where('is_advertisement', true);
		});
    }

    /**
     * @param Builder $query
     * @param string  $orderingDir
     *
     * @return Builder
     */
    public function scopeProductsStartAtOrdering(Builder $query, string $orderingDir): Builder {
        return $query->orderBy('start_at', $orderingDir);
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
     * @param string  $orderingDir
     *
     * @return Builder
     */
    public function scopeProductsReviewsCountOrdering(Builder $query, string $orderingDir): Builder {
        return $query->withCount('reviews')->orderBy('reviews_count', $orderingDir);
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
                case 'search':
                    {
                        $query->search($value);
                    }
                    break;
                case 'tags' :
                    {
                        $query->tagsFilter($value);
                    }
                    break;
                case 'auditories' :
                    {
                        $query->auditoriesFilter($value);
                    }
                    break;
                case 'holidays' :
                    {
                        $query->holidaysFilter($value);
                    }
                    break;
                case 'categories' :
                    {
                        $query->categoriesFilter($value);
                    }
                    break;
                case 'city_id':
                    {
                    	/**
						 * На будущее, когда будет учитываться город
						 */
//                        $query->cityFilter($value);
                    }
                    break;
                case 'organization_id':
                    {
                        $query->organizationProducts($value);
                    }
                    break;
                case 'is_active':
                    {
                        $query->productsIsActive($value);
                    }
                    break;
                case 'is_advertisement':
                    {
                        $query->productsIsAdvertisement();
                    }
                    break;
                case 'whereIn':
                    {
                        $query->productsWhereIn($value);
                    }
                    break;
                case 'whereNotIn':
                    {
						$query->productsWhereNotIn($value);
                    }
                    break;
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

		if ($ordering === 'random') {
			$query->inRandomOrder();
		} elseif ($ordering === 'created_at') {
            $query->createdAtOrdering($orderingDir);
        } elseif ($ordering === 'search' || $ordering === 'name') {
            $query->searchOrdering($orderingDir);
        } elseif ($ordering === 'tags') {
            $query->tagsFilterOrdering($orderingDir);
        } elseif ($ordering === 'categories') {
            $query->categoriesFilterOrdering($orderingDir);
        } elseif ($ordering === 'auditories') {
            $query->auditoriesFilterOrdering($orderingDir);
        } elseif ($ordering === 'holidays') {
            $query->holidaysFilterOrdering($orderingDir);
        } elseif ($ordering === 'popularity') {
            $query->popularityOrdering($orderingDir);
        } elseif ($ordering === '10_days_left') {
            $query->tenDaysLeft($orderingDir);
        } elseif ($ordering === 'organization_rating') {
            $query->organizationRating($orderingDir);
        } elseif ($ordering === 'start_at') {
            $query->productsStartAtOrdering($orderingDir);
        } elseif ($ordering === 'reviews_count') {
            $query->productsReviewsCountOrdering($orderingDir);
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
     * @return User|null
     */
    public function getCreator(): ?User {
        return $this->creator;
    }

    /**
     * @return null|string
     */
    public function getCreatorName(): ?string {
        $creator = $this->getCreator();
        if (is_null($creator)) {
            return null;
        }

        return $creator->getName();
    }

    /**
     * @return BelongsToMany
     */
    public function usersFromWishlist(): BelongsToMany {
        return $this->belongsToMany(User::class, 'product_user_wishlist', 'product_id', 'user_id');
    }

    /**
     * @return Collection
     */
    public function getUsersFromWishlist(): Collection {
        return $this->usersFromWishlist;
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
     * @param int $userId
     *
     * @return bool
     */
    public function isUserLeftReview(int $userId): bool {
        return $this->reviews()->where('user_id', $userId)->exists();
    }

    /**
     * @return null|string
     */
    public function getScheduleText(): ?string {
        return $this->getOrganization()->getScheduleText();
    }

    /**
     * @param Builder $query
     * @param array   $coordinates
     *
     * @return Builder
     */
    public function scopeProductsByCoordinates(Builder $query, array $coordinates): Builder {
        return $query->whereHas('points', function (Builder $query) use ($coordinates) {
            $query->pointsByCoordinates($coordinates);
        });
    }

    public function getEagerLoadingRelations(): array {
        return $this->eagerLoadingRelations;
    }

    /**
     * @param Builder $query
     * @param int     $userId
     *
     * @return Builder
     */
    public function scopeProductsByUser(Builder $query, int $userId): Builder {
        return $query->whereHas('organization', function (Builder $query) use ($userId) {
            $query->organizationsByUser($userId);
        });
    }

    /**
     * @return MorphMany
     */
    public function bookmarks(): MorphMany {
        return $this->morphMany(Bookmark::class, 'bookmarkable');
    }

    /**
     * @return Collection
     */
    public function getBookmarks(): Collection {
        return $this->bookmarks;
    }

    /**
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeProductsWith(Builder $query): Builder {
        return $query->with($this->getEagerLoadingRelations());
    }

    /**
     * @return BelongsToMany
     */
    public function auditories(): BelongsToMany {
        return $this->belongsToMany(Auditory::class, 'auditory_product', 'product_id', 'auditory_id');
    }

    /**
     * @return Collection
     */
    public function getAuditories(): Collection {
        return $this->auditories;
    }

    /**
     * @return AuditoryCollection
     */
    public function getAuditoriesShortInfo(): AuditoryCollection {
        $auditories = $this->getAuditories();

        return new AuditoryCollection($auditories);
    }

    /**
     * @return array
     */
    public function getAuditoriesList(): array {
        return $this->auditories()->orderBy('name', 'ASC')->pluck('auditories', 'auditories.id')->toArray();
    }

    /**
     * @return BelongsToMany
     */
    public function holidays(): BelongsToMany {
        return $this->belongsToMany(Holiday::class, 'holiday_product', 'product_id', 'holiday_id');
    }

    /**
     * @return Collection
     */
    public function getHolidays(): Collection {
        return $this->holidays;
    }

    /**
     * @return HolidayCollection
     */
    public function getHolidaysShortInfo(): HolidayCollection {
        $holidays = $this->getHolidays();

        return new HolidayCollection($holidays);
    }

    /**
     * @return array
     */
    public function getHolidaysList(): array {
        return $this->holidays()->orderBy('name', 'ASC')->pluck('holidays', 'holidays.id')->toArray();
    }

    /**
     * @param array $points
     */
    public function updatePoints(array $points): void {
        //Точки, что уже добавлены к акции
        $chosenPoints = $this->getPoints()->keyBy('id')->keys()->toArray();
        $addPoints = array_diff($points, $chosenPoints);
        $removePoints = array_diff($chosenPoints, $points);
        $this->points()->detach($removePoints);
        $this->points()->attach($addPoints);
    }

    /**
     * @param array $auditories
     */
    public function updateAuditories(array $auditories): void {
        //Аудитория, что уже добавлена к акции
        $chosenAuditories = $this->getAuditories()->keyBy('id');

        //Массив для хранения аудитории, пришедшей на сохранение
        $addAuditories = [];

        if (!empty($auditories)) {
            foreach ($auditories as $auditory) {
                $auditoryId = $auditory['id'];
                $addAuditories[] = $auditoryId;

                //Если аудитория не была добавлена к акции ранее, то добавляем
                if (!isset($chosenAuditories[$auditoryId])) {
                    $this->auditories()->attach($auditoryId);
                }
            }

            //Сравнение массивов ранее добавленной аудитории и той, что пришла на сохранение.
            //Сравнение проводится для того, чтобы удалить неиспользуемые аудитории у акций
            $removeAuditories = array_diff($chosenAuditories->keys()->toArray(), $addAuditories);
            $this->auditories()->detach($removeAuditories);
        } else {
            $this->auditories()->detach($chosenAuditories);
        }
    }

    /**
     * @param array $holidays
     */
    public function updateHolidays(array $holidays): void {
        //Праздники, что уже добавлены к акции
        $chosenHolidays = $this->getHolidays()->keyBy('id');

        //Массив для хранения праздников, пришедших на сохранение
        $addHolidays = [];

        if (!empty($holidays)) {
            foreach ($holidays as $holiday) {
                $holidayId = $holiday['id'];
                $addHolidays[] = $holidayId;

                //Если праздники не были добавлены к акции ранее, то добавляем
                if (!isset($chosenHolidays[$holidayId])) {
                    $this->holidays()->attach($holidayId);
                }
            }

            //Сравнение массивов ранее добавленных праздников и тех, что пришли на сохранение.
            //Сравнение проводится для того, чтобы удалить неиспользуемые праздники у акций
            $removeHolidays = array_diff($chosenHolidays->keys()->toArray(), $addHolidays);
            $this->holidays()->detach($removeHolidays);
        } else {
            $this->holidays()->detach($chosenHolidays);
        }
    }

    /**
     * @param array $tags
     */
    public function updateTags(array $tags): void {
        //Теги, что уже добавлены к акции
        $chosenTags = $this->getTags()->keyBy('id');

        //Массив для хранения тегов, пришедших на сохранение
        $addTags = [];

        if (!empty($tags)) {
            foreach ($tags as $tag) {
                $tagId = $tag['id'];
                $addTags[] = $tagId;

                //Если теги не были добавлены к акции ранее, то добавляем
                if (!isset($chosenTags[$tagId])) {
                    $this->tags()->attach($tagId);
                }
            }

            //Сравнение массивов ранее добавленных тегов и тех, что пришли на сохранение.
            //Сравнение проводится для того, чтобы удалить неиспользуемые теги у акций
            $removeTags = array_diff($chosenTags->keys()->toArray(), $addTags);
            $this->tags()->detach($removeTags);
        } else {
            $this->tags()->detach($chosenTags);
        }
    }

    /**
     * @param array $categories
     */
    public function updateCategories(array $categories): void {
        //Категории, что уже добавлены к акции
        $chosenCategories = $this->getCategories()->keyBy('id');

        //Массив для хранения категорий, пришедших на сохранение
        $addCategories = [];

        if (!empty($categories)) {
            foreach ($categories as $category) {
                $categoryId = $category['id'];
                $addCategories[] = $categoryId;

                //Если категории не были добавлены к акции ранее, то добавляем
                if (!isset($chosenCategories[$categoryId])) {
                    $this->categories()->attach($categoryId);
                }
            }

            //Сравнение массивов ранее добавленных категорий и тех, что пришли на сохранение.
            //Сравнение проводится для того, чтобы удалить неиспользуемые категории у акций
            $removeCategories = array_diff($chosenCategories->keys()->toArray(), $addCategories);
            $this->categories()->detach($removeCategories);
        } else {
            $this->categories()->detach($chosenCategories);
        }
    }

    /**
     * @param array $frd
     *
     * @throws \Exception
     */
    public function updateProduct(array $frd): void {
        $this->setName($frd['name']);
        if(isset($frd['caption'])){
			$this->setCaption($frd['caption']);
		}
        if(isset($frd['is_advertisement'])){
			$this->setIsAdvertisement($frd['is_advertisement']);
		}
        if(isset($frd['is_perpetual'])){
			$this->setIsPerpetual($frd['is_perpetual']);
		}
        $this->setMainCategoryId($frd['main_category_id'] ?? null);
        $this->setDescription($frd['description'] ?? null);
        $this->setShortDescription($frd['short_description'] ?? null);
        $this->setConditions($frd['conditions'] ?? null);
        $this->setStartAt($frd['start_at']);
        $this->setEndAt($frd['end_at']);
        $this->setOriginPrice($frd['origin_price'] ?? null);
        $this->setValue($frd['value'] ?? null);
        $this->setCurrencyId($frd['currency_id']);
        $this->setPublished($frd['is_published']);

        $this->updatePoints($frd['points']);
        $this->updateAuditories($frd['auditories'] ?? []);
        $this->updateHolidays($frd['holidays'] ?? []);
        $this->updateTags($frd['tags'] ?? []);
        $this->updateCategories($frd['categories'] ?? []);
        $this->updateSliderImages($frd['images']);
        $this->updateSocialAccounts($frd['socials'] ?? []);

        $this->save();
    }

    /**
     * @return array
     */
    public function getIconImageForMap(): array {
        $organization = $this->getOrganization();
        $organizationTypeMapPoint = $organization->getTypeMapPoint();

        if ($organizationTypeMapPoint === Organization::TYPE_MAP_POINT_MINI_LOGO && $organization->hasMiniLogo()) {
            return [$organization->getMiniLogoLink(), Organization::TYPE_MAP_POINT_MINI_LOGO, null];
        } else {
            $category = $this->getMainOrFirstCategoryWithEmptyImage();

            if (!is_null($category)) {
                return [$category->getEmptyImageLink(), Organization::TYPE_MAP_POINT_CATEGORY, $category->getColor()];
            } else {
                return [null, Organization::TYPE_MAP_POINT_CATEGORY, null];
            }
        }
    }

    /**
     * @param Builder $query
     * @param int     $pointId
     *
     * @return Builder
     */
    public function scopeProductsByPoint(Builder $query, int $pointId): Builder {
        return $query->whereHas('points', function (Builder $query) use ($pointId){
            $query->whereKey($pointId);
        });
    }
}

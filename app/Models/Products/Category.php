<?php
/**
 * Created by PhpStorm.
 * User: Pavel Onokhov
 * Date: 27.06.2019
 * Time: 22:49
 */

namespace App\Models\Products;


use App\Models\Files\Image;
use App\Models\Articles\Article;
use App\Traits\ImagesTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

/**
 * App\Models\Products\Category
 *
 * @property int                                                                          $id
 * @property string                                                                       $name
 * @property string                                                                       $color
 * @property int|null                                                                     $ordering
 * @property int|null                                                                     $image_id
 * @property \Illuminate\Support\Carbon|null                                              $created_at
 * @property \Illuminate\Support\Carbon|null                                              $updated_at
 * @property int|null                                                                     $empty_image_id
 * @property-read \App\Models\Files\Image|null                                            $emptyImage
 * @property int                                                                          $for_blog
 * @property int                                                                          $for_products
 * @property int                                                                          $is_favorite
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Articles\Article[] $articles
 * @property-read \App\Models\Files\Image|null                                            $image
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Products\Product[] $products
 * @property int|null                                                                     $active_image_id
 * @property int|null                                                                     $business_image_id
 * @property int|null                                                                     $business_active_image_id
 * @property-read \App\Models\Files\Image|null                                            $activeImage
 * @property-read \App\Models\Files\Image|null                                            $businessActiveImage
 * @property-read \App\Models\Files\Image|null                                            $businessImage
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products\Category whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products\Category whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products\Category whereImageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products\Category whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products\Category whereOrdering($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products\Category whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products\Category filter($frd)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products\Category ordering($frd)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products\Category search($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products\Category searchOrdering($orderingDir)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products\Category whereActiveImageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products\Category whereBusinessActiveImageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products\Category whereBusinessImageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products\Category categoriesFavorite($isFavorite)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products\Category categoriesForBlog($forBlog)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products\Category categoriesForProducts($forProducts)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products\Category categoriesOrWhereIn($ids)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products\Category whereForBlog($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products\Category whereForProducts($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products\Category whereIsFavorite($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products\Category whereEmptyImageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products\Category newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products\Category newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products\Category query()
 * @mixin \Eloquent
 */
class Category extends Model {
    use ImagesTrait;

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'ordering',
        'image_id',
        'empty_image_id',
        'color',
        'active_image_id',
        'business_image_id',
        'business_active_image_id',
        'for_blog', //Категория относится к блогам или нет
        'for_products', //Категория относится к акциям или нет
        'is_favorite' //Является ли категория популярной/избранной
    ];

    const CATEGORIES_DEFAULT               = 1;
    const CATEGORIES_FOR_MAP               = 2;
    const CATEGORY_ID_FOR_DEFAULT_MAP_ICON = 51;

    /**
     * @var string
     */
    protected $defaultOrdering = 'ordering';

    /**
     * @var string
     */
    protected $defaultOrderingDir = 'DESC';

    /**
     * Количество избранных категорий для блога, которое отдается по-умолчанию
     * @var int
     */
    protected $perPageForFavoriteBlog = 11;

    /**
     * Количество категорий по-умолчанию для админки admin.skidvis.ru
     * @var int
     */
    protected $perPageForAdminPanel = 30;

    /**
     * @return int
     */
    public function getPerPageForFavoriteBlog(): int {
        return $this->perPageForFavoriteBlog;
    }

    /**
     * @return int
     */
    public function getPerPageForAdminPanel(): int {
        return $this->perPageForAdminPanel;
    }

    /**
     * @return string
     */
    public function getName(): string {
        //return mb_convert_case($this->name, MB_CASE_TITLE_SIMPLE);
        return $this->name;
    }

    /**
     * @return string
     */
    public function getColor():? string {
        return $this->color??'#00C2FF';
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void {
        $this->name = $name;
    }

    /**
     * @param string $color
     */
    public function setColor(string $color): void {
        $this->color = $color;
    }

    /**
     * @return int|null
     */
    public function getOrdering(): ?int {
        return $this->ordering;
    }

    /**
     * @param int|null $ordering
     */
    public function setOrdering(?int $ordering): void {
        $this->ordering = $ordering;
    }

    /**
     * @return BelongsTo
     */
    public function image(): BelongsTo {
        return $this->belongsTo(Image::class);
    }

    /**
     * @return Image|null
     */
    public function getImage(): ?Image {
        return $this->image;
    }

    /**
     * @return bool
     */
    public function hasImage(): bool {
        return (!is_null($this->getImage()));
    }

    /**
     * @return null|string
     */
    public function getImageLink(): ?string {
        if (!$this->hasImage()) {
            return null;
        }

        return $this->getImage()->getPublishPath();
    }

    /**
     * @return BelongsToMany
     */
    public function products(): BelongsToMany {
        return $this->belongsToMany(Product::class, 'product_category', 'category_id', 'product_id');
    }

    /**
     * @return Collection
     */
    public function getProducts(): Collection {
        return $this->products;
    }

    /**
     * @return bool|null
     */
    public function forProducts(): ?bool {
        return $this->{'for_products'};
    }

    /**
     * @param bool $forProducts
     */
    public function setForProducts(bool $forProducts): void {
        $this->{'for_products'} = $forProducts;
    }

    /**
     * @return bool|null
     */
    public function forBlog(): ?bool {
        return $this->{'for_blog'};
    }

    /**
     * @param bool $forBlog
     */
    public function setForBlog(bool $forBlog): void {
        $this->{'for_blog'} = $forBlog;
    }

    /**
     * @return bool|null
     */
    public function isFavorite(): ?bool {
        return $this->{'is_favorite'};
    }

    /**
     * @param bool $isFavorite
     */
    public function setIsFavorite(bool $isFavorite): void {
        $this->{'is_favorite'} = $isFavorite;
    }

    /**
     * @return array
     */
    public static function getAllCategoriesList(): array {
        return self::orderBy('name', 'ASC')->pluck('categories.name', 'categories.id')->toArray();
    }

    /**
     * @return array
     */
    public static function getAllCategoriesProductsList(): array {
        return self::categoriesForProducts(true)
                   ->orderBy('name', 'ASC')
                   ->pluck('categories.name', 'categories.id')
                   ->toArray();
    }

    /**
     * @return array
     */
    public static function getAllCategoriesBlogList(): array {
        return self::categoriesForBlog(true)
                   ->orderBy('name', 'ASC')
                   ->pluck('categories.name', 'categories.id')
                   ->toArray();
    }

    /**
     * @param Builder $query
     * @param array   $ids
     *
     * @return Builder
     */
    public function scopeCategoriesOrWhereIn(Builder $query, array $ids): Builder {
        return $query->orWhereIn('id', $ids);
    }

    /**
     * @param Builder $query
     * @param bool    $isFavorite
     *
     * @return Builder
     */
    public function scopeCategoriesFavorite(Builder $query, bool $isFavorite): Builder {
        return $query->where('is_favorite', $isFavorite);
    }

    /**
     * @param Builder $query
     * @param bool    $forProducts
     *
     * @return Builder
     */
    public function scopeCategoriesForProducts(Builder $query, bool $forProducts): Builder {
        return $query->where('for_products', $forProducts);
    }

    /**
     * @param Builder $query
     * @param bool    $forBlog
     *
     * @return Builder
     */
    public function scopeCategoriesForBlog(Builder $query, bool $forBlog): Builder {
        return $query->where('for_blog', $forBlog);
    }

    /**
     * @param Builder $query
     * @param array   $frd
     *
     * @return Builder
     */
    public function scopeFilter(Builder $query, array $frd): Builder {
        foreach ($frd as $key => $value) {
            if (is_null($value)) {
                continue;
            }

            switch ($key) {
                case 'favorites':
                    {
                        $query->categoriesFavorite($value);
                    }
                    break;
                case 'products':
                    {
                        $query->categoriesForProducts($value);
                    }
                    break;
                case 'blog':
                    {
                        $query->categoriesForBlog($value);
                    }
                    break;
                case 'orWhereIn':
                    {
                        $query->categoriesOrWhereIn($value);
                    }
                    break;
                case 'search':
                    {
                        $query->search($value);
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
     * @param string  $value
     *
     * @return Builder
     */
    public function scopeSearch(Builder $query, string $value): Builder {
        return $query->where(function (Builder $query) use ($value) {
            $query->where('name', 'like', '%' . $value . '%');
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
    public function scopeOrdering(Builder $query, array $frd): Builder {
        $ordering = (isset($frd['ordering']))
            ? $frd['ordering']
            : $this->getDefaultOrdering();
        $orderingDir = (isset($frd['orderingDir']))
            ? $frd['orderingDir']
            : $this->getDefaultOrderingDir();

        if ($ordering === 'search') {
			$ordering = 'name';
        }
        if ($ordering !== 'name') {
			$query->orderBy($ordering, $orderingDir);
			$query->orderBy('name');
		} else {
			$query->orderBy($ordering, $orderingDir);
		}
        return $query;
    }

    /**
     * @return BelongsTo
     */
    public function activeImage(): BelongsTo {
        return $this->belongsTo(Image::class);
    }

    /**
     * @return Image|null
     */
    public function getActiveImage(): ?Image {
        return $this->activeImage;
    }

    /**
     * @return bool
     */
    public function hasActiveImage(): bool {
        return (!is_null($this->getActiveImage()));
    }

    /**
     * @return null|string
     */
    public function getActiveImageLink(): ?string {
        if (!$this->hasActiveImage()) {
            return null;
        }

        return $this->getActiveImage()->getPublishPath();
    }

    /**
     * @return BelongsTo
     */
    public function businessImage(): BelongsTo {
        return $this->belongsTo(Image::class);
    }

    /**
     * @return Image|null
     */
    public function getBusinessImage(): ?Image {
        return $this->businessImage;
    }

    /**
     * @return bool
     */
    public function hasBusinessImage(): bool {
        return (!is_null($this->getBusinessImage()));
    }

    /**
     * @return null|string
     */
    public function getBusinessImageLink(): ?string {
        if (!$this->hasBusinessImage()) {
            return null;
        }

        return $this->getBusinessImage()->getPublishPath();
    }

    /**
     * @return BelongsTo
     */
    public function businessActiveImage(): BelongsTo {
        return $this->belongsTo(Image::class);
    }

    /**
     * @return Image|null
     */
    public function getBusinessActiveImage(): ?Image {
        return $this->businessActiveImage;
    }

    /**
     * @return bool
     */
    public function hasBusinessActiveImage(): bool {
        return (!is_null($this->getBusinessActiveImage()));
    }

    /**
     * @return null|string
     */
    public function getBusinessActiveImageLink(): ?string {
        if (!$this->hasBusinessActiveImage()) {
            return null;
        }

        return $this->getBusinessActiveImage()->getPublishPath();
    }

    /**
     * @return BelongsToMany
     */
    public function articles(): BelongsToMany {
        return $this->belongsToMany(Article::class, 'article_category', 'category_id', 'article_id');
    }

    /**
     * @return Collection
     */
    public function getArticles(): Collection {
        return $this->articles;
    }

    /**
     * @return BelongsTo
     */
    public function emptyImage(): BelongsTo {
        return $this->belongsTo(Image::class);
    }

    /**
     * @return Image|null
     */
    public function getEmptyImage(): ?Image {
        return $this->emptyImage;
    }

    /**
     * @return bool
     */
    public function hasEmptyImage(): bool {
        return (!is_null($this->getEmptyImage()));
    }

    /**
     * @return null|string
     */
    public function getEmptyImageLink(): ?string {
        if (!$this->hasEmptyImage()) {
            return null;
        }

        return $this->getEmptyImage()->getPublishPath();
    }

    /**
     * @param array $icons
     *
     * @throws \Exception
     */
    public function updateIcons(array $icons): void {
        if (!empty($icons)) {
            foreach ($icons as $key => $newIconId) {
                if (isset($newIconId)) {
                    $oldIconId = $this->{$key};
                    $oldIcon = Image::whereKey($oldIconId)->first();

                    if (!is_null($oldIcon)) {
                        $oldIcon->delete();
                        $this->{$key} = null;
                    }

                    $newIcon = Image::whereKey($newIconId)->first();
                    (is_null($newIcon))
                        ?: $this->images()->save($newIcon);

                    $this->{$key} = $newIcon->getKey();
                }
            }
        }
    }

    /**
     * @param array $frd
     *
     * @throws \Exception
     */
    public function updateCategory(array $frd): void {
        $this->setIsFavorite($frd['is_favorite'] ?? false);
        $this->setForBlog($frd['for_blog'] ?? false);
        $this->setForProducts($frd['for_products'] ?? false);
        $this->updateIcons($frd['icon'] ?? []);

        $this->save();
    }

    /**
     * @return string
     */
    public static function getDefaultImageForMap(): string {
        /**
         * @var Category $category
         */
        $category = self::whereKey(Category::CATEGORY_ID_FOR_DEFAULT_MAP_ICON)->first();

        return $category->getEmptyImageLink();
    }
}

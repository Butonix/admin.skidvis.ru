<?php
/**
 * Created by PhpStorm.
 * User: Pavel Onokhov
 * Date: 29.07.2019
 * Time: 0:06
 */

namespace App\Models\Articles;

use App\Models\Bookmarks\Bookmark;
use App\Models\Files\Image;
use App\Models\Organizations\Organization;
use App\Models\Products\Category;
use App\Models\Reviews\Like;
use App\Models\Reviews\Review;
use App\Models\Users\User;
use App\Traits\ImagesTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Articles\Article
 *
 * @property int                                                                            $id
 * @property string|null                                                                    $name
 * @property string|null                                                                    $text
 * @property int|null                                                                       $creator_id
 * @property int|null                                                                       $organization_id
 * @property int|null                                                                       $cover_id
 * @property int|null                                                                       $read_time
 * @property int                                                                            $views
 * @property \Illuminate\Support\Carbon|null                                                $created_at
 * @property \Illuminate\Support\Carbon|null                                                $updated_at
 * @property \Illuminate\Support\Carbon|null                                                $deleted_at
 * @property string|null                                                                    $short_description
 * @property string|null                                                                    $author
 * @property-read \App\Models\Users\User|null                                               $creator
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Files\Image[]        $images
 * @property-read \App\Models\Organizations\Organization|null                               $organization
 * @property int                                                                            $is_actual
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Products\Category[]  $categories
 * @property-read \App\Models\Files\Image|null                                              $cover
 * @property mixed|null                                                                     $editor
 * @property int|null                                                                       $article_label_id
 * @property-read \App\Models\Articles\ArticleLabel|null                                    $articleLabel
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Bookmarks\Bookmark[] $bookmarks
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Files\Image[]        $sliderImages
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Files\Image[]        $textImages
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Reviews\Review[]       $reviews
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Articles\Article onlyTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Articles\Article withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Articles\Article withoutTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Articles\Article newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Articles\Article newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Articles\Article query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Articles\Article whereIsActual($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Articles\Article whereCoverId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Articles\Article whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Articles\Article whereCreatorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Articles\Article whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Articles\Article whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Articles\Article whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Articles\Article whereOrganizationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Articles\Article whereReadTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Articles\Article whereText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Articles\Article whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Articles\Article whereViews($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Articles\Article whereAuthor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Articles\Article whereShortDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Articles\Article articlesActual($actual)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Articles\Article articlesByCategories($categories)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Articles\Article createdAtOrdering($orderingDir)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Articles\Article filter($frd)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Articles\Article ordering($frd)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Articles\Article search($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Articles\Article articlesSimple()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Articles\Article whereArticleLabelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Articles\Article whereEditor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Articles\Article articlesWhereIn($ids)
 * @method static bool|null forceDelete()
 * @method static bool|null restore()
 * @mixin \Eloquent
 */
class Article extends Model {
    use SoftDeletes;
    use ImagesTrait;

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        //Html содержимое статьи
        'text',
        //JSON текста статьи, хранится для возможного редактирования статьи (json используется для воспроизведения текста со всеми стилями)
        'editor',
        'creator_id',
        'organization_id',
        'cover_id',
        'read_time',
        'views',
        'short_description',
        'author',
        'is_actual',
        'article_label_id',
    ];

    const ARTICLES_RESPONSE_TYPE_SIMPLE_ACTUAL = 1;
    const ARTICLES_RESPONSE_TYPE_ALL           = 2;
    const ARTICLES_RESPONSE_TYPE_BOOKMARKS     = 3;
    const ARTICLES_TYPE_DEFAULT                = 4;
    const ARTICLES_TYPE_LIST                   = 5;

    //За какое количество дней проверяются свежие статьи
    protected static $latestDays = 3;

    /**
     * @var array
     */
    protected $casts = [
        'editor' => 'json',
    ];

    /**
     * Количество слов в минуту
     * @var string
     */
    protected $wordsPerMinute = 30;

    /**
     * Количество секунда на чтение одного изображения
     * @var string
     */
    protected $imageReadTime = 12;

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
    protected $perPageActualArticles = 6;

    /**
     * @var int
     */
    protected $perPageNewArticles = 7;

    /**
     * @return int
     */
    public static function getLatestDays(): int {
        return self::$latestDays;
    }

    /**
     * @return bool
     */
    public static function hasLatestArticles(): bool {
        $articles = self::whereDate('created_at', '>=', now()->subDays(self::getLatestDays()))->get();

        //Если коллекция $articles не пустая, то значит, что есть свежие статьи
        return $articles->isNotEmpty();
    }

    /**
     * @var array
     */
    protected static $rules = [
        'name'              => 'required|string',
        'short_description' => 'required_without:author|nullable|string',
        'author'            => 'required_without:short_description|nullable|string',
    ];

    /**
     * @var array
     */
    protected static $messages = [
        'name.required'                      => 'Укажите название для статьи',
        'short_description.required_without' => 'Без указания автора необходимо указать краткое описание',
        'author.required_without'            => 'Без указания краткого описания необходимо указать автора',
    ];

    /**
     * @return int
     */
    public function getWordsPerMinute(): int {
        return $this->wordsPerMinute;
    }

    /**
     * @return int
     */
    public function getImageReadTime(): int {
        return $this->imageReadTime;
    }

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
    public function getPerPageActualArticles(): int {
        return $this->perPageActualArticles;
    }

    /**
     * @return int
     */
    public function getPerPageSimpleArticles(): int {
        return $this->perPageNewArticles;
    }

    /**
     * @return null|string
     */
    public function getName(): ?string {
        return $this->{'name'};
    }

    /**
     * @param null|string $name
     */
    public function setName(?string $name): void {
        $this->{'name'} = $name;
    }

    /**
     * @return array|null
     */
    public function getEditor(): ?array {
        return $this->{'editor'};
    }

    /**
     * @return null|string
     */
    public function getEditorJsonEncode(): ?string {
        $editor = $this->getEditor();

        if (is_null($editor)) {
            return null;
        }

        return json_encode($editor);
    }

    /**
     * @param array|null $editor
     */
    public function setEditor(?array $editor): void {
        $this->{'editor'} = $editor;
    }

    /**
     * @return null|string
     */
    public function getText(): ?string {
        return $this->{'text'};
    }

    /**
     * @param null|string $text
     */
    public function setText(?string $text): void {
        $this->{'text'} = $text;
    }

    /**
     * Функция возвращает время на прочтение статьи в минутах
     * @return int|null
     */
    public function getReadTime(): ?int {
        return $this->{'read_time'} / 60;
    }

    /**
     * @param int|null $read_time
     */
    public function setReadTime(?int $read_time): void {
        $this->{'read_time'} = $read_time;
    }

    /**
     * @return int
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
     * @return array|null
     */
    public function getOrganizationInfo(): ?array {
        $organization = $this->getOrganization();

        if (is_null($organization)) {
            return null;
        }

        return [
            'id'    => $organization->getKey(),
            'name'  => $organization->getName(),
            'logo'  => $organization->getAvatarLink(),
            'color' => $organization->getAvatarColor(),
        ];
    }

    /**
     * @return int|null
     */
    public function getOrganizationId(): ?int {
        $organization = $this->getOrganization();

        if (is_null($organization)) {
            return null;
        }

        return $organization->getKey();
    }

    /**
     * @return null|string
     */
    public function getShortDescription(): ?string {
        return $this->{'short_description'};
    }

    /**
     * @param null|string $short_description
     */
    public function setShortDescription(?string $short_description): void {
        $this->{'short_description'} = $short_description;
    }

    /**
     * @return null|string
     */
    public function getAuthor(): ?string {
        return $this->{'author'};
    }

    /**
     * @param null|string $author
     */
    public function setAuthor(?string $author): void {
        $this->{'author'} = $author;
    }

    /**
     * @return bool
     */
    public function hasAuthor(): bool {
        return !is_null($this->getAuthor());
    }

    /**
     * @return int
     */
    public function isActual(): ?bool {
        return $this->{'is_actual'};
    }

    /**
     * @param bool $isActual
     */
    public function setActual(bool $isActual): void {
        $this->{'is_actual'} = $isActual;
    }

    /**
     *
     */
    public function calculateReadTime(): void {
        //Очищаем имеющийся текст от html и php тегов
        $content = strip_tags($this->getText());

        // Плучаем общее количество изображений в тексте
        preg_match_all("~<img~i", $content, $images);

        // Получаем общее время чтения текста в секундах
        $textReadTime = round(count(preg_split("/\s/", $content)) / $this->getWordsPerMinute(), 1) * 60;

        // Получаем общее время чтения изображений в секундах
        $imageTimeRead = (count($images[0]) * $this->getImageReadTime());

        // Получаем общее время чтения (текст + изображения) в секундах
        $totalTimeRead = $imageTimeRead + $textReadTime;

        $this->setReadTime($totalTimeRead);
    }

    /**
     * @return BelongsToMany
     */
    public function categories(): BelongsToMany {
        return $this->belongsToMany(Category::class, 'article_category', 'article_id', 'category_id');
    }

    /**
     * @return Article|null
     */
    public function next(): ?Article {
        return self::where('id', '<', $this->id)->orderBy('id', 'desc')->first();

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
	 * @param Builder $query
	 * @param string  $orderingDir
	 *
	 * @return Builder
	 */
	public function scopeArticlesReviewsCountOrdering(Builder $query, string $orderingDir): Builder {
		return $query->withCount('reviews')->orderBy('reviews_count', $orderingDir);
	}

    ///**
    // * @return Article
    // */
    //public function previous(): Article {
    //    return self::where('id', '<', $this->id)->orderBy('id', 'asc')->first();
    //}

    /**
     * @return Collection
     */
    public function getCategories(): Collection {
        return $this->categories;
    }

    /**
     * @return array|null
     */
    public function getNextArticle(): ?array {
        $article = $this->next();

        if (is_null($article)) {
            return null;
        }

        return [
            'id'                => $article->getKey(),
            'mainImage'         => $article->getCoverLink(),
            'name'              => $article->getName(),
            //'content'           => $article->getText(),
            'short_description' => $article->getShortDescription(),
            'author'            => $article->getAuthor(),
            'organization'      => $article->getOrganizationInfo(),
            'readTime'          => $article->getReadTime(),
            'views'             => $article->getViews(),
            'is_actual'         => $article->isActual(),
            'label'             => [
                'src'  => $article->getArticleLabelLink(),
                'name' => $article->getArticleName(),
            ],
            'categories'        => $article->getCategoriesShortInfo(),
        ];
    }

    /**
     * @return array
     */
    public function getCategoriesShortInfo(): array {
        $categories = $this->getCategories();
        $result = [];

        foreach ($categories as $category) {
            /**
             * @var Category $category
             */
            $result[] = [
                'id'   => $category->getKey(),
                'name' => $category->getName(),
            ];
        }

        return $result;
    }

    /**
     * @return array
     */
    public function getCategoriesNames(): array {
        return $this->getCategories()->keyBy('name')->keys()->toArray();
    }

    /**
     * @return array
     */
    public function getCategoriesId(): array {
        return $this->getCategories()->keyBy('id')->keys()->toArray();
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
     * @param array   $categories
     *
     * @return Builder
     */
    public function scopeArticlesByCategories(Builder $query, array $categories): Builder {
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
                    $query->whereIn('id', $resultArray);
                });
            });
        }

        return $query;
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
                  ->orWhere('author', 'like', '%' . $value . '%');
        });
    }

    /**
     * @param Builder $query
     * @param bool    $actual
     *
     * @return Builder
     */
    public function scopeArticlesActual(Builder $query, bool $actual): Builder {
        return $query->where(function (Builder $query) use ($actual) {
            $query->where('is_actual', $actual);
        });
    }

    /**
     * @param Builder $query
     * @param array   $ids
     *
     * @return Builder
     */
    public function scopeArticlesWhereIn(Builder $query, array $ids): Builder {
        return $query->whereIn('id', $ids);
    }

    /**
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeArticlesSimple(Builder $query): Builder {
        return $query->where(function (Builder $query) {
            $query->where('is_actual', false);
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
                case 'actual':
                    {
                        $query->articlesActual($value);
                    }
                    break;
                case 'categories':
                    {
                        $query->articlesByCategories($value);
                    }
                    break;
                case 'search':
                    {
                        $query->search($value);
                    }
                    break;
                case 'whereIn':
                    {
                        $query->articlesWhereIn($value);
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

        if ($ordering === 'created_at') {
            $query->createdAtOrdering($orderingDir);
        } elseif ($ordering === 'reviews_count') {
			$query->articlesReviewsCountOrdering($orderingDir);
		}

        return $query;
    }

    /**
     * @return array
     */
    public function getCoverLinks(): array {
        $cover = $this->getCover();
        $result = [];
        $coverLinks = [];

        if (is_null($cover)) {
            return $result;
        }

        $coverLinks['src'] = $cover->getPublishPath();
        $coverLinks['id'] = $cover->getKey();

        if ($cover->getMime() !== 'image/svg+xml') {
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

        return $result;
    }

    /**
     * @return MorphMany
     */
    public function textImages(): MorphMany {
        return $this->morphMany(Image::class, 'fileable')
                    ->where('public_path', 'like', '%' . 'text_' . '%')
                    ->where('file_parent_id', null);
    }

    /**
     * @return Collection
     */
    public function getTextImages(): Collection {
        return $this->textImages;
    }

    /**
     * @param array $images
     *
     * @throws \Exception
     */
    public function updateTextImages(array $images): void {
        $oldTextImages = $this->getTextImages()->keyBy('id');
        $oldTextImagesKeys = $oldTextImages->keys()->toArray();
        $newTextImagesKeys = [];

        if (!empty($images)) {
            foreach ($images as $imageId) {
                $newTextImagesKeys[] = $imageId;

                if (isset($oldTextImages[$imageId])) {
                    continue;
                }

                $newTextImage = Image::whereKey($imageId)->first();
                (is_null($newTextImage))
                    ?: $this->images()->save($newTextImage);
            }

            $oldTextImagesForDelete = array_diff($oldTextImagesKeys, $newTextImagesKeys);
            foreach ($oldTextImagesForDelete as $coverId) {
                /**
                 * @var Image $oldTextImageForDelete
                 */
                $oldTextImageForDelete = $oldTextImages[$coverId];
                $oldTextImageForDelete->delete();
            }
        } else {
            $oldTextImagesForDelete = $oldTextImagesKeys;
            foreach ($oldTextImagesForDelete as $coverId) {
                /**
                 * @var Image $oldTextImageForDelete
                 */
                $oldTextImageForDelete = $oldTextImages[$coverId];
                $oldTextImageForDelete->delete();
            }
        }
    }

    /**
     * @return BelongsTo
     */
    public function articleLabel(): BelongsTo {
        return $this->belongsTo(ArticleLabel::class);
    }

    /**
     * @return ArticleLabel|null
     */
    public function getArticleLabel(): ?ArticleLabel {
        return $this->articleLabel;
    }

    /**
     * @return null|string
     */
    public function getArticleName(): ?string {
        $label = $this->getArticleLabel();

        if (is_null($label)) {
            return null;
        }

        return $label->getName();
    }

    /**
     * @return null|string
     */
    public function getArticleLabelLink(): ?string {
        $label = $this->getArticleLabel();

        if (is_null($label)) {
            return null;
        }

        return $label->getImageLink();
    }

    /**
     * @return int|null
     */
    public function getArticleLabelId(): ?int {
        $label = $this->getArticleLabel();

        if (is_null($label)) {
            return null;
        }

        return $label->getKey();
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
     * @param array $categories
     */
    public function updateCategories(array $categories): void {
        //Категории, что уже добавлены к статье
        $chosenCategories = $this->getCategories()->keyBy('id');

        //Массив для хранения категорий, пришедших на сохранение
        $addCategories = [];

        if (!empty($categories)) {
            foreach ($categories as $category) {
                $addCategories[] = $category;

                //Если категории не были добавлены к акции ранее, то добавляем
                if (!isset($chosenCategories[$category])) {
                    $this->categories()->attach($category);
                }
            }

            //Сравнение массивов ранее добавленных категорий и тех, что пришли на сохранение.
            //Сравнение проводится для того, чтобы удалить неиспользуемые категории у статьи
            $removeCategories = array_diff($chosenCategories->keys()->toArray(), $addCategories);
            $this->categories()->detach($removeCategories);
        } else {
            $this->categories()->detach($chosenCategories);
        }
    }

    /**
     * @param int|null $organizationId
     */
    public function updateOrganization(?int $organizationId): void {
        if (!is_null($organizationId)) {
            $this->organization()->associate($organizationId);
        } else {
            $this->organization()->dissociate();
        }
    }

    /**
     * @param int|null $articleLabelId
     */
    public function updateArticleLabel(?int $articleLabelId): void {
        if (!is_null($articleLabelId)) {
            $this->articleLabel()->associate($articleLabelId);
        } else {
            $this->articleLabel()->dissociate();
        }
    }

    /**
     * @param array $frd
     *
     * @throws \Exception
     */
    public function updateArticle(array $frd): void {
        $this->setName($frd['name']);
        $this->setText($frd['text'] ?? null);
        $this->setEditor($frd['editor'] ?? null);
        $this->setShortDescription($frd['short_description'] ?? null);
        $this->setAuthor($frd['author'] ?? null);
        $this->setActual($frd['is_actual'] ?? false);

        // Сохраняем/обновляем главное изображение для статьи по id
        $this->updateTextImages($frd['textImages'] ?? []);
        $this->updateCover($frd['images'] ?? null);
        $this->updateCategories($frd['categories'] ?? []);
        $this->updateOrganization($frd['organization_id'] ?? null);
        $this->updateArticleLabel($frd['article_label_id'] ?? null);

        $this->calculateReadTime();
        $this->save();
    }
}

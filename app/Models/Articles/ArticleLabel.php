<?php
/**
 * Created by PhpStorm.
 * User: Pavel Onokhov
 * Date: 11.08.2019
 * Time: 13:54
 */

namespace App\Models\Articles;


use App\Models\Files\Image;
use App\Traits\ImagesTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\Articles\ArticleLabel
 *
 * @property int                                                                          $id
 * @property string|null                                                                  $name
 * @property int|null                                                                     $image_id
 * @property \Illuminate\Support\Carbon|null                                              $created_at
 * @property \Illuminate\Support\Carbon|null                                              $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Articles\Article[] $articles
 * @property-read \App\Models\Files\Image                                                 $cover
 * @property-read \App\Models\Files\Image|null                                            $image
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Files\Image[]      $images
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Files\Image[]      $sliderImages
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Articles\ArticleLabel filter($frd)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Articles\ArticleLabel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Articles\ArticleLabel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Articles\ArticleLabel query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Articles\ArticleLabel search($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Articles\ArticleLabel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Articles\ArticleLabel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Articles\ArticleLabel whereImageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Articles\ArticleLabel whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Articles\ArticleLabel whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ArticleLabel extends Model {
    use ImagesTrait;

    /**
     * @var array
     */
    protected $fillable = ['name', 'image_id'];

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
     * @return null|string
     */
    public function getImageLink(): ?string {
        $image = $this->getImage();

        if (is_null($image)) {
            return null;
        }

        return $image->getPublishPath();
    }

    /**
     * @return HasMany
     */
    public function articles(): HasMany {
        return $this->hasMany(Article::class);
    }

    /**
     * @return Collection
     */
    public function getArticles(): Collection {
        return $this->articles;
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
     * @return array
     */
    public static function getArticleLabelsList(): array {
        return self::orderBy('name', 'ASC')->pluck('article_labels.name', 'article_labels.id')->toArray();
    }

    /**
     * @param Image $oldIcon
     *
     * @throws \Exception
     */
    public function deleteImage(Image $oldIcon): void {
        $oldIcon->delete();
        $this->image()->dissociate();
    }

    /**
     * @param int|null $imageId
     *
     * @throws \Exception
     */
    public function updateImage(?int $imageId): void {
        $oldIcon = $this->getImage();

        if (!is_null($imageId)) {
            if (!is_null($oldIcon)) {
                $this->deleteImage($oldIcon);
                $this->image()->dissociate();
            }

            $newIcon = Image::whereKey($imageId)->first();

            if (!is_null($newIcon)) {
                $this->images()->save($newIcon);
                $this->image()->associate($newIcon);
            }
        } else {
            if (!is_null($oldIcon)) {
                $this->deleteImage($oldIcon);
            }
        }
    }
}

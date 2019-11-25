<?php
/**
 * Created by PhpStorm.
 * User: Pavel Onokhov
 * Date: 26.06.2019
 * Time: 13:30
 */

namespace App\Models\Products;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * App\Models\Products\Tag
 *
 * @property int                                                                          $id
 * @property string                                                                       $name
 * @property \Illuminate\Support\Carbon|null                                              $created_at
 * @property \Illuminate\Support\Carbon|null                                              $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Products\Product[] $products
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products\Tag newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products\Tag newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products\Tag query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products\Tag whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products\Tag whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products\Tag whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products\Tag whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products\Tag ordering($frd)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products\Tag search($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products\Tag searchOrdering($orderingDir)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products\Tag filter($frd)
 * @mixin \Eloquent
 */
class Tag extends Model {
    /**
     * @var array
     */
    protected $fillable = ['name'];

    /**
     * @var string
     */
    protected $defaultOrderingDir = 'ASC';

    /**
     * Количество тегов по-умолчанию для админки admin.skidvis.ru
     * @var int
     */
    protected $perPageForAdminPanel = 30;

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
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void {
        $this->name = $name;
    }

    /**
     * @return BelongsToMany
     */
    public function products(): BelongsToMany {
        return $this->belongsToMany(Product::class, 'tag_product', 'tag_id', 'product_id');
    }

    /**
     * @return Collection
     */
    public function getProducts(): Collection {
        return $this->products;
    }

    /**
     * @return array
     */
    public static function getAllTagsList(): array {
        return self::orderBy('name', 'ASC')->pluck('tags.name', 'tags.id')->toArray();
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
     * @param string  $orderingDir
     *
     * @return Builder
     */
    public function scopeSearchOrdering(Builder $query, string $orderingDir): Builder {
        return $query->orderBy('name', $orderingDir);
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

            switch ($key) {
                case 'ordering':
                    {
                        if ($value === 'search') {
                            $query->searchOrdering($orderingDir);
                        }
                    }
                    break;
                default:
                    {
                        $query->orderBy('name', $orderingDir);
                    }
                    break;
            }
        }

        return $query;
    }

}

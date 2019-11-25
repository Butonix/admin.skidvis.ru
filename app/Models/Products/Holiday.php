<?php
/**
 * Created by PhpStorm.
 * User: Pavel Onokhov
 * Date: 26.08.2019
 * Time: 17:13
 */

namespace App\Models\Products;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Products\Holiday
 *
 * @property int                             $id
 * @property string|null                     $name
 * @property int|null                        $ordering
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int                             $is_favorite
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products\Holiday newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products\Holiday newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products\Holiday query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products\Holiday whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products\Holiday whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products\Holiday whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products\Holiday whereOrdering($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products\Holiday whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products\Holiday filter($frd)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products\Holiday nameOrdering($orderingDir)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products\Holiday ordering($frd)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products\Holiday search($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products\Holiday holidaysFavorite($isFavorite)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products\Holiday holidaysOrWhereIn($ids)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products\Holiday whereIsFavorite($value)
 * @mixin \Eloquent
 */
class Holiday extends Model {
    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'ordering',
        'is_favorite' //Является ли категория популярной/избранной
    ];

    /**
     * @var string
     */
    protected $defaultOrdering = 'name';

    /**
     * @var string
     */
    protected $defaultOrderingDir = 'ASC';

    /**
     * Количество выходных/праздников по-умолчанию для админки admin.skidvis.ru
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
     * @return int|null
     */
    public function getOrdering(): ?int {
        return $this->{'ordering'};
    }

    /**
     * @param int|null $ordering
     */
    public function setOrdering(?int $ordering): void {
        $this->{'ordering'} = $ordering;
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
     * @param array   $ids
     *
     * @return Builder
     */
    public function scopeHolidaysOrWhereIn(Builder $query, array $ids): Builder {
        return $query->orWhereIn('id', $ids);
    }

    /**
     * @param Builder $query
     * @param bool    $isFavorite
     *
     * @return Builder
     */
    public function scopeHolidaysFavorite(Builder $query, bool $isFavorite): Builder {
        return $query->where('is_favorite', $isFavorite);
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
                case 'orWhereIn':
                    {
                        $query->holidaysOrWhereIn($value);
                    }
                    break;
                case 'favorites':
                    {
                        $query->holidaysFavorite($value);
                    }
                    break;
                default:
                    {
                        if ($key !== 'ordering') {
                            if (in_array($key, $this->fillable)) {
                                $query->where($key, $value);
                            }
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
    public function getDefaultOrdering(): string {
        return $this->defaultOrdering;
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
    public function scopeNameOrdering(Builder $query, string $orderingDir): Builder {
        return $query->orderBy('name', $orderingDir);
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

        if ($ordering === 'search' || $ordering === 'name') {
            $query->nameOrdering($orderingDir);
        } elseif ($ordering === 'ordering') {
            $query->orderBy('ordering', $orderingDir);
        }

        return $query;
    }
}

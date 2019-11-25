<?php
/**
 * Created by PhpStorm.
 * User: Pavel Onokhov
 * Date: 21.06.2019
 * Time: 18:06
 */

namespace App\Models\Social;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Social\SocialNetwork
 *
 * @property int                             $id
 * @property string                          $name
 * @property string|null                     $link
 * @property string|null                     $icon_url
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null                     $display_name
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Social\SocialNetwork newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Social\SocialNetwork newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Social\SocialNetwork query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Social\SocialNetwork whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Social\SocialNetwork whereIconUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Social\SocialNetwork whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Social\SocialNetwork whereLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Social\SocialNetwork whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Social\SocialNetwork whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Social\SocialNetwork filter($frd)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Social\SocialNetwork whereDisplayName($value)
 * @mixin \Eloquent
 */
class SocialNetwork extends Model {
    /**
     * @var array
     */
    protected $fillable = ['name', 'display_name', 'link', 'icon_url'];

    /**
     * @return string
     */
    public function getName(): string {
        return $this->{'name'};
    }

    /**
     * @return null|string
     */
    public function getDisplayName(): ?string {
        return $this->{'display_name'};
    }

    /**
     * @return null|string
     */
    public function getLink(): ?string {
        return $this->{'link'};
    }

    /**
     * @return null|string
     */
    public function getIconUrl(): ?string {
        return $this->{'icon_url'};
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
                        $query->where(function (Builder $query) use ($value) {
                            $query->where('name', 'like', '%' . $value . '%');
                        });
                    }
                    break;
            }
        }

        return $query;
    }
}

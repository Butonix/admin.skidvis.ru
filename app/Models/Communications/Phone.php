<?php
/**
 * Created by PhpStorm.
 * User: Pavel Onokhov
 * Date: 21.06.2019
 * Time: 17:06
 */

namespace App\Models\Communications;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Communications\Phone
 *
 * @property int                                                                              $id
 * @property string                                                                           $phone
 * @property int                                                                              $phoneable_id
 * @property string                                                                           $phoneable_type
 * @property \Illuminate\Support\Carbon|null                                                  $created_at
 * @property \Illuminate\Support\Carbon|null                                                  $updated_at
 * @property \Illuminate\Support\Carbon|null                                                  $deleted_at
 * @property string|null                                                                      $code
 * @property string|null                                                                      $full_phone
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Communications\Phone[] $phoneable
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Communications\Phone newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Communications\Phone newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Communications\Phone onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Communications\Phone query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Communications\Phone whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Communications\Phone whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Communications\Phone whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Communications\Phone wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Communications\Phone wherePhoneableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Communications\Phone wherePhoneableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Communications\Phone whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Communications\Phone withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Communications\Phone withoutTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Communications\Phone whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Communications\Phone whereFullPhone($value)
 * @method static bool|null forceDelete()
 * @method static bool|null restore()
 * @mixin \Eloquent
 */
class Phone extends Model {
    use SoftDeletes;

    protected $fillable = ['phone', 'full_phone', 'code', 'phoneable_id', 'phoneable_type'];

    /**
     * @return string
     */
    public function getPhone(): string {
        return $this->phone;
    }

    /**
     * @param string $phone
     */
    public function setPhone(string $phone): void {
        $this->phone = $phone;
    }

    /**
     * @return null|string
     */
    public function getCode(): ?string {
        return $this->code;
    }

    /**
     * @param null|string $code
     */
    public function setCode(?string $code): void {
        $this->code = $code;
    }

    /**
     * @return null|string
     */
    public function getFullPhone(): ?string {
        return $this->full_phone;
    }

    /**
     * @param null|string $full_phone
     */
    public function setFullPhone(?string $full_phone): void {
        $this->full_phone = $full_phone;
    }

    /**
     * @return MorphTo
     */
    public function phoneable(): MorphTo {
        return $this->morphTo();
    }

}

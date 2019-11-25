<?php
/**
 * Created by PhpStorm.
 * User: Pavel Onokhov
 * Date: 21.06.2019
 * Time: 17:33
 */

namespace App\Models\Communications;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Communications\Email
 *
 * @property int                                                                              $id
 * @property string                                                                           $email
 * @property int                                                                              $emailable_id
 * @property string                                                                           $emailable_type
 * @property \Illuminate\Support\Carbon|null                                                  $created_at
 * @property \Illuminate\Support\Carbon|null                                                  $updated_at
 * @property \Illuminate\Support\Carbon|null                                                  $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Communications\Email[] $emailable
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Communications\Email newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Communications\Email newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Communications\Email onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Communications\Email query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Communications\Email whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Communications\Email whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Communications\Email whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Communications\Email whereEmailableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Communications\Email whereEmailableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Communications\Email whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Communications\Email whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Communications\Email withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Communications\Email withoutTrashed()
 * @method static bool|null forceDelete()
 * @method static bool|null restore()
 * @mixin \Eloquent
 */
class Email extends Model {
    use SoftDeletes;

    /**
     * @var array
     */
    protected $fillable = ['email', 'emailable_id', 'emailable_type'];

    /**
     * @return string
     */
    public function getEmail(): string {
        return $this->{'email'};
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void {
        $this->{'email'} = $email;
    }

    /**
     * @return MorphTo
     */
    public function emailable(): MorphTo {
        return $this->morphTo();
    }
}

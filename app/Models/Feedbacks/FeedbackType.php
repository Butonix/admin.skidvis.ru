<?php
/**
 * Created by PhpStorm.
 * User: Pavel Onokhov
 * Date: 07.08.2019
 * Time: 23:34
 */

namespace App\Models\Feedbacks;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Feedbacks\FeedbackType
 *
 * @property int                             $id
 * @property string|null                     $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Feedbacks\FeedbackType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Feedbacks\FeedbackType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Feedbacks\FeedbackType query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Feedbacks\FeedbackType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Feedbacks\FeedbackType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Feedbacks\FeedbackType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Feedbacks\FeedbackType whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class FeedbackType extends Model {
    /**
     * @var array
     */
    protected $fillable = ['name'];

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
}

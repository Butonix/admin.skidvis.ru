<?php
/**
 * Created by PhpStorm.
 * User: Pavel Onokhov
 * Date: 04.07.2019
 * Time: 18:04
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Biglion
 *
 * @property int $id
 * @property string $link
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Biglion newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Biglion newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Biglion query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Biglion whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Biglion whereLink($value)
 * @mixin \Eloquent
 */
class Biglion extends Model {
    protected $fillable = ['link'];

    public $timestamps = false;
}

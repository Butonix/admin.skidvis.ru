<?php

namespace App\Models\Files\Documents;

use App\Models\Files\File;
use App\Models\Suppliers\Filters\Validator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * App\Models\Files\Documents\Sheet
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Files\Documents\Sheet newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Files\Documents\Sheet newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Files\Documents\Sheet query()
 * @mixin \Eloquent
 * @property int                                                $id
 * @property int                                                $user_id
 * @property string|null                                        $fileable_type
 * @property string|null                                        $fileable_id
 * @property string|null                                        $public_path
 * @property string|null                                        $local_path
 * @property string|null                                        $name
 * @property string|null                                        $mime
 * @property int|null                                           $size
 * @property array|null                                         $payload
 * @property \Illuminate\Support\Carbon|null                    $created_at
 * @property \Illuminate\Support\Carbon|null                    $updated_at
 * @property string|null                                        $delete_at
 * @property string|null                                        $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $sheetable
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Files\File filter($frd)
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Files\Documents\Sheet onlyTrashed()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Files\Documents\Sheet whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Files\Documents\Sheet whereDeleteAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Files\Documents\Sheet whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Files\Documents\Sheet whereFileableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Files\Documents\Sheet whereFileableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Files\Documents\Sheet whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Files\Documents\Sheet whereLocalPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Files\Documents\Sheet whereMime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Files\Documents\Sheet whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Files\Documents\Sheet wherePayload($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Files\Documents\Sheet wherePublicPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Files\Documents\Sheet whereSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Files\Documents\Sheet whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Files\Documents\Sheet whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Files\Documents\Sheet withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Files\Documents\Sheet withoutTrashed()
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $fileable
 * @property-read \App\Models\Users\User                        $user
 * @property string|null $file_delete_at
 * @property string|null $file_deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Files\Documents\Sheet whereFileDeleteAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Files\Documents\Sheet whereFileDeletedAt($value)
 * @property int|null $file_parent_id
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Files\Documents\Sheet whereFileParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Files\File unusedImages()
 */
class Sheet extends File
{
	use SoftDeletes;


	/**
	 * @param Collection|null $filters
	 * @param int|null        $stringFieldId
	 * @param string|null     $search
	 *
	 * @return array|LengthAwarePaginator
	 * @throws \GuzzleHttp\Exception\GuzzleException
	 */
	public function getPaginator(Collection $filters = null, int $stringFieldId = null, string $search = null)
	{

		$frd     = request()->all();
		$page    = $frd['page'] ?? 1;
		$perPage = $frd['perPage'] ?? 25;
		$data    = $this->getContents();
		$lines   = explode(PHP_EOL, $data);

		if (null !== $search)
		{
			$search = mb_strtolower($search, 'UTF-8');
		}

		/**
		 * @var Validator $validator
		 */
		$validator = new Validator($filters);

		$rows = array_map(function ($row) use ($validator, $stringFieldId, $search) {

			if (null !== $stringFieldId)
			{
				$string = explode(';', $row)[$stringFieldId] ?? null;
			}
			else
			{
				$string = $row;
			}

			if (true === $validator->validate($string))
			{
				if (null === $search
				|| false !== stripos($string,$search) )
				{
					return str_getcsv($string, ';', '"');

				}
			}
		}, $lines);

		/**
		 * Remove empty rows
		 */
		foreach ($rows as $i => $row)
		{
			if (null === $row)
			{
				unset($rows[$i]);
			}

		}


		$rowsCollection = collect($rows);
		$chunkedRows    = $rowsCollection->chunk($perPage)->toArray()[($page - 1)] ?? [];
		$rows           = new LengthAwarePaginator($chunkedRows, \count($rows), $perPage, $page, [
			'path' => request()->url(),
		]);

		return $rows;
	}

	public function delete()
	{
		return parent::delete();
	}

	/**
	 * @return MorphTo|null
	 */
	public function sheetable(): ?MorphTo
	{
		return $this->morphTo('fileable', 'fileable_type', 'fileable_id');
	}

	/**
	 * @return string
	 */
	public function getNameForHumans(): string
	{
		return '«'
			. $this->fileable->getName()
			. '» '
			. $this->getSizeFormatted()
			. ' от '
			. $this->created_at->format('d.m H:i')
			. ' №'
			. $this->getKey();
	}

}

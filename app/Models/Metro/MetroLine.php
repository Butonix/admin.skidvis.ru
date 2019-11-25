<?php

namespace App\Models\Metro;

use App\Models\Cities\City;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MetroLine extends Model
{
	/**
	 * @var array
	 */
	protected $fillable = [
		'city_id',
		'hex_color',
		'name',
		'ordering',
		'api_id',
	];

	public function getHexColor():?string {
		return $this->{'hex_color'};
	}

	/**
	 * @param string|null $data
	 */
	public function setHexColor(?string $data): void {
		$this->{'hex_color'} = $data;
	}

	public function getApiId():?string {
		return $this->{'api_id'};
	}

	/**
	 * @param string|null $data
	 */
	public function setApiId(?string $data): void {
		$this->{'api_id'} = $data;
	}

	public function getName():?string {
		return $this->{'name'};
	}

	/**
	 * @param string|null $data
	 */
	public function setName(?string $data): void {
		$this->{'name'} = $data;
	}

	public function getOrdering():?int {
		return $this->{'ordering'};
	}

	/**
	 * @param int|null $data
	 */
	public function setOrdering(?int $data): void {
		$this->{'ordering'} = $data;
	}

	/**
	 * @return BelongsTo
	 */
	public function city(): BelongsTo {
		return $this->belongsTo(City::class, 'city_id');
	}

	/**
	 * @return City|null
	 */
	public function getCity(): ?City {
		return $this->city;
	}

	/**
	 * @return null|string
	 */
	public function getCityName(): ?string {
		$city = $this->getCity();

		if (is_null($city)) {
			return null;
		}

		return $city->getName();
	}

	/**
	 * @return int|null
	 */
	public function getCityId(): ?int {
		$city = $this->getCity();

		if (is_null($city)) {
			return null;
		}

		return $city->getKey();
	}

}

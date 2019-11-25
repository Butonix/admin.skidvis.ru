<?php

namespace App\Models\Metro;

use App\Models\Cities\City;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MetroStation extends Model
{
	/**
	 * @var array
	 */
	protected $fillable = [
		'city_id',
		'metro_line_id',
		'name',
		'ordering',
		'api_id',
		'latitude',
		'longitude',
	];

	public function getName():?string {
		return $this->{'name'};
	}

	/**
	 * @param string|null $data
	 */
	public function setName(?string $data): void {
		$this->{'name'} = $data;
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

	public function getOrdering():?int {
		return $this->{'ordering'};
	}

	/**
	 * @param int|null $data
	 */
	public function setOrdering(?int $data): void {
		$this->{'ordering'} = $data;
	}

	public function getLatitude():?float {
		return $this->{'latitude'};
	}

	/**
	 * @param int|null $data
	 */
	public function setLatitude(?int $data): void {
		$this->{'latitude'} = $data;
	}

	public function getLongitude():?float {
		return $this->{'longitude'};
	}

	/**
	 * @param int|null $data
	 */
	public function setLongitude(?int $data): void {
		$this->{'longitude'} = $data;
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
	 * @return null|string
	 */
	public function getColorFirstMetroLine(): ?string {
		$metroLine = $this->getMetroLine();

		if (is_null($metroLine)) {
			return null;
		}

		return $metroLine->getHexColor();
	}

	/**
	 * @return null|string
	 */
	public function getNameMetroLine(): ?string {
		$metroLine = $this->getMetroLine();

		if (is_null($metroLine)) {
			return null;
		}

		return $metroLine->getName();
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

	/**
	 * @return BelongsTo
	 */
	public function metroLine(): BelongsTo {
		return $this->belongsTo(MetroLine::class, 'metro_line_id');
	}

	/**
	 * @return MetroLine|null
	 */
	public function getMetroLine(): ?MetroLine {
		return $this->metroLine;
	}
}

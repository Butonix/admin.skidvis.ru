<?php
/**
 * Created by PhpStorm.
 * User: Pavel Onokhov
 * Date: 21.06.2019
 * Time: 16:50
 */

namespace App\Models\Organizations;


use App\Models\Cities\City;
use App\Models\Metro\MetroLine;
use App\Models\Metro\MetroStation;
use App\Models\Products\Product;
use App\Models\Social\SocialAccount;
use App\Traits\EmailsTrait;
use App\Traits\PhonesTrait;
use App\Traits\SchedulesTrait;
use App\Traits\SocialsTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use GuzzleHttp\Client;

/**
 * App\Models\Organizations\Point
 *
 * @property int                                                                                                 $id
 * @property string                                                                                              $name
 * @property int                                                                                                 $organization_id
 * @property float|null                                                                                          $latitude
 * @property float|null                                                                                          $longitude
 * @property string|null                                                                                         $street
 * @property string|null                                                                                         $building
 * @property string|null                                                                                         $full_street
 * @property \Illuminate\Support\Carbon|null                                                                     $created_at
 * @property \Illuminate\Support\Carbon|null                                                                     $updated_at
 * @property string|null                                                                                         $deleted_at
 * @property-read \App\Models\Organizations\Organization                                                         $organization
 * @property int|null                                                                                            $phone_id
 * @property int|null                                                                                            $email_id
 * @property-read \App\Models\Communications\Phone|null                                                          $phone
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Organizations\OrganizationPointSchedule[] $schedules
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Products\Product[]                        $products
 * @property int                                                                                                 $own_schedule
 * @property string|null                                                                                         $city_kladr_id
 * @property array|null                                                                                          $payload
 * @property-read \App\Models\Cities\City|null                                                                   $city
 * @property-read \App\Models\Communications\Email|null                                                          $email
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Communications\Email[]                    $emails
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Communications\Phone[]                    $phones
 * @property-read \App\Models\Organizations\OrganizationPointSchedule                                            $schedule
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Social\SocialAccount[]                    $socialAccounts
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organizations\Point newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organizations\Point newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organizations\Point query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organizations\Point whereBuilding($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organizations\Point whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organizations\Point whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organizations\Point whereFullStreet($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organizations\Point whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organizations\Point whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organizations\Point whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organizations\Point whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organizations\Point whereOrganizationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organizations\Point whereStreet($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organizations\Point whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organizations\Point filter($frd)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organizations\Point whereEmailId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organizations\Point wherePhoneId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organizations\Point whereOwnSchedule($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organizations\Point cityFilter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organizations\Point whereCityKladrId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organizations\Point wherePayload($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organizations\Point pointsByCoordinates($coordinates)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organizations\Point pointsWithProducts()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organizations\Point pointsByCoordinatesWithProducts($coordinates, $frd)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organizations\Point pointsByProduct($productId)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Organizations\Point onlyTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Organizations\Point withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Organizations\Point withoutTrashed()
 * @method static bool|null restore()
 * @method static bool|null forceDelete()
 * @mixin \Eloquent
 */
class Point extends Model {
    use SoftDeletes;
    use PhonesTrait;
    use EmailsTrait;
    use SocialsTrait;
    use SchedulesTrait;

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'organization_id',
        'latitude',
        'longitude',
        'street',
        'building',
        'full_street',
        'phone_id',
        'email_id',
        'own_schedule',
        'city_kladr_id',
        'payload',
        'metro_station_id',
        'metro_station_distance',
        'extension',
    ];

    const POINTS_TYPE_DEFAULT        = 1;
    const POINTS_TYPE_FOR_MAP        = 2;
    const POINT_SCHEDULE_TYPE_OWN    = 3;
    const POINT_SCHEDULE_TYPE_PUBLIC = 4;

    /**
     * @var int
     */
    protected $perPageForMap = 100;

    /**
     * @var array
     */
    protected $casts = [
        'payload' => 'array',
    ];

    /**
     * @var array
     */
    protected static $rules = [
        'name'        => 'required|string|max:255',
        'latitude'    => 'nullable|numeric',
        'longitude'   => 'nullable|numeric',
        'full_street' => 'required|string|max:255',
        'street'      => 'nullable|string',
        'building'    => 'nullable|string',
        'phone'       => 'nullable|string',
        'email'       => 'nullable|email',
        'timezone'    => 'nullable|integer',
    ];

    /**
     * @var array
     */
    protected static $messages = [
        'name.required'        => 'Укажите название точки',
        'full_street.required' => 'Укажите полный адрес',
    ];

    /**
     * @return array
     */
    public static function getRules(): array {
        return self::$rules;
    }

    /**
     * @return array
     */
    public static function getMessages(): array {
        return self::$messages;
    }

    /**
     * @return int
     */
    public function getPerPageForMap(): int {
        return $this->perPageForMap;
    }

    /**
     * @return array|null
     */
    public function getPayload(): ?array {
        return $this->{'payload'};
    }

    /**
     * @param array|null $payload
     */
    public function setPayload(?array $payload): void {
        $this->{'payload'} = $payload;
    }

    /**
     * @return string
     */
    public function getName(): string {
        return $this->{'name'};
    }

	/**
	 * @param string $name
	 */
	public function setName(string $name): void {
		$this->{'name'} = $name;
	}

    /**
     * @return string|null
     */
    public function getExtension():? string {
        return $this->{'extension'};
    }

    /**
     * @param string $extension|null
     */
    public function setExtension(?string $extension): void {
        $this->{'extension'} = $extension;
    }

    /**
     * @return float|null
     */
    public function getLatitude(): ?float {
        return $this->{'latitude'};
    }

    /**
     * @param float|null $latitude
     */
    public function setLatitude(?float $latitude): void {
        $this->{'latitude'} = $latitude;
    }

    /**
     * @return null|string
     */
    public function getCityKladrId(): ?string {
        return $this->{'city_kladr_id'};
    }

    /**
     * @param null|string $cityKladrId
     */
    public function setCityKladrId(?string $cityKladrId): void {
        $this->{'city_kladr_id'} = $cityKladrId;
    }

    /**
     * @param null|int $data
     */
    public function setMetroStationId(?int $data): void {
        $this->{'metro_station_id'} = $data;
    }

    /**
     * @param null|int $data
     */
    public function setMetroStationDistance(?int $data): void {
        $this->{'metro_station_distance'} = $data;
    }

    public function getMetroStationDistance():? int {
        return $this->{'metro_station_distance'};
    }

    /**
     * @return BelongsTo
     */
    public function city(): BelongsTo {
        return $this->belongsTo(City::class, 'city_kladr_id', 'kladr');
    }

    /**
     * @return BelongsTo
     */
    public function metroStation(): BelongsTo {
        return $this->belongsTo(MetroStation::class, 'metro_station_id');
    }

	/**
	 * @return MetroStation|null
	 */
	public function getMetroStation(): ?MetroStation {
		return $this->metroStation;
	}

    /**
     * @return null|string
     */
    public function getColorMetroStations():? string {
		$metroStation = $this->getMetroStation();

		if (is_null($metroStation)) {
			return null;
		}

		return $metroStation->getColorFirstMetroLine();
    }

    /**
     * @return null|string
     */
    public function getNameMetroStation():? string {
		$metroStation = $this->getMetroStation();

		if (is_null($metroStation)) {
			return null;
		}

		return $metroStation->getName();
    }

    /**
     * @return null|string
     */
    public function getNameMetroLine():? string {
		$metroStation = $this->getMetroStation();

		if (is_null($metroStation)) {
			return null;
		}

		return $metroStation->getNameMetroLine();
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

    /**
     * @return float|null
     */
    public function getCityLatitude(): ?float {
        $city = $this->getCity();

        if (is_null($city)) {
            return null;
        }

        return $city->getLatitude();
    }

    /**
     * @return float|null
     */
    public function getCityLongitude(): ?float {
        $city = $this->getCity();

        if (is_null($city)) {
            return null;
        }

        return $city->getLongitude();
    }

    /**
     * @return float|null
     */
    public function getLongitude(): ?float {
        return $this->{'longitude'};
    }

    /**
     * @param float|null $longitude
     */
    public function setLongitude(?float $longitude): void {
        $this->{'longitude'} = $longitude;
    }

    /**
     * @return null|string
     */
    public function getStreet(): ?string {
        return $this->{'street'};
    }

    /**
     * @param null|string $street
     */
    public function setStreet(?string $street): void {
        $this->{'street'} = $street;
    }

    /**
     * @return null|string
     */
    public function getBuilding(): ?string {
        return $this->{'building'};
    }

    /**
     * @param null|string $building
     */
    public function setBuilding(?string $building): void {
        $this->{'building'} = $building;
    }

    /**
     * @return null|string
     */
    public function getFullStreet(): ?string {
        return $this->{'full_street'};
    }

    /**
     * @return null|string
     */
    public function getStreetReplacedCity(): ?string {
    	$region = null;
    	$city = null;
    	$full_street = $this->{'full_street'};
		if(!empty($full_street) && isset($this->payload) && isset($this->payload['data'])){
			$region = $this->payload['data']['region_with_type']??null;
			$city = $this->payload['data']['city_with_type']??null;
		}

		if(isset($region)){
			$full_street = str_replace($region.', ', '', $full_street);
		}

		if(isset($city)){
			$full_street = str_replace($city.', ', '', $full_street);
		}


        return $full_street;
    }

    /**
     * @param null|string $full_street
     */
    public function setFullStreet(?string $full_street): void {
        $this->{'full_street'} = $full_street;
    }

    /**
     * @return BelongsTo
     */
    public function organization(): BelongsTo {
        return $this->belongsTo(Organization::class);
    }

    /**
     * @return Organization
     */
    public function getOrganization(): Organization {
        return $this->organization;
    }

    /**
     * @return bool
     */
    public function ownSchedule(): ?bool {
        return $this->{'own_schedule'};
    }

    /**
     * @param bool $own_schedule
     */
    public function setOwnSchedule(bool $own_schedule): void {
        $this->{'own_schedule'} = $own_schedule;
    }

    /**
     * @param int $scheduleType
     *
     * @return OrganizationPointSchedule|null
     */
    public function getSchedule(int $scheduleType): ?OrganizationPointSchedule {
        //Каждая точка (адрес) имеет свой режим работы, но в зависимости от параметра `own_schedule` распознаем,
        //выставлено ли в параметрах брать режим работы от организации или нет.

        if ($scheduleType === self::POINT_SCHEDULE_TYPE_OWN) {
            // При наличии данного параметра всегда отдаем режим работы точки,
            // даже если параметр `own_schedule = false`
            return $this->schedule;
        } elseif ($this->hasOwnSchedule() && self::POINT_SCHEDULE_TYPE_PUBLIC) {
            // Если параметр выше отсутствует или не совпадает,
            // то отдаем режим работы точки в зависимости от параметра `own_schedule
            return $this->schedule;
        } else {
            // В противном случае отдаем режим работы организации
            return $this->getOrganization()->getSchedule();
        }
    }

    /**
     * @return int|null
     */
    public function getTimezone(): ?int {
        $city = $this->getCity();

        if (is_null($city)) {
            return null;
        }

        return $city->getKey();
    }

    /**
     * @return bool
     */
    public function hasOwnSchedule(): bool {
        return (is_null($this->ownSchedule()))
            ? false
            : $this->ownSchedule();
    }

    /**
     * @return null|string
     */
    public function getScheduleText(): ?string {
        $schedule = $this->getSchedule(self::POINT_SCHEDULE_TYPE_PUBLIC);

        if (is_null($schedule)) {
            return null;
        }

        return $schedule->getTextTime();
    }

    /**
     * @return BelongsToMany
     */
    public function products(): BelongsToMany {
        return $this->belongsToMany(Product::class, 'product_point', 'point_id', 'product_id');
    }

    /**
     * @return Collection
     */
    public function getProducts(): Collection {
        return $this->products;
    }

    /**
     * @param Builder $query
     * @param array   $coordinates
     *
     * @return Builder
     */
    public function scopePointsByCoordinates(Builder $query, array $coordinates): Builder {
        return $query->where('latitude', '<=', $coordinates['latitudeMax'])
                     ->where('latitude', '>=', $coordinates['latitudeMin'])
                     ->where('longitude', '<=', $coordinates['longitudeMax'])
                     ->where('longitude', '>=', $coordinates['longitudeMin']);
    }

    /**
     * @param Builder $query
     * @param array   $coordinates
     * @param array   $frd
     *
     * @return Builder
     */
    public function scopePointsByCoordinatesWithProducts(Builder $query, array $coordinates, array $frd): Builder {
        return $this->pointsByCoordinates($coordinates)
                    ->has('products', '>', 0)
                    ->whereHas('products', function (Builder $query) use ($frd) {
                        $query->publicProducts()->filter($frd)->productsIsActive(true);
                    });
    }

    /**
     * @param Builder $query
     * @param         $value
     *
     * @return Builder
     */
    public function scopeCityFilter(Builder $query, $value): Builder {
        return $query->where(function (Builder $query) use ($value) {
            $query->whereHas('city', function (Builder $query) use ($value) {
                $query->whereKey((int)$value);
            });
        });
    }

    /**
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopePointsWithProducts(Builder $query): Builder {
        return $query->whereHas('products');
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
                            $query->orWhere('name', 'like', '%' . $value . '%')
                                  ->orWhereHas('phone', function (Builder $query) use ($value) {
                                      $query->where('full_phone', 'like', '%' . $value . '%');
                                  })
                                  ->orWhereHas('email', function (Builder $query) use ($value) {
                                      $query->where('email', 'like', '%' . $value . '%');
                                  })
                                  ->orWhere('full_street', 'like', '%' . $value . '%');
                        });

                    }
                    break;
                case 'city_id':
                    {
                        $query->cityFilter($value);
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
     * @param array        $frd
     * @param Organization $organization
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Exception
     */
    public function updatePoint(array $frd, Organization $organization): void {
        $this->setName($frd['name']);
        $this->setExtension($frd['extension'] ?? null);
        $this->setFullStreet($frd['full_street']);
        $this->setLatitude((float)$frd['latitude'] ?? null);
        $this->setLongitude((float)$frd['longitude'] ?? null);
        $this->setStreet($frd['street'] ?? null);
        $this->setBuilding($frd['building'] ?? null);
        $this->setPayload($frd['payload'] ?? null);
        $this->updatePhone($frd['phone'] ?? null);
        $this->updateEmail($frd['email'] ?? null);
        $this->updateCity($frd['city_kladr_id'] ?? null);
        $this->updateSchedule($frd['operationMode'] ?? [], $frd['own_schedule'], $frd['timezone'] ?? null, $organization);
        $this->attachMetroStation();
        $this->save();
    }

    /**
     * @param string $cityKladrId
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function updateCity(?string $cityKladrId): void {
        if (!is_null($cityKladrId)) {
            $city = City::where('kladr', $cityKladrId)->first();
            if (is_null($city)) {
                $city = new City();
                $city->updateByKladr($cityKladrId);
            }

            $this->city()->associate($cityKladrId);
        } else {
            $this->city()->dissociate();
        }
    }

    /**
     * @param Builder $query
     * @param int     $productId
     *
     * @return Builder
     */
    public function scopePointsByProduct(Builder $query, int $productId): Builder {
        return $query->whereHas('products', function (Builder $query) use ($productId){
            $query->whereKey($productId);
        });
    }

	/**
	 * @return Client
	 */
	public function getClient(): Client {
		if (is_null($this->client)) {
			$this->client = new Client([
				'timeout' => 30,
			]);
		}

		return $this->client;
	}

	public function getDistanceMain(float $latitudeFrom, float $longitudeFrom, float $latitudeTo, float $longitudeTo): int
	{
		$EARTH_RADIUS_IN_METERS = 6372797.560856;
		$DEGREEES_TO_RAD = 0.017453292519943295769236907684886;
		$dtheta = ($latitudeFrom - $latitudeTo) * $DEGREEES_TO_RAD;
		$dlambda = ($longitudeFrom - $longitudeTo) * $DEGREEES_TO_RAD;
		$mean_t = ($latitudeFrom + $latitudeTo) * $DEGREEES_TO_RAD / 2.0;
		$cos_meant = cos($mean_t);

    	return $EARTH_RADIUS_IN_METERS * sqrt($dtheta * $dtheta + $cos_meant * $cos_meant * $dlambda * $dlambda);
	}

	public function getDistance(float $latitudeTo, float $longitudeTo): int
	{
    	return $this->getDistanceMain($this->getLatitude(), $this->getLongitude(), $latitudeTo, $longitudeTo);
	}

	public function attachMetroStation(): void
	{
		if($this->getCityId() !== 3){
			return;
		}
		$cityId = $this->getCityId();

		$metroStations = MetroStation::whereNotNull('latitude')
			->whereNotNull('longitude')
			->where('city_id', $cityId)->get();

		/**
		 * @var MetroStation $metroStation
		 */
		$minDistance = null;
		$id = null;
		foreach ($metroStations as $metroStation) {

			$distance = $this->getDistance($metroStation->getLatitude(), $metroStation->getLongitude());
			if($minDistance === null || $minDistance !== null && $minDistance > $distance){
				$minDistance = $distance;
				$id = $metroStation->getKey();
			}
		}
		if($minDistance !== null && $minDistance < 1000){
			$this->setMetroStationId($id);
			$this->setMetroStationDistance($minDistance);
		}

	}

	public function updateMetroStations():void {
		$cityId = 3; // Санкт-Петербург

		$counter = 0;
		$count = Point::whereNotNull('latitude')
			->whereNotNull('longitude')->count();

		$metroStations = MetroStation::
			whereNotNull('latitude')
			->whereNotNull('longitude')
			->where('city_id', $cityId)->get();

		Point::whereNotNull('latitude')
			->whereNotNull('longitude')
			->chunk(100, function ($items) use (&$metroStations, &$counter, &$count) {
			foreach ($items as $item) {
				$counter++;
				/**
				 * @var Point $item
				 * @var MetroStation $metroStation
				 */
				$minDistance = null;
				$id = null;
				foreach ($metroStations as $metroStation) {

					$distance = $item->getDistance($metroStation->getLatitude(), $metroStation->getLongitude());
					if($minDistance === null || $minDistance !== null && $minDistance > $distance){
						$minDistance = $distance;
						$id = $metroStation->getKey();
					}
				}
				if($minDistance !== null && $minDistance < 1000){
					$item->setMetroStationId($id);
					$item->setMetroStationDistance($minDistance);
					$item->save();
					dump($counter.'/'.$count.' Point:'.$item->getKey().' MetroStation:'.$id.' Distance:'.$minDistance.'m');
				} else {
					dump($counter.'/'.$count);
				}
			}
		});

	}

}

<?php
/**
 * Created by PhpStorm.
 * User: Pavel Onokhov
 * Date: 17.07.2019
 * Time: 11:59
 */

namespace App\Models\Cities;


use App\Models\Metro\MetroLine;
use App\Models\Metro\MetroStation;
use App\Models\Organizations\Point;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\Cities\City
 *
 * @property int                                                                             $id
 * @property string|null                                                                     $name
 * @property string|null                                                                     $region
 * @property string|null                                                                     $district
 * @property string|null                                                                     $UTC
 * @property \Illuminate\Support\Carbon|null                                                 $created_at
 * @property \Illuminate\Support\Carbon|null                                                 $updated_at
 * @property string|null                                                                     $timezone
 * @property float|null                                                                      $latitude
 * @property float|null                                                                      $longitude
 * @property string|null                                                                     $kladr
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Organizations\Point[] $points
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cities\City newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cities\City newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cities\City query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cities\City whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cities\City whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cities\City whereDistrict($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cities\City whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cities\City whereRegion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cities\City whereTimezone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cities\City whereUTC($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cities\City whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cities\City whereKladr($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cities\City whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cities\City whereLongitude($value)
 * @mixin \Eloquent
 */
class City extends Model {
    /**
     * @var array
     */
    protected $fillable = ['name', 'region', 'district', 'UTC', 'timezone', 'kladr', 'latitude', 'longitude'];

    /**
     * @var string
     */
    const DEFAULT_CITY               = 'Москва';
    const DEFAULT_TIMEZONE_ID        = 2;
    const DEFAULT_CITY_FOR_USERS     = 'Санкт-Петербург';
    const DEFAULT_TIMEZONE_FOR_USERS = 3;

    /**
     * @var
     */
    protected $client;

    /**
     * @var string
     */
    protected $kladrApiUrl    = 'https://suggestions.dadata.ru/suggestions/api/4_1/rs/findById/address';
    protected $timezoneApiUrl = 'http://api.timezonedb.com/v2.1/get-time-zone';

    protected $metroAPI = 'https://api.hh.ru/metro/2'; // Санкт-Петербург = City.find(3)

    /**
     * @return string
     */
    public function getKladrApiUrl(): string {
        return $this->kladrApiUrl;
    }

    /**
     * @return string
     */
    public function getKladrApiToken(): string {
        return env('DADATA_TOKEN');
    }

    /**
     * @return string
     */
    public function getTimezoneApiUrl(): string {
        return $this->timezoneApiUrl;
    }

    /**
     * @return string
     */
    public function getTimezoneApiToken(): string {
        return env('TIMEZONE_API_TOKEN');
    }


    /**
     * @param string $kladr
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function updateByKladr(string $kladr): void {
        $client = $this->getClient();
        $cityInfo = $client->request('GET', $this->getKladrApiUrl(), [
            'headers' => [
                'Content-Type'  => 'application/json',
                'Accept'        => 'application/json',
                'Authorization' => 'Token ' . $this->getKladrApiToken(),
            ],
            'query'   => [
                'query' => $kladr,
            ],
        ])->getBody()->getContents();
        $name = $region = $district = $UTC = $timezone = $latitude = $longitude = null;
        $cityInfo = json_decode($cityInfo, true);

        if (!empty($cityInfo['suggestions'])) {
            $cityInfo = $cityInfo['suggestions'][0];
            $name = $cityInfo['data']['city'];
            $region = $cityInfo['data']['region'] . ' ' . $cityInfo['data']['region_type_full'];
            $district = $cityInfo['data']['federal_district'];
            $latitude = $cityInfo['data']['geo_lat'];
            $longitude = $cityInfo['data']['geo_lon'];

            if (isset($latitude) && isset($longitude)) {
                $timezoneInfo = $client->request('GET', $this->getTimezoneApiUrl(), [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'Accept'       => 'application/json',
                        //'Authorization' => 'Token ' . $this->getKladrApiToken(),
                    ],
                    'query'   => [
                        'key'    => $this->getTimezoneApiToken(),
                        'format' => 'json',
                        'by'     => 'position',
                        'lat'    => $latitude,
                        'lng'    => $longitude,
                    ],
                ])->getBody()->getContents();
                $timezoneInfo = json_decode($timezoneInfo, true);

                if (!empty($timezoneInfo)) {
                    //Указание UTC для города строится с расчетом на то, что добавляются только города России,
                    //в противном случае может оказаться так, что UTC в секундах при переводе в часы является дробным числом
                    //и могут полететь ошибки. (Часовые пояса России все имеют ровное количество часов, т.е. без минут)
                    $timezone = $timezoneInfo['zoneName'];

                    try {
                        $UTCseconds = $timezoneInfo['gmtOffset'];
                        $UTChours = $UTCseconds / 60 / 60; //Перевод в часы
                        $UTCsign = ($UTCseconds < 0)
                            ? '-'
                            : '+'; //Если секунды имеют отрицательное значение, то UTC со знаком "-"
                        $UTChours = (string)$UTChours; //Часы переводим в строку для работы с символами
                        $UTChours = str_replace(['-', '+'], [
                            '',
                            '',
                        ], $UTChours); //Плюс или минус меняем на пустой символ
                        $UTChours = ((int)$UTChours < 10)
                            ? '0' . $UTChours
                            : $UTChours; //Если значение часов менее 10, то добавляем 0 перед значением
                        $UTC = $UTCsign . $UTChours . ':00'; //Формируем UTC
                    } catch (\Exception $exception) {
                        \Log::error('City@updateByKladr', [
                            'message' => $exception->getMessage(),
                            'line'    => $exception->getLine(),
                            'code'    => $exception->getCode(),
                            'file'    => $exception->getFile(),
                        ]);
                    }
                }
            }
        }

        $this->setName($name);
        $this->setRegion($region);
        $this->setDistrict($district);
        $this->setUTC($UTC);
        $this->setTimezone($timezone);
        $this->setKladr($kladr);
        $this->setLatitude($latitude);
        $this->setLongitude($longitude);
        $this->save();
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

    /**
     * @return null|string
     */
    public function getName(): ?string {
        return $this->name;
    }

    /**
     * @param null|string $name
     */
    public function setName(?string $name): void {
        $this->name = $name;
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
    public function getKladr(): ?string {
        return $this->{'kladr'};
    }

    /**
     * @param null|string $kladr
     */
    public function setKladr(?string $kladr): void {
        $this->{'kladr'} = $kladr;
    }

    /**
     * @return null|string
     */
    public function getRegion(): ?string {
        return $this->region;
    }

    /**
     * @param null|string $region
     */
    public function setRegion(?string $region): void {
        $this->region = $region;
    }

    /**
     * @return null|string
     */
    public function getDistrict(): ?string {
        return $this->district;
    }

    /**
     * @param null|string $district
     */
    public function setDistrict(?string $district): void {
        $this->district = $district;
    }

    /**
     * @return null|string
     */
    public function getUTC(): ?string {
        return $this->UTC;
    }

    /**
     * @param null|string $UTC
     */
    public function setUTC(?string $UTC): void {
        $this->UTC = $UTC;
    }

    /**
     * @return null|string
     */
    public function getTimezone(): ?string {
        return $this->timezone;
    }

    /**
     * @param null|string $timezone
     */
    public function setTimezone(?string $timezone): void {
        $this->timezone = $timezone;
    }

    /**
     * @return Carbon
     */
    public function getTimeNowWithTimezone(): Carbon {
        return Carbon::now()->setTimezone($this->getTimezone());
    }

    /**
     * @return string
     */
    public function getCityWithTime(): string {
        return $this->getTimeNowWithTimezone()->format('H:i') . ', ' . $this->getName();
    }

    /**
     * @return HasMany
     */
    public function points(): HasMany {
        return $this->hasMany(Point::class, 'city_kladr_id', 'kladr');
    }

    /**
     * @return Collection
     */
    public function getPoints(): Collection {
        return $this->points;
    }

    public function updateMetroStations():void {
    	$cityId = 3; // Санкт-Петербург
		$client = $this->getClient();
		$metroInfo = $client->request('GET', $this->metroAPI)->getBody()->getContents();
		$metroInfo = json_decode($metroInfo, true);
		foreach ($metroInfo['lines'] as $lineInfo){
			$line = MetroLine::updateOrCreate(['api_id'=>$lineInfo['id']], [
				'city_id'=>$cityId,
				'hex_color'=>$lineInfo['hex_color'],
				'name'=>$lineInfo['name'],
			]);
			foreach ($lineInfo['stations'] as $stationInfo){
				$station = MetroStation::updateOrCreate(['api_id'=>$stationInfo['id']], [
					'city_id'=>$cityId,
					'metro_line_id'=>$line->getKey(),
					'order'=>$stationInfo['order'],
					'name'=>$stationInfo['name'],
					'latitude'=>$stationInfo['lat'],
					'longitude'=>$stationInfo['lng'],
				]);
			}
		}
	}
}

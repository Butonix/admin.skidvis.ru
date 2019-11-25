<?php
/**
 * Created by PhpStorm.
 * User: Pavel Onokhov
 * Date: 17.07.2019
 * Time: 12:57
 */

namespace App\Http\Controllers\Api\Cities;


use App\Http\Controllers\Controller;
use App\Http\Resources\Cities\CityCollection;
use App\Models\Cities\City;
use Illuminate\Http\Request;

class CityController extends Controller {

    /**
     * @var
     */
    protected $cities;

    /**
     * CityController constructor.
     *
     * @param City $timezones
     */
    public function __construct(City $cities) {
        $this->cities = $cities;
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request) {
        $timezones = $this->cities::orderBy('name')->get();
        $timezones = new CityCollection($timezones);

        return response()->json([
            'list' => $timezones,
        ], 200);
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function allCities(Request $request) {
        $cities = $this->cities::whereHas('points')->orderBy('name')->get(['id', 'name', 'latitude', 'longitude']);

        return response()->json([
            'list' => $cities,
        ], 200);
    }

    /**
     * @param Request $request
     * @param City    $city
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, City $city) {
        return response()->json([
            'id'        => $city->getKey(),
            'name'      => $city->getName(),
            'latitude'  => $city->getLatitude(),
            'longitude' => $city->getLongitude(),
        ], 200);
    }
}

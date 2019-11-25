<?php
/**
 * Created by PhpStorm.
 * User: Pavel Onokhov
 * Date: 24.06.2019
 * Time: 14:45
 */

namespace App\Http\Controllers\Api\Organizations;


use App\Http\Controllers\Controller;
use App\Http\Resources\Organizations\PointCollection;
use App\Models\Cities\City;
use App\Models\Organizations\Organization;
use App\Models\Organizations\OrganizationPointSchedule;
use App\Models\Organizations\Point;
use App\Http\Resources\Organizations\Point as PointResource;
use App\Models\Users\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;

class PointController extends Controller {
    /**
     * @var Point
     */
    protected $points;

    /**
     * PointController constructor.
     *
     * @param Point $points
     */
    public function __construct(Point $points) {
        $this->points = $points;

        $this->middleware(['permission:points--create'])->only(['store']);
        $this->middleware(['permission:points--read'])->only(['index', 'show']);
        $this->middleware(['permission:points--update'])->only(['edit', 'update']);
        $this->middleware(['permission:points--delete'])->only(['destroy']);
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function map(Request $request) {
        $frd = $request->all();

        $points = $this->points->pointsByCoordinatesWithProducts([
            'latitudeMin'  => $frd['latitudeMin'],
            'longitudeMin' => $frd['longitudeMin'],
            'latitudeMax'  => $frd['latitudeMax'],
            'longitudeMax' => $frd['longitudeMax'],
        ], $frd)->paginate($frd['perPage'] ?? $this->points->getPerPageForMap());

        $response['list'] = (new PointCollection($points))->additional([
            'meta' => [
                'needProducts' => true,
                'filter'       => $frd,
                'type'         => Point::POINTS_TYPE_FOR_MAP,
                'scheduleType' => Point::POINT_SCHEDULE_TYPE_PUBLIC,
            ],
        ]);

        return response()->json($response, 200);
    }

    /**
     * @param Request      $request
     * @param Organization $organization
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request, Organization $organization) {
        /**
         * @var User $user
         */
        $user = \Auth::guard('api')->user();

        if ($user->hasNoAccess($organization)) {
            return response()->json([
                'status' => 'error',
                'alert'  => [
                    'type' => 'error',
                    'text' => 'Недостаточно прав для получения адресов',
                ],
            ], 403);
        }

        $frd = $request->all();
        $points = [];
        $responseTypeId = (isset($frd['responseTypeId']))
            ? (int)$frd['responseTypeId']
            : 1;

        if ($responseTypeId === 1) {
            $points = $organization->points()
                                   ->filter($frd)
                                   ->orderBy('name', 'ASC')
                                   ->paginate($frd['perPage'] ?? $this->points->getPerPage());
            $points = (new PointCollection($points))->additional([
                'meta' => [
                    'needProducts' => false,
                    'type'         => Point::POINTS_TYPE_DEFAULT,
                    'scheduleType' => Point::POINT_SCHEDULE_TYPE_OWN,
                ],
            ]);
        } elseif ($responseTypeId === 2) {
            $points = $organization->points()->get(['id', 'name', 'full_street', 'latitude', 'longitude']);
        }

        return response()->json([
            'list' => $points,
        ], 200);
    }

    /**
     * @param Request      $request
     * @param Organization $organization
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Exception
     */
    public function store(Request $request, Organization $organization) {
        /**
         * @var Point $point
         */
        $validator = Validator::make($request->all(), Point::getRules(), Point::getMessages());

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        $frd = $request->all();
        $point = $organization->points()->save(new Point());
        $point->updatePoint($frd, $organization);

        return response()->json([
            'status' => 'OK',
            'alert'  => [
                'type' => 'success',
                'text' => 'Адрес успешно создан.',
            ],
            'action' => [
                'type' => null,
                'url'  => null,
            ],
            'point'  => new PointResource($point),
        ], 200);
    }

    /**
     * @param Request      $request
     * @param Organization $organization
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Exception
     */
    public function checkBeforeStore(Request $request, Organization $organization) {
        /**
         * @var Point $point
         */
        $validator = Validator::make($request->all(), [
        	'addresses' => 'required|array|min:1'
		], [
			'addresses.required'        => 'Обязательно укажите массив с адресами',
			'addresses.array'        => 'Поле содержащее адреса должно быть массивом строк(адресов)',
			'addresses.min' => 'Массив с адресами не должен быть пустым',
		]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        $frd = $request->only(['addresses']);
        $duplicates = [];

		foreach ($frd['addresses'] as $key=>$item){
			if($organization->points()->where('full_street', 'like', '%' . $item . '%')->exists()){
				$duplicates[] = $key;
			};
		}


        return response()->json([
            'status' => 'OK',
			'duplicates' => $duplicates
        ], 200);
    }

    /**
     * @param Request      $request
     * @param Organization $organization
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Exception
     */
    public function importSimple(Request $request, Organization $organization) {
        /**
         * @var Point $point
         */
        $validator = Validator::make($request->all(), [
        	'addresses' => 'required|array|min:1'
		], [
			'addresses.required'        => 'Обязательно укажите массив с адресами',
			'addresses.array'        => 'Поле содержащее адреса должно быть массивом объектов(адресов)',
			'addresses.min' => 'Массив с адресами не должен быть пустым',
		]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        $frd = $request->only(['addresses']);

		foreach ($frd['addresses'] as $key=>$item){
			$validator = Validator::make($item, Point::getRules(), Point::getMessages());

			if ($validator->fails()) {
				return response()->json([
					'errors' => $validator->errors(),
				], 422);
			}

			$point = $organization->points()->save(new Point());
			$point->updatePoint($item, $organization);
		}


        return response()->json([
            'status' => 'OK',
        ], 200);
    }

    /**
     * @param Request      $request
     * @param Organization $organization
     * @param Point        $point
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Request $request, Organization $organization, Point $point) {
        /**
         * @var User $user
         */
        $user = \Auth::guard('api')->user();

        if ($user->hasNoAccess($organization)) {
            return response()->json([
                'status' => 'error',
                'alert'  => [
                    'type' => 'error',
                    'text' => 'Недостаточно прав для просмотра адреса',
                ],
            ], 403);
        }

        if ($organization->points()->whereKey($point)->exists()) {
            return response()->json([
                'point' => new PointResource($point),
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'alert'  => [
                    'type' => 'error',
                    'text' => 'Данный адрес не найден в указанной организации.',
                ],
                'action' => [
                    'type' => null,
                    'url'  => null,
                ],
            ], 404);
        }
    }

    /**
     * @param Request      $request
     * @param Organization $organization
     * @param Point        $point
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Request $request, Organization $organization, Point $point) {
        /**
         * @var User $user
         */
        $user = \Auth::guard('api')->user();

        if ($user->hasNoAccess($organization)) {
            return response()->json([
                'status' => 'error',
                'alert'  => [
                    'type' => 'error',
                    'text' => 'Недостаточно прав для редактирования адреса',
                ],
            ], 403);
        }

        if ($organization->points()->whereKey($point)->exists()) {
            return response()->json([
                'point' => new PointResource($point),
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'alert'  => [
                    'type' => 'error',
                    'text' => 'Данный адрес не найден в указанной организации.',
                ],
                'action' => [
                    'type' => null,
                    'url'  => null,
                ],
            ], 404);
        }
    }

    /**
     * @param Request      $request
     * @param Organization $organization
     * @param Point        $point
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function update(Request $request, Organization $organization, Point $point) {
        /**
         * @var User $user
         */
        $user = \Auth::guard('api')->user();

        if ($user->hasNoAccess($organization)) {
            return response()->json([
                'status' => 'error',
                'alert'  => [
                    'type' => 'error',
                    'text' => 'Недостаточно прав для обновления адреса',
                ],
            ], 403);
        }

        if (!$organization->points()->whereKey($point)->exists()) {
            return response()->json([
                'status' => 'error',
                'alert'  => [
                    'type' => 'error',
                    'text' => 'Данный адрес не найден в указанной организации.',
                ],
                'action' => [
                    'type' => null,
                    'url'  => null,
                ],
            ], 404);
        }

        /**
         * @var Point $point
         */
        $validator = Validator::make($request->all(), Point::getRules(), Point::getMessages());

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        $frd = $request->all();
        $point->updatePoint($frd, $organization);

        return response()->json([
            'status' => 'OK',
            'alert'  => [
                'type' => 'success',
                'text' => 'Адрес успешно обновлен',
            ],
            'action' => [
                'type' => null,
                'url'  => null,
            ],
        ], 200);
    }

    /**
     * @param Request      $request
     * @param Organization $organization
     * @param Point        $point
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy(Request $request, Organization $organization, Point $point) {
        /**
         * @var User $user
         */
        $user = \Auth::guard('api')->user();

        if ($user->hasNoAccess($organization)) {
            return response()->json([
                'status' => 'error',
                'alert'  => [
                    'type' => 'error',
                    'text' => 'Недостаточно прав для удаления адреса',
                ],
            ], 403);
        }

        if (!$organization->points()->whereKey($point)->exists()) {
            return response()->json([
                'status' => 'error',
                'alert'  => [
                    'type' => 'error',
                    'text' => 'Данный адрес не найден в указанной организации.',
                ],
                'action' => [
                    'type' => null,
                    'url'  => null,
                ],
            ], 404);
        }

        $point->delete();

        return response()->json([
            'status' => 'OK',
            'alert'  => [
                'type' => 'success',
                'text' => 'Адрес успешно удален.',
            ],
            'action' => [
                'type' => null,
                'url'  => null,
            ],
        ], 200);
    }


    public function mapIcon(Request $request, $color = null) {
    	return response()->view('icons.map', compact('color'))->header('Content-Type', 'image/svg+xml');
    }
}

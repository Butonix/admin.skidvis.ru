<?php
/**
 * Created by PhpStorm.
 * User: Pavel Onokhov
 * Date: 26.06.2019
 * Time: 13:34
 */

namespace App\Http\Controllers\Products;


use App\Http\Controllers\Controller;
use App\Models\Products\Holiday;
use App\Models\Products\Tag;
use Artesaos\SEOTools\Facades\SEOMeta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HolidayController extends Controller {
    /**
     * @var Holiday
     */
    protected $holidays;

    /**
     * HolidayController constructor.
     *
     * @param Holiday $holidays
     */
    public function __construct(Holiday $holidays) {
        $this->holidays = $holidays;

        $this->middleware(['permission:holidays--create'])->only(['create', 'store']);
        $this->middleware(['permission:holidays--read'])->only(['index', 'show']);
        $this->middleware(['permission:holidays--update'])->only(['edit', 'update', 'favorite']);
        $this->middleware(['permission:holidays--delete'])->only(['destroy', 'actionsDestroy']);
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request) {
        SEOMeta::setTitle('Праздники');
        $frd = $request->all();
        $holidays = $this->holidays->filter($frd)
                                   ->ordering($frd)
                                   ->paginate($frd['perPage'] ?? $this->holidays->getPerPageForAdminPanel());

        return view('products.holidays.index', compact('frd', 'holidays'));
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(Request $request) {
        SEOMeta::setTitle('Создание праздника');
        $frd = $request->all();
        $holidays = $this->holidays::orderByDesc('id')->take(20)->get();

        return view('products.holidays.create', compact('holidays'));
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     * @throws \Throwable
     */
    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255|unique:holidays',
        ], [
            'name.unique' => 'Данный праздник/выходной уже существует',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $frd = $request->all();
        $holiday = $this->holidays->create($frd);
        $frdSearch = [];

        foreach ($frd as $key => $value) {
            if ($key !== '_method' && $key !== '_token' && $key !== 'users') {
                $frdSearch[$key] = $value;
            }
        }

        $frd = $frdSearch;
        $holidays = $this->holidays->orderByDesc('id')->get();
        $html = view('products.holidays.components._lastCreatedHolidays', compact('frd', 'holidays'))->render();
        $message = [
            'message' => [
                'type' => 'success',
                'text' => 'Праздник успешно добавлен.',
            ],
            'replace' => [
                'selector' => '.js-index',
                'html'     => $html,
            ],
        ];
        $response = response()->json($message);

        return $response;
    }

    /**
     * @param Request $request
     * @param Holiday $holiday
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Request $request, Holiday $holiday) {
        SEOMeta::setTitle($holiday->getName());
        return view('products.holidays.show', compact('holiday'));
    }

    /**
     * @param Request $request
     * @param Holiday $holiday
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Request $request, Holiday $holiday) {
        SEOMeta::setTitle($holiday->getName() . ' - редактирование');
        return view('products.holidays.edit', compact('holiday'));
    }

    /**
     * @param Request $request
     * @param Holiday $holiday
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Holiday $holiday) {
        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255|unique:holidays,name,' . $holiday->getKey(),
        ], [
            'name.unique' => 'Данный праздник/выходной уже существует',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $frd = $request->only(['name', 'is_favorite']);
        $holiday->update($frd);

        if (!isset($frd['is_favorite'])) {
            $holiday->setIsFavorite(false);
        }
        $holiday->save();

        $message = [
            'type' => 'success',
            'text' => 'Праздник успешно обновлен',
        ];

        return redirect()->back()->with('flash_message', $message);
    }

    /**
     * @param Request $request
     * @param Holiday $holiday
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy(Request $request, Holiday $holiday) {
        $holiday->delete();

        $message = [
            'type' => 'success',
            'text' => 'Праздник «' . $holiday->getName() . '» успешно удален',
        ];

        return redirect()->back()->with('flash_message', $message);
    }

    /**
     * @param Request  $request
     * @param Holiday $holiday
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function favorite(Request $request, Holiday $holiday) {
        $frd = $request->only(['is_favorite']);

        $message = [
            'message' => [
                'type' => 'success'
            ]
        ];

        if (isset($frd['is_favorite']) && 'on' === $frd['is_favorite']) {
            $holiday->setIsFavorite(true);
            $holiday->save();
            $message['message']['text'] = 'Праздник «' . $holiday->getName() . '» добавлен в избранное';
        } else {
            $holiday->setIsFavorite(false);
            $holiday->save();
            $message['message']['text'] = 'Праздник «' . $holiday->getName() . '» удален из избранного';
        }

        $response = response()->json($message);

        return $response;
    }
}

<?php
/**
 * Created by PhpStorm.
 * User: Pavel Onokhov
 * Date: 26.08.2019
 * Time: 17:31
 */

namespace App\Http\Controllers\Api\Products;

use App\Http\Controllers\Controller;
use App\Http\Resources\Products\HolidayCollection;
use App\Models\Products\Holiday;
use Illuminate\Http\Request;

class HolidayController extends Controller {
    /**
     * @var
     */
    protected $holidays;

    /**
     * HolidayController constructor.
     *
     * @param Holiday $holidays
     */
    public function __construct(Holiday $holidays) {
        $this->holidays = $holidays;
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request) {
        $frd = $request->all();

        $holidays = $this->holidays->filter($frd)->ordering($frd)->paginate($frd['perPage'] ?? $this->holidays->getPerPage());

        return response()->json([
            'list' => new HolidayCollection($holidays),
        ], 200);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Organizations\Organization;
use App\Models\Products\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class WelcomeController extends Controller {
    public function __construct() {

    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function welcomePage(Request $request) {
        if (\Auth::guest()) {
            return view('welcome');
        } else {
            return redirect()->route('home');
        }
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function policy(Request $request) {
        return view('policy');
    }

    function microtime_float() {
        list($usec, $sec) = explode(" ", microtime());
        return ((float)$usec + (float)$sec);
    }

    /**
     * @throws \Exception
     */
    public function test() {

        dump(Organization::all()->keyBy('id'));
        dd(Organization::whereDoesntHave('schedule')->get()->keyBy('id'));

        dd(123123123);
    }
}

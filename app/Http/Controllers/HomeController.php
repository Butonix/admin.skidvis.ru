<?php

namespace App\Http\Controllers;

use App\Models\Users\User;
use App\Notifications\NewUser;
use Artesaos\SEOTools\Facades\SEOMeta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class HomeController extends Controller {
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index() {
        SEOMeta::setTitle('Главная');
        return view('home');
    }

    public function mail(Request $request) {
        $user = User::find(1);

        return (new NewUser())
            ->toMail($user);
    }
}

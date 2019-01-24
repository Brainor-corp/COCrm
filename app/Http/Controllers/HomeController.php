<?php

namespace App\Http\Controllers;

use App\Offer;
use App\OfferGroup;
use App\Set;
use App\User;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
//        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
//        $offer = Offer::create([
//            'name' => 'тестовый',
//            'tax' => '6',
//            'group_id' => 1,
//        ]);
        $test = Set::with('equipment')->get();
        dd($test);
        return view('home');
    }
}

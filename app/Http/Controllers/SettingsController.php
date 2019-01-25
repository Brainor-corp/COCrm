<?php
/**
 * Created by PhpStorm.
 * User: Brainor-PC-000001
 * Date: 24.01.2019
 * Time: 14:13
 */

namespace App\Http\Controllers;

use App\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller

{
    public function getTaxBySlug(Request $request){
        if(isset($request->taxSlug)){
            return Setting::where('slug', $request->taxSlug)->get();
        }
        else{
            return Setting::all();
        }
    }
}
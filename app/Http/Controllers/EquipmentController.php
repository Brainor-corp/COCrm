<?php
/**
 * Created by PhpStorm.
 * User: Brainor-PC-000001
 * Date: 24.01.2019
 * Time: 14:13
 */

namespace App\Http\Controllers;

use App\Equipment;
use Illuminate\Http\Request;

class EquipmentController extends Controller

{
    public function findEquipmentByCode(Request $request){
        if(isset($request->code)){
            return Equipment::where('code', 'like', '%' . $request->code . '%')->get();
        }
        return [];
    }
}
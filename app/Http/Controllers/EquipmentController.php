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
use Illuminate\Http\Response;

class EquipmentController extends Controller

{
    public function findEquipmentByCode(Request $request){
        if(isset($request->code)){
            return Equipment::where([['code', 'like', '%' . $request->code . '%'], ['class', 'equipment']])->with('type')->limit(10)->get();
        }
        return [];
    }

    public function findWorkByCode(Request $request){
        if(isset($request->code)){
            return Equipment::where([['code', 'like', '%' . $request->code . '%'], ['class', 'work']])->with('type')->limit(10)->get();
        }
        return [];
    }

    public function searchEquipment(Request $request) {
        $equipment = Equipment::where([['code', 'like', '%' . $request->term . '%'],['class', $request->class]])
            ->select(['id', 'code', 'name'])->limit(10)->get();

        return ['results' => $equipment];
    }
}
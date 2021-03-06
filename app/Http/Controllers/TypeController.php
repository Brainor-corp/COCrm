<?php
/**
 * Created by PhpStorm.
 * User: Brainor-PC-000001
 * Date: 24.01.2019
 * Time: 14:13
 */

namespace App\Http\Controllers;

use App\Equipment;
use App\Type;
use Illuminate\Http\Request;

class TypeController extends Controller

{
    public function getTypesByClass(Request $request) {
        return Type::where('class', $request->class)->get();
    }

    public function getAllEquipmentTypes() {
        return Type::whereIn('class', ['equipment', 'work'])->get()->groupBy('id');
    }

    public function getDefaultTypesWithEquipment(){
        return Type::where([
            ['optional', 'default'],
            ['class', 'equipment']
        ])->with('equipment')->get();
    }

    public function getDefaultWorks(){
        return Type::where('optional', 'default')->with('work')->where('class', 'work')->get();
    }
}
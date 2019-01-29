<?php

namespace App\Http\Controllers;

use App\Offer;
use App\OfferGroup;
use Barryvdh\DomPDF\PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class COController extends Controller
{
    public function display($uuid){
        if(!isset($uuid)){
            abort(404);
        }
        $offersGroup = OfferGroup::where('uuid', $uuid)->first();
        if(!isset($offersGroup)){
            abort(404);
        }
        $offers = Offer::where('group_id', $offersGroup->id)->with('equipments')->get();
        return view('pages.kpPage')->with(compact('offers', 'offersGroup'));
    }

    public function getOfferGroup(Request $request){
        $id = $request->id;
        if(!isset($id)){
            abort(404);
        }
        $offersGroup = OfferGroup::where('id', $id)->with('offers.equipments.type')->first();

        $groupedArr = $offersGroup->toArray();
        foreach ($offersGroup->offers as $key => $offer){
            $buffer = [];
            foreach ($offer->equipments as $equipment){
                if(isset($buffer[$equipment->type->name])){
                    array_push($buffer[$equipment->type->name], $equipment);
                }
                else{
                    $buffer[$equipment->type->name][] = $equipment;
                }
            }
            $groupedArr['offers'][$key]['equipments'] = $buffer;
        }

        return $groupedArr;
    }

    public  function downloadAsPdf($uuid){
        if(!isset($uuid)){
            abort(404);
        }
        $offersGroup = OfferGroup::where('uuid', $uuid)->first();
        if(!isset($offersGroup)){
            abort(404);
        }
        $offers = Offer::where('group_id', $offersGroup->id)->with('equipments')->get();
        $pdf = App::make('dompdf.wrapper');
        $vars = [
            'offers' => $offers,
            'offersGroup' => $offersGroup
        ];
        $pdf->loadView('pages.kpPDFPage', $vars);

        return $pdf->download('KommercheskoePredlojenie.pdf');
    }
}
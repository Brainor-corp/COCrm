<?php

namespace App\Http\Controllers;

use App\Offer;
use App\OfferGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class COController extends Controller
{
    public function display($uuid){
        if(!isset($uuid)){
            abort(404);
        }

        $offersGroup = OfferGroup::where('uuid', $uuid)->with('offers.equipments')->first();

        if(!isset($offersGroup)){
            abort(404);
        }

        return view('pages.kpPage')->with(compact( 'offersGroup'));
    }

    public function getOfferGroup(Request $request){
        if(!isset($request->id)){
            abort(404);
        }

        $offersGroup = OfferGroup::where('id', $request->id)->with('offers.equipments.type')->first();
        $groupedArr = $offersGroup->toArray();

        foreach ($offersGroup->offers as $key => $offer) {
            $groupedArr['offers'][$key]['equipments'] = $offer->equipments->groupBy('type.name');
        }

        return $groupedArr;
    }

    public  function downloadAsPdf($uuid){
        if(!isset($uuid)){
            abort(404);
        }

        $offersGroup = OfferGroup::where('uuid', $uuid)->with('offers.equipments')->first();

        if(!isset($offersGroup)){
            abort(404);
        }

        $pdf = App::make('dompdf.wrapper');
        $vars = [
            'offersGroup' => $offersGroup
        ];
        $pdf->loadView('pages.kpPDFPage', $vars);

        return $pdf->download('KommercheskoePredlojenie.pdf');
    }
}
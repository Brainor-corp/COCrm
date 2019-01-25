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
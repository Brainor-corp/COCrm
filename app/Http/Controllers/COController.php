<?php

namespace App\Http\Controllers;

use App\Offer;
use App\OfferGroup;
use App\Setting;
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

        $offersGroup = OfferGroup::where('id', $request->id)->with('offers.equipments.type', 'equipment')->first();
        $groupedArr = $offersGroup->toArray();

        foreach ($offersGroup->offers as $key => $offer) {
            $groupedArr['offers'][$key]['equipments'] = $offer->equipments->groupBy('type.name');
        }

        return $groupedArr;
    }

    public function downloadAsPdf($uuid){
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

    public function calculateAllPrices(Request $request){
        $offer_group = json_decode($request->offer_group, true);
        $response['adjustmentPrice'] = self::calculateAdjustmentPrice($offer_group->adjusters);
        $response['noTaxProfit'] = self::calculateNoTaxProfit($response['adjustmentPrice'], $offer_group->adjusters->pay_percentage);
        $response['VAT'] = self::calculateVAT($response['noTaxProfit']);
        $response['additionalDiscount'] = self::calculateAdditionalDiscount($response['VAT']);
        return $response;
    }

    private function calculateAdjustmentPrice($adjustment){
        return ($adjustment->adjusters_number * $adjustment->adjustment_days * $adjustment->adjusters_wage) + ($adjustment->adjustment_days + $adjustment->fuel);
    }

    private function calculateNoTaxProfit($price, $percentage){
        return ($price * (100 - $percentage) / $percentage) + $price;
    }

    private function calculateVAT($noTaxProfit){
        $taxMaxPercentage = Setting::max('value')->value;
        return ($noTaxProfit * $taxMaxPercentage / 100) * 20 / 100;
    }

    private function calculateAdditionalDiscount($VAT){
        $taxMaxPercentage = Setting::max('value')->value;
        $taxMinPercentage = Setting::min('value')->value;
        return $taxMaxPercentage - $taxMinPercentage - $VAT;
    }
}
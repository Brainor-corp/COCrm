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
        try{
            $response['equipmentPrice'] = self::calculateEquipmentPrice($request['offer_group']['offers']);
            $response['consumablePrice'] = self::calculateConsumablePrice($request['offer_group']['offers']);
            $response['adjustmentPrice'] = self::calculateAdjustmentPrice($request['offer_group']['adjusters']);
            $response['noTaxProfit'] = self::calculateNoTaxProfit($response['adjustmentPrice'], $request['offer_group']['adjusters']['pay_percentage']);
            $response['VAT'] = self::calculateVAT($response['noTaxProfit']);
            $response['totalWorkPrice'] = self::calculateTotalWorkPrice($response['noTaxProfit']);
            $response['totalWorkPriceNoVAT'] = self::calculateTotalWorkPriceNoVAT($response['noTaxProfit']);
            $response['additionalDiscount'] = ($response['noTaxProfit']+$response['noTaxProfit']*40/100) - $response['totalWorkPriceNoVAT'] - $response['VAT'];
//            $response['additionalDiscount'] = self::calculateAdditionalDiscount($response['noTaxProfit'],$response['VAT']);
        }
        catch (\Exception $e){
            return $e;
        }
        return $response;
    }

    private function calculateEquipmentPrice($offers){
        $totalPrice=0;
        foreach ($offers as $offer){
            foreach ($offer['equipments'] as $equipment_tab){
                foreach ($equipment_tab as $equipment){
                    if($equipment['type'] !== 'rashodnye-materialy'){
                        $totalPrice+= $equipment['price'] * $equipment['quantity'];
                    }
                }
            }
        }
        return $totalPrice;
    }

    private function calculateConsumablePrice($offers){
        $totalPrice=0;
        foreach ($offers as $offer){
            foreach ($offer['equipments'] as $equipment_tab){
                foreach ($equipment_tab as $equipment){
                    if($equipment['type'] === 'rashodnye-materialy'){
                        $totalPrice+= $equipment['price'] * $equipment['quantity'];
                    }
                }
            }
        }
        return $totalPrice;
    }

    private function calculateAdjustmentPrice($adjustment){
        return ($adjustment['adjusters_number'] * $adjustment['adjustment_days'] * $adjustment['adjusters_wage']) + ($adjustment['adjustment_days'] * $adjustment['fuel']);
    }

    private function calculateNoTaxProfit($price, $percentage){
        return (($price * (100 - $percentage)) / $percentage) + $price;
    }

    private function calculateVAT($noTaxProfit){
        $taxes = Setting::where('class', 'tax')->get();
        $value = function($item) {
            return intval($item->value);
        };
        $taxMaxPercentage = $taxes->max($value);
        return ($noTaxProfit + ($noTaxProfit * $taxMaxPercentage / 100)) * 20 / 120;
    }

    private function calculateAdditionalDiscount($noTaxProfit, $VAT){
        $taxes = Setting::where('class', 'tax')->get();
        $value = function($item) {
            return intval($item->value);
        };
        $taxMaxPercentage = $taxes->max($value);
        $taxMinPercentage = $taxes->min($value);
        return ($noTaxProfit + ($noTaxProfit * $taxMaxPercentage / 100)) - $VAT - ($noTaxProfit + ($noTaxProfit * $taxMinPercentage / 100));
    }

    private function calculateTotalWorkPrice($noTaxProfit){
        $taxes = Setting::where('class', 'tax')->get();
        $value = function($item) {
            return intval($item->value);
        };
        $taxMaxPercentage = $taxes->max($value);
        return ($noTaxProfit + ($noTaxProfit * $taxMaxPercentage / 100));
    }

    private function calculateTotalWorkPriceNoVAT($noTaxProfit){
        return round($noTaxProfit * 1.0638297873);
    }
}
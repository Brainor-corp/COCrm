<?php

namespace App\Http\Controllers;

use App\DefaultTab;
use App\Offer;
use App\OfferGroup;
use App\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;

class COController extends Controller
{
    public function display($uuid){
        if(!isset($uuid)){
            abort(404);
        }

        $offersGroup = OfferGroup::where('uuid', $uuid)->with('offers.equipments', 'equipment', 'user')->first();

        if(!isset($offersGroup)){
            abort(404);
        }

        return view('pages.kpPage')->with(compact( 'offersGroup'));
    }

    public function getDefaultWorkTabs(){
        return DefaultTab::where('class', 'work')->with('equipments')->orderBy('order')->get();
    }

    public function getDefaultEquipmentTabs(){
        return DefaultTab::where('class', 'equipment')->with('equipments')->orderBy('order')->get();
    }

    public function getOfferGroup(Request $request){
        if(!isset($request->id)){
            abort(404);
        }

        $offersGroup = OfferGroup::where('id', $request->id)->with('offers.equipments', 'equipment', 'user')->first();
        $groupedArr = $offersGroup->toArray();

        foreach ($offersGroup->equipment->groupBy('pivot.tab_slug')->values() as $key => $work){
            $workBuff[$key]['equipments'] = $work;
            $workBuff[$key]['name'] = $work->first()->pivot->tab_name;
            $workBuff[$key]['slug'] = $work->first()->pivot->tab_slug;
        }
        $groupedArr['works'] = $workBuff;
//        $groupedArr['works'] = $offersGroup->equipment;

        foreach ($offersGroup->offers as $offerIndex => $offer) {
            $buff1 = $offer->equipments->groupBy('pivot.tab_slug')->values();
            foreach ($buff1 as $key => $value){
                $buff2['equipments'] = $value;
                $buff2['name'] = $value->first()->pivot->tab_name;
                $buff2['slug'] = $value->first()->pivot->tab_slug;
                $buff1[$key] = $buff2;
            }
            $groupedArr['offers'][$offerIndex]['equipments'] = $buff1;
        }

        return $groupedArr;
    }

    public function downloadAsPdf($uuid){
        if(!isset($uuid)){
            abort(404);
        }

        $offersGroup = OfferGroup::where('uuid', $uuid)->with('offers.equipments', 'user')->first();

        if(!isset($offersGroup)){
            abort(404);
        }

        $pdf = App::make('dompdf.wrapper');
        $vars = [
            'offersGroup' => $offersGroup
        ];
        $pdf->loadView('pages.kpPDFPage', $vars);

        return $pdf->download('Kommercheskoe Predlojenie '.Carbon::now()->addHours(3)->format('d.m.Y h-i').'.pdf');
    }

    public function calculateAllPrices(Request $request){
        $response = [];
        $response['adjustmentPrice'] = self::calculateAdjustmentPrice($request['offer_group']['adjusters']) ?? null;
        $response['workNumber'] = self::calculateWorkNumber($request['offer_group']['works']) ?? null;
        foreach ($request['offer_group']['offers'] as $key => $offer) {
            try {
                $response[$key]['equipmentPrice'] = round(self::calculateEquipmentPrice($offer), 2);
                $response[$key]['consumablePrice'] = round(self::calculateConsumablePrice($offer), 2);
                $response[$key]['noTaxProfit'] = $request['offer_group']['adjusters']['adjusters_no_tax'] ?? round(self::calculateNoTaxProfit($response['adjustmentPrice'], $request['offer_group']['adjusters']['pay_percentage']), 2);
                $response[$key]['VAT'] = round(self::calculateVAT($response[$key]['noTaxProfit']), 2);
                $response[$key]['totalWorkPrice'] = round(self::calculateTotalWorkPrice($response[$key]['noTaxProfit']), 2);
                $response[$key]['totalWorkPriceNoVAT'] = round(self::calculateTotalWorkPriceNoVAT($response[$key]['noTaxProfit']), 2);
                $response[$key]['additionalDiscount'] = round(($response[$key]['noTaxProfit'] + $response[$key]['noTaxProfit'] * 40 / 100) - $response[$key]['totalWorkPriceNoVAT'] - $response[$key]['VAT'], 2);
//            $response['additionalDiscount'] = self::calculateAdditionalDiscount($response['noTaxProfit'],$response['VAT']);
            } catch (\Exception $e) {
                return $e;
            }
        }
        return $response;
    }
    public function getOfferGroupTemplates(Request $request){

        $offerGroupTemplates = OfferGroup::where('template', 1)->get()->toArray();

        return $offerGroupTemplates;
    }

    public function calculatePrePrices(Request $request){
        $response['adjustmentPrice'] = self::calculateAdjustmentPrice($request['offer_group']['adjusters']);
        $response['noTaxProfit'] = $request['offer_group']['adjusters']['adjusters_no_tax'] ?? round(self::calculateNoTaxProfit($response['adjustmentPrice'], $request['offer_group']['adjusters']['pay_percentage']), 2);
        $response['VAT'] = round(self::calculateVAT($response['noTaxProfit']), 2);
        $response['totalWorkPrice'] = round(self::calculateTotalWorkPrice($response['noTaxProfit']), 2);
        $response['totalWorkPriceNoVAT'] = round(self::calculateTotalWorkPriceNoVAT($response['noTaxProfit']), 2);
        $response['additionalDiscount'] = round(($response['noTaxProfit'] + $response['noTaxProfit'] * 40 / 100) - $response['totalWorkPriceNoVAT'] - $response['VAT'], 2);
        return $response;
    }

    private function calculateEquipmentPrice($offer){
        $totalPrice=0;
        foreach ($offer['equipments'] as $type => $equipments){
            foreach ($equipments['equipment'] as $equipment){
                if($type !== 'rashodnye-materialy'){
                    $totalPrice += $equipment['price'] * $equipment['quantity'];
//                    $totalPrice += ((($equipment['price_small_trade'] - $equipment['price_special'])/2) + $equipment['price_special']) * $equipment['quantity'];
                }
            }
        }
        return $totalPrice;
    }

    private function calculateConsumablePrice($offer){
        $totalPrice=0;
        foreach ($offer['equipments'] as $type => $equipments){
            foreach ($equipments['equipment'] as $equipment){
                if($type === 'rashodnye-materialy'){
                    $totalPrice += $equipment['price'] * $equipment['quantity'];
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
        return round($noTaxProfit + ($noTaxProfit * $taxMaxPercentage / 100), 2);
    }

    private function calculateTotalWorkPriceNoVAT($noTaxProfit){
        return round($noTaxProfit * 1.0638297873);
    }

    private function calculateWorkNumber($works){
        $counter = 0;
        foreach ($works as $workTab){
            foreach($workTab['work'] as $work){
                $counter++;
            }
        }
        return $counter;
    }
}
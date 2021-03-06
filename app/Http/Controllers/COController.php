<?php

namespace App\Http\Controllers;

use App\DefaultTab;
use App\Equipment;
use App\OfferGroup;
use App\Setting;
use Convertio\Exceptions\APIException;
use Convertio\Exceptions\CURLException;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
use \Convertio\Convertio;
use Illuminate\Support\Facades\File;

class COController extends Controller
{
    public function display($uuid){
        if(!isset($uuid)){
            abort(404);
        }

        $maxString = Setting::where('slug', 'maksimalnoe-kolichestvo-strok-v-odnom-variante-kp')->first()->value;

        $offersGroup = OfferGroup::where('uuid', $uuid)->with('offers.equipments', 'equipment', 'user')->firstOrFail();

        if(!isset($offersGroup)){
            abort(404);
        }

        return view('pages.newKP')->with(compact( 'offersGroup', 'pdf', 'maxString'));
    }

    public function getDefaultWorkTabs(){
        return DefaultTab::where([['class', 'work'], ['display', true]])->with('equipments')->orderBy('order')->get();
    }

    public function getDefaultEquipmentTabs(){
        return DefaultTab::where([['class', 'equipment'], ['display', true]])->with('equipments')->orderBy('order')->get();
    }

    public function getOfferGroup(Request $request){
        if(!isset($request->id)){
            abort(404);
        }

        $offersGroup = OfferGroup::where('id', $request->id)->with('offers.equipments', 'equipment', 'user')->first();
        $groupedArr = $offersGroup->toArray();

        $workBuff = [];
        $works = $offersGroup->equipment->groupBy('pivot.tab_slug')->values();
        if(isset($works)) {
            foreach ($works as $key => $work) {
                $workBuff[$key]['equipments'] = $work;
                $workBuff[$key]['name'] = $work->first()->pivot->tab_name;
                $workBuff[$key]['slug'] = $work->first()->pivot->tab_slug;
            }
        }
        $groupedArr['works'] = $workBuff;

        foreach ($offersGroup->offers as $offerIndex => $offer) {
            $equipments = $offer->equipments->groupBy('pivot.tab_slug')->values();
            if(isset($equipments)) {
                $buff1 = $equipments;
                foreach ($buff1 as $key => $value) {
                    $buff2['equipments'] = $value;
                    $buff2['name'] = $value->first()->pivot->tab_name;
                    $buff2['slug'] = $value->first()->pivot->tab_slug;
                    $buff1[$key] = $buff2;
                }
                $groupedArr['offers'][$offerIndex]['equipments'] = $buff1;
            } else {
                $groupedArr['offers'][$offerIndex]['equipments'] = [];
            }
        }

        return $groupedArr;
    }

    public function downloadAsPdf($uuid){
        File::delete(File::allfiles(public_path('savedKPs/')));

        if(!isset($uuid)){
            abort(404);
        }

        $pdfToken = Setting::where([['class', 'token'], ['slug', 'token-pdf-generatora']])->first();
        if(!isset($pdfToken->value)){
            return 'Сначала укажите реальный токен для генератора pdf';
        }
        else{
            $pdfToken = $pdfToken->value;
        }
        try {
            $API = new Convertio($pdfToken);
        } catch (APIException $e) {
            return $e->getMessage();
        }
        try {
            $API->startFromURL(route('showCO', ['uuid' => $uuid]) . '?pdf=1', 'pdf')
                ->wait()
                ->download('savedKPs/' . $uuid . '.pdf')
                ->delete();
        } catch (APIException $e) {
            return $e->getMessage();
        } catch (CURLException $e) {
            return $e->getMessage();
        } catch (\Exception $e) {
            return $e->getMessage();
        }

        return response()->download(public_path('savedKPs/' . $uuid . '.pdf'));
    }

    public function calculateAllPrices(Request $request){
        $response = [];
        try {
            $response['maxEquipmentsNumber'] = Setting::where('slug', 'maksimalnoe-kolichestvo-strok-v-odnom-variante-kp')->first()->value;
            $response['adjustmentPrice'] = self::calculateAdjustmentPrice($request['offer_group']['adjusters']) ?? null;
            if(isset($request['offer_group']['works'])) {
                $response['workNumber'] = self::calculateWorkNumber($request['offer_group']['works']) ?? null;
            }
            foreach ($request['offer_group']['offers'] as $key => $offer) {
                $response[$key]['equipmentsNumber'] = self::calculateEquipmentsNumber($offer);
                $response[$key]['equipmentPrice'] = round(self::calculateEquipmentPrice($offer), 2);
                $response[$key]['consumablePrice'] = round(self::calculateConsumablePrice($offer), 2);
                $response[$key]['noTaxProfit'] = $request['offer_group']['adjusters']['adjusters_no_tax'] ?? round(self::calculateNoTaxProfit($response['adjustmentPrice'], $request['offer_group']['adjusters']['pay_percentage']), 2);
                $response[$key]['VAT'] = round(self::calculateVAT($response[$key]['noTaxProfit']), 2);
                $response[$key]['totalWorkPrice'] = round(self::calculateTotalWorkPrice($response[$key]['noTaxProfit']), 2);
                $response[$key]['totalWorkPriceNoVAT'] = round(self::calculateTotalWorkPriceNoVAT($response[$key]['noTaxProfit']), 2);
                $response[$key]['additionalDiscount'] = round(($response[$key]['noTaxProfit'] + $response[$key]['noTaxProfit'] * 40 / 100) - $response[$key]['totalWorkPriceNoVAT'] - $response[$key]['VAT'], 2);
//                $response[$key]['additionalDiscount'] = self::calculateAdditionalDiscount($response[$key]['noTaxProfit'],$response[$key]['VAT']);

            }
        } catch (\Exception $e) {
            return $e;
        }
        return $response;
    }
    public function getOfferGroupTemplates(Request $request){

        $offerGroupTemplates = OfferGroup::where('template', 1)->get()->toArray();

        return $offerGroupTemplates;
    }

    public function calculatePrePrices(Request $request){
        try {
            $response['adjustmentPrice'] = self::calculateAdjustmentPrice($request['offer_group']['adjusters']);
            $response['noTaxProfit'] = $request['offer_group']['adjusters']['adjusters_no_tax'] ?? round(self::calculateNoTaxProfit($response['adjustmentPrice'], $request['offer_group']['adjusters']['pay_percentage']), 2);
            $response['VAT'] = round(self::calculateVAT($response['noTaxProfit']), 2);
            $response['totalWorkPrice'] = round(self::calculateTotalWorkPrice($response['noTaxProfit']), 2);
            $response['totalWorkPriceNoVAT'] = round(self::calculateTotalWorkPriceNoVAT($response['noTaxProfit']), 2);
            $response['additionalDiscount'] = round(($response['noTaxProfit'] + $response['noTaxProfit'] * 40 / 100) - $response['totalWorkPriceNoVAT'] - $response['VAT'], 2);
        }
        catch( \Exception $e){
            return $e;
        }
        return $response;
    }

    private function calculateEquipmentPrice($offer){
        $totalPrice=0;
        if(isset($offer['equipments'])) {
            foreach ($offer['equipments'] as $type => $equipments) {
                if (isset($equipments['equipment'])) {
                    foreach ($equipments['equipment'] as $equipment) {
                        if ($type !== 'rashodnye-materialy') {
                            $totalPrice += $equipment['price'] * $equipment['quantity'];
                        }
                    }
                }
            }
        }
        return $totalPrice;
    }

    private function calculateConsumablePrice($offer){
        $totalPrice=0;
        if(isset($offer['equipments'])) {
            foreach ($offer['equipments'] as $type => $equipments) {
                if (isset($equipments['equipment'])) {
                    foreach ($equipments['equipment'] as $equipment) {
                        if ($type === 'rashodnye-materialy') {
                            $totalPrice += $equipment['price'] * $equipment['quantity'];
                        }
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
        return round($noTaxProfit + ($noTaxProfit * $taxMaxPercentage / 100), 2);
    }

    private function calculateTotalWorkPriceNoVAT($noTaxProfit){
        return round($noTaxProfit * 1.0638297873);
    }

    private function calculateWorkNumber($works){
        $counter = 0;
        foreach ($works as $workTab){
            if(isset($workTab['work'])){
                foreach($workTab['work'] as $work){
                    if($work['quantity'] > 0){
                        $counter++;
                    }
                }
            }
        }
        return $counter;
    }

    private function calculateEquipmentsNumber($offer){
        $count = 0;
        if($offer['equipments']){
            foreach ($offer['equipments'] as $type => $equipment){
                if($equipment['equipment'] && $type != 'rashodnye-materialy'){
                    foreach ($equipment['equipment'] as $eq){
                        if(isset($eq) && $eq['quantity'] > 0){
                            $count++;
                        }
                    }
                }
            }
        }
        return $count;
    }
}
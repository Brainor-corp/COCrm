<?php
/**
 * Created by PhpStorm.
 * User: Brainor-PC-000001
 * Date: 24.01.2019
 * Time: 14:13
 */

namespace App\Http\Controllers;

use App\Equipment;
use App\Offer;
use App\OfferGroup;
use Illuminate\Http\Request;

class OfferController extends Controller

{
    public function saveOfferGroup(Request $request){
        $group = $request->offer_group;
        if(!isset($group)){
            return "no";
        }
        $createGroup = new OfferGroup();
        foreach ($group as $item => $value){
            if($item == 'name'){
                $createGroup->name = $value;
            }
            elseif($item == 'offer'){
                foreach ($value as $offers){
                    foreach ($offers as $offer){
                        $createOffer = new Offer();
                        foreach ($offer as $key => $value){
                            switch ($key){
                                case 'equipments':
                                    foreach ($value as $equipment){
                                        if($equipment->id === 'new'){
                                            $createEquipment = new Equipment();
                                            $createEquipment->code = $equipment->code;
                                            $createEquipment->name = $equipment->name;
                                            $createEquipment->description = $equipment->description;
                                            $createEquipment->quantity = $equipment->quantity;
                                            $createEquipment->points = $equipment->points;
                                            $createEquipment->price = $equipment->price;
                                            $createEquipment->price_trade = $equipment->price_trade;
                                            $createEquipment->price_small_trade = $equipment->price_small_trade;
                                            $createEquipment->price_special = $equipment->price_special;
                                            $createEquipment->class = $equipment->class;
                                            $createEquipment->save();
                                        }
                                        else{
                                            $createOffer->equipment()->attach($equipment, array(
                                                'quantity' => $equipment->quantity,
                                                'price' => $equipment->price,
                                                'price_trade' => $equipment->price_trade,
                                                'price_small_trade' => $equipment->price_small_trade,
                                                'price_special' => $equipment->price_special,
                                                'comment' => $equipment->comment,
                                            ));
                                        }
                                    }
                                    break;
                                case 'works':
                                    foreach ($value as $work){
                                        if($work->id === 'new'){
                                            $createWork = new Equipment();
                                            $createWork->code = $work->code;
                                            $createWork->name = $work->name;
                                            $createWork->description = $work->description;
                                            $createWork->quantity = $work->quantity;
                                            $createWork->points = $work->points;
                                            $createWork->price = $work->price;
                                            $createWork->price_trade = $work->price_trade;
                                            $createWork->price_small_trade = $work->price_small_trade;
                                            $createWork->price_special = $work->price_special;
                                            $createWork->class = $work->class;
                                            $createWork->save();
                                        }
                                        else{
                                            $createOffer->equipment()->attach($work, array(
                                                'quantity' => $work->quantity,
                                                'price' => $work->price,
                                                'price_trade' => $work->price_trade,
                                                'price_small_trade' => $work->price_small_trade,
                                                'price_special' => $work->price_special,
                                                'comment' => $work->comment,
                                            ));
                                        }
                                    }
                                    break;
                                case 'tax':
                                    $createOffer->tax = $value;
                                    break;
                                case 'name':
                                    $createOffer->name = $value;
                                    break;
                            }
                        }
                        $createOffer->save();//todo заполнение оффера
                    }
                }
            }
        }
        return 'ok';
    }
}
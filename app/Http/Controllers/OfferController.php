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
use App\Type;
use Illuminate\Http\Request;

class OfferController extends Controller
{
    public function saveOfferGroup(Request $request){
        $group = $request->offer_group;
        if(!isset($group)){
            return "no";
        }
        try{
            $createGroup = new OfferGroup();
            $createGroup->name = $group['name'];
            $createGroup->adjusters_no_tax = $group['adjusters']['adjusters_no_tax'] ?? null;
            $createGroup->adjusters_number = $group['adjusters']['adjusters_number'];
            $createGroup->adjustments_days = $group['adjusters']['adjustment_days'];
            $createGroup->fuel_number = $group['adjusters']['fuel'];
            $createGroup->adjusters_wage = $group['adjusters']['adjusters_wage'];
            $createGroup->pay_percentage = $group['adjusters']['pay_percentage'];
            $createGroup->save();
            foreach ($group['offers'] as $offer){
                $createOffer = new Offer();
                $createOffer->group_id = $createGroup->id;
                $createOffer->name = $offer['name'];
                $createOffer->save();
                foreach ($offer['equipments'] as $equipment_tab){
                    foreach($equipment_tab as $equipment){
                        if($equipment['base_id'] === '-1'){
                            $createEquipment = new Equipment();
                            $createEquipment->code = $equipment['code'];
                            $createEquipment->name = $equipment['name'];
                            $createEquipment->description = $equipment['description'];
                            $createEquipment->points = $equipment['points'];
                            $createEquipment->price = $equipment['price'];
                            $createEquipment->price_trade = $equipment['price_trade'];
                            $createEquipment->price_small_trade = $equipment['price_small_trade'];
                            $createEquipment->price_special = $equipment['price_special'];
                            $createEquipment->class = Type::where('slug', $equipment['type'])->first()->class;
                            $createEquipment->type_id = Type::where('slug', $equipment['type'])->first()->id;
                            $createEquipment->save();

                            $createOffer->equipments()->attach($createEquipment->id, array(
                                'quantity' => $equipment['quantity'],
                                'price' => $equipment['price'],
                                'price_trade' => $equipment['price_trade'],
                                'price_small_trade' => $equipment['price_small_trade'],
                                'price_special' => $equipment['price_special'],
                                'counted_price' => $equipment['counted_price'],
                                'comment' => $equipment['comment'],
                            ));
                        }
                        else{
                            $createOffer->equipments()->attach($equipment['base_id'], array(
                                'quantity' => $equipment['quantity'],
                                'price' => $equipment['price'],
                                'price_trade' => $equipment['price_trade'],
                                'price_small_trade' => $equipment['price_small_trade'],
                                'price_special' => $equipment['price_special'],
                                'counted_price' => $equipment['counted_price'],
                                'comment' => $equipment['comment'],
                            ));
                        }
                    }
                }
            }
            foreach ($group['works'] as $work) {
                if($work['id'] === '-1') {
                    $createWork = new Equipment();
                    $createWork->code = $work['code'];
                    $createWork->name = $work['name'];
                    $createWork->points = $work['points'];
                    $createWork->description = '';
                    $createWork->price = 0;
                    $createWork->price_trade = 0;
                    $createWork->price_small_trade = 0;
                    $createWork->price_special = 0;
                    $createWork->class = 'work';
                    $createWork->type_id = Type::where('slug', 'rabota')->first()->id;
                    $createWork->save();

                    $createGroup->equipment()->attach($createWork->id, array(
                        'quantity' => $work['quantity'],
                    ));
                }
                else{
                    $createGroup->equipment()->attach($work['id'], array(
                        'quantity' => $work['quantity'],
                    ));
                }
            }
        }
        catch (\Exception $e){
            abort(500);
        }
        return url('kp/' . $createGroup->uuid);
    }
}
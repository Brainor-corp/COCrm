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
        try{
            $group = $request->offer_group;
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
            throw new \Exception($e);
        }
        return url('kp/' . $createGroup->uuid);
    }

    public function updateOfferGroup(Request $request){
        try {
            $buffWork = [];
            $newOfferGroup = $request[0]['offer_group'];
            $offerGroup = OfferGroup::where('id', $request[1])->with('offers.equipments.type', 'equipment')->first();
            $offerGroup->adjusters_no_tax = $newOfferGroup['adjusters']['adjusters_no_tax'];
            $offerGroup->adjusters_number = $newOfferGroup['adjusters']['adjusters_number'];
            $offerGroup->adjusters_wage = $newOfferGroup['adjusters']['adjusters_wage'];
            $offerGroup->adjustments_days = $newOfferGroup['adjusters']['adjustment_days'];
            $offerGroup->fuel_number = $newOfferGroup['adjusters']['fuel'];
            $offerGroup->pay_percentage = $newOfferGroup['adjusters']['pay_percentage'];
            $offerGroup->name = $newOfferGroup['name'];
            $oldOffers = Offer::where('group_id', $offerGroup->id)->get();
            foreach ($oldOffers as $oldOffer) {
                $oldOffer->equipments()->detach();
                $oldOffer->delete();
            }
            foreach ($newOfferGroup['offers'] as $offer) {
                $createOffer = new Offer();
                $createOffer->group_id = $offerGroup->id;
                $createOffer->name = $offer['name'];
                $createOffer->save();
                foreach ($offer['equipments'] as $type => $equipment_tab) {
                    foreach ($equipment_tab as $equipment) {
                        if ($equipment['base_id'] === '-1') {
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
                                'type' => $type,
                            ));
                        } else {
                            $createOffer->equipments()->attach($equipment['base_id'], array(
                                'quantity' => $equipment['quantity'],
                                'price' => $equipment['price'],
                                'price_trade' => $equipment['price_trade'],
                                'price_small_trade' => $equipment['price_small_trade'],
                                'price_special' => $equipment['price_special'],
                                'counted_price' => $equipment['counted_price'],
                                'comment' => $equipment['comment'],
                                'type' => $type,
                            ));
                        }
                    }
                }
            }
            foreach ($newOfferGroup['works'] as $work) {
                if ($work['id'] === '-1') {
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

                    $buffWork[$createWork->id] = ['quantity' => $work['quantity']];
                } else {
                    $buffWork[$work['id']] = ['quantity' => $work['quantity']];
                }
            }
            $offerGroup->equipment()->sync($buffWork);
            $offerGroup->save();
        }
        catch (\Exception $e){
            throw new \Exception($e);
        }
        return url('kp/' . $offerGroup->uuid);
    }
}
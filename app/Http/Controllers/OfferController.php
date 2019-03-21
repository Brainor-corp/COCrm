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
use Cviebrock\EloquentSluggable\Services\SlugService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OfferController extends Controller
{
    private function validateOfferGroup($group){
        $errorMsg = null;
        if(!$group['name']){
            $errorMsg = 'Введите название КП.';
        }
        foreach ($group['offers'] as $offer) {
            if(!$offer['name']){
                $errorMsg = 'Введите название для всех Вариантов КП.';
            }

            if(isset($offer['equipments'])) {
                foreach ($offer['equipments'] as $type => $equipment_tab) {
                    if (isset($equipment_tab['equipment'])) {
                        foreach ($equipment_tab['equipment'] as $equipment) {
                            if (empty($equipment['code'])) {
                                $errorMsg = 'Поле "Артикул" у оборудования обязательно.';
                            }
                            if (empty($equipment['name'])) {
                                $errorMsg = 'Поле "Название" у оборудования обязательно.';
                            }
                            if (empty($equipment['points'])) {
                                $errorMsg = 'Поле "Ед. измерения" у оборудования обязательно.';
                            }
                            if (empty($equipment['price'])) {
                                $errorMsg = 'Поле "Цена" у оборудования обязательно.';
                            }
                            if (empty($equipment['price_trade'])) {
                                $errorMsg = 'Поле "Розн. цена" у оборудования обязательно.';
                            }
                            if (empty($equipment['price_small_trade'])) {
                                $errorMsg = 'Поле "3 колонка" у оборудования обязательно.';
                            }
                            if (empty($equipment['price_special'])) {
                                $errorMsg = 'Поле "Спец. цена" у оборудования обязательно.';
                            }
                        }
                    }
                }
            }
        }
        if(isset($group['works'])) {
            foreach ($group['works'] as $workTab) {
                if (empty($workTab['work'])) {
                    foreach ($workTab['work'] as $work) {
                        if (empty($work['name'])) {
                            $errorMsg = 'Поле "Название" у работы обязательно.';
                        }
                        if (empty($work['points'])) {
                            $errorMsg = 'Поле "Ед. измерения" у работы обязательно.';
                        }
                    }
                } else {
                    $errorMsg = 'Добавьте работы в КП.';
                }
            }
        }
        else{
            $errorMsg = 'Добавьте работы в КП.';
        }
        return $errorMsg;
    }

    public function saveOfferGroup(Request $request){
        $userId = Auth::user()->id;
        $group = $request->offer_group;
        $error = self::validateOfferGroup($group);
        if($error){
            throw new \Exception($error);
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
            $createGroup->user_id = $userId;
            $createGroup->save();
            foreach ($group['offers'] as $offer){
                if(isset($offer['equipments'])) {
                    $createOffer = new Offer();
                    $createOffer->group_id = $createGroup->id;
                    $createOffer->name = $offer['name'];
                    $createOffer->description = $offer['description'];
                    $createOffer->save();
                    foreach ($offer['equipments'] as $type => $equipment_tab) {
                        foreach ($equipment_tab['equipment'] as $equipment) {
                            if ($equipment['base_id'] === '-1') {
                                $createEquipment = new Equipment();
                                $createEquipment->code = $equipment['code'];
                                $createEquipment->name = $equipment['name'];
                                $createEquipment->short_description = $equipment['short_description'];
                                $createEquipment->points = $equipment['points'];
                                $createEquipment->price = $equipment['price'];
                                $createEquipment->price_trade = $equipment['price_trade'];
                                $createEquipment->price_small_trade = $equipment['price_small_trade'];
                                $createEquipment->price_special = $equipment['price_special'];
                                $createEquipment->class = 'equipment';
//                            $createEquipment->type_id = Type::where('slug', $equipment['type'])->first()->id;
                                $createEquipment->save();

                                $createOffer->equipments()->attach($createEquipment->id, array(
                                    'quantity' => $equipment['quantity'],
                                    'price' => $equipment['price'],
                                    'price_trade' => $equipment['price_trade'],
                                    'price_small_trade' => $equipment['price_small_trade'],
                                    'price_special' => $equipment['price_special'],
                                    'counted_price' => $equipment['counted_price'],
                                    'comment' => $equipment['comment'],
                                    'tab_slug' => $type,
                                    'tab_name' => $equipment_tab['name'],
                                ));
                            } else {
//                                $oldEquipment = Equipment::where('id', $equipment['base_id'])->first();
//                                $oldEquipment->short_description = $equipment['short_description'];
//                                $oldEquipment->save();

                                $createOffer->equipments()->attach($equipment['base_id'], array(
                                    'quantity' => $equipment['quantity'],
                                    'price' => $equipment['price'],
                                    'price_trade' => $equipment['price_trade'],
                                    'price_small_trade' => $equipment['price_small_trade'],
                                    'price_special' => $equipment['price_special'],
                                    'counted_price' => $equipment['counted_price'],
                                    'comment' => $equipment['comment'],
                                    'tab_slug' => $type,
                                    'tab_name' => $equipment_tab['name'],
                                ));
                            }
                        }
                    }
                }
            }
            if(isset($group['works'])) {
                foreach ($group['works'] as $workTabKey => $workTab) {
                    if (isset($workTab['work'])) {
                        foreach ($workTab['work'] as $work) {
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
//                    $createWork->type_id = Type::where('slug', 'rabota')->first()->id;
                                $createWork->save();

                                $createGroup->equipment()->attach($createWork->id, array(
                                    'quantity' => $work['quantity'],
                                    'tab_name' => $workTab['name'],
                                    'tab_slug' => $workTabKey,
                                ));
                            } else {
                                $createGroup->equipment()->attach($work['id'], array(
                                    'quantity' => $work['quantity'],
                                    'tab_name' => $workTab['name'],
                                    'tab_slug' => $workTabKey,
                                ));
                            }
                        }
                    }
                }
            }
            else{
                throw new \Exception('Добавьте работы в КП.');
            }
        }
        catch (\Exception $e){
            throw new \Exception('Произошла ошибка. Пожалуйста, обновите страницу и попробуйте снова.');
//            throw $e;
        }
        return url('kp/' . $createGroup->uuid);
    }

    public function updateOfferGroup(Request $request){
        $error = self::validateOfferGroup($request[0]['offer_group']);
        if($error){
            throw new \Exception($error);
        }
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
                if(isset($offer['equipments'])) {
                    $createOffer = new Offer();
                    $createOffer->group_id = $offerGroup->id;
                    $createOffer->name = $offer['name'];
                    $createOffer->description = $offer['description'];
                    $createOffer->save();
                    foreach ($offer['equipments'] as $type => $equipment_tab) {
                        foreach ($equipment_tab['equipment'] as $equipment) {
                            if ($equipment['base_id'] === '-1') {
                                $createEquipment = new Equipment();
                                $createEquipment->code = $equipment['code'];
                                $createEquipment->name = $equipment['name'];
                                $createEquipment->short_description = $equipment['short_description'];
                                $createEquipment->points = $equipment['points'];
                                $createEquipment->price = $equipment['price'];
                                $createEquipment->price_trade = $equipment['price_trade'];
                                $createEquipment->price_small_trade = $equipment['price_small_trade'];
                                $createEquipment->price_special = $equipment['price_special'];
                                $createEquipment->class = 'equipment';
//                            $createEquipment->type_id = Type::where('slug', $equipment['type'])->first()->id;
                                $createEquipment->save();

                                $createOffer->equipments()->attach($createEquipment->id, array(
                                    'quantity' => $equipment['quantity'],
                                    'price' => $equipment['price'],
                                    'price_trade' => $equipment['price_trade'],
                                    'price_small_trade' => $equipment['price_small_trade'],
                                    'price_special' => $equipment['price_special'],
                                    'counted_price' => $equipment['counted_price'],
                                    'comment' => $equipment['comment'],
                                    'tab_slug' => SlugService::createSlug(Equipment::class, 'slug', $equipment_tab['name']),
                                    'tab_name' => $equipment_tab['name'],
                                ));
                            } else {
//                                $oldEquipment = Equipment::where('id', $equipment['base_id'])->first();
//                                $oldEquipment->short_description = $equipment['short_description'];
//                                $oldEquipment->save();

                                $createOffer->equipments()->attach($equipment['base_id'], array(
                                    'quantity' => $equipment['quantity'],
                                    'price' => $equipment['price'],
                                    'price_trade' => $equipment['price_trade'],
                                    'price_small_trade' => $equipment['price_small_trade'],
                                    'price_special' => $equipment['price_special'],
                                    'counted_price' => $equipment['counted_price'],
                                    'comment' => $equipment['comment'],
                                    'tab_slug' => SlugService::createSlug(Equipment::class, 'slug', $equipment_tab['name']),
                                    'tab_name' => $equipment_tab['name'],
                                ));
                            }
                        }
                    }
                }
            }
            if(isset($newOfferGroup['works'])) {
                $buffWork = [];
                foreach ($newOfferGroup['works'] as $workTabKey => $workTab) {
                    if (isset($workTab['work'])) {
                        foreach ($workTab['work'] as $work) {
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
//                    $createWork->type_id = Type::where('slug', 'rabota')->first()->id;
                                $createWork->save();

                                $buffWork[$createWork->id] = [
                                    'quantity' => $work['quantity'],
                                    'tab_name' => $workTab['name'],
                                    'tab_slug' => SlugService::createSlug(Equipment::class, 'slug', $workTab['name']),
                                ];
                            } else {
                                $buffWork[$work['id']] = [
                                    'quantity' => $work['quantity'],
                                    'tab_name' => $workTab['name'],
                                    'tab_slug' => SlugService::createSlug(Equipment::class, 'slug', $workTab['name']),
                                ];
                            }
                        }
                    }
                }
            }
            else{
                throw new \Exception('Добавьте работы в КП.');
            }
            $offerGroup->equipment()->sync($buffWork);
            $offerGroup->save();
        }
        catch (\Exception $e){
            throw new \Exception('Произошла ошибка. Пожалуйста, обновите страницу и попробуйте снова.');
//            throw $e;
        }
        return url('kp/' . $offerGroup->uuid);
    }
}
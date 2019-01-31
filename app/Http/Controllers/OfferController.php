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
        $createGroup = new OfferGroup();
        $createGroup->name = $group['name'];
        $createGroup->save();
        foreach ($group as $item => $value){
            if($item == 'offers'){
                foreach ($value as $offers){
                    $createOffer = new Offer();
                    $createOffer->tax = $offers['tax'];
                    $createOffer->name = $offers['name'];
                    array_splice($offers, 2);
                    $createOffer->group_id = $createGroup->id;
                    $createOffer->save();
                    foreach ($offers as $offer){
                        foreach ($offer as $key => $value){
                            $equipment = $value;
                            if($equipment['id'] === 'new'){
                                $createEquipment = new Equipment();
                                $createEquipment->code = $equipment['code'];
                                $createEquipment->name = $equipment['name'];
                                $createEquipment->description = $equipment['description'];
                                $createEquipment->points = $equipment['points'];
                                $createEquipment->price = $equipment['price'];
                                $createEquipment->price_trade = $equipment['price'];
                                $createEquipment->price_small_trade = $equipment['price_small_trade'];
                                $createEquipment->price_special = $equipment['price_special'];
                                $createEquipment->class = $key;
                                $createEquipment->type_id = Type::where('slug', $equipment['type'])->first()->id;
                                $createEquipment->save();

                                $createOffer->equipment()->attach($createEquipment->id, array(
                                    'quantity' => $value['quantity'],
                                    'price' => $equipment['price'],
                                    'price_trade' => $equipment['price_trade'],
                                    'price_small_trade' => $equipment['price_small_trade'],
                                    'price_special' => $equipment['price_special'],
                                    'comment' => $equipment['comment'],
                                ));
                            }
                            else{
                                $createOffer->equipment()->attach($equipment['id'], array(
                                    'quantity' => $equipment['quantity'],
                                    'price' => $equipment['price'],
                                    'price_trade' => $equipment['price_trade'],
                                    'price_small_trade' => $equipment['price_small_trade'],
                                    'price_special' => $equipment['price_special'],
                                    'comment' => $equipment['comment'],
                                ));
                            }
                        }
                    }
                }
            }
        }
        return 'ok';
    }

    public function testRequest(){
        $offer_group =
            array (
                'offer_group' =>
                    array (
                        'name' => 'offergrouptest',
                        'offers' =>
                            array (
                                0 =>
                                    array (
                                        'name' => 'Вариант 1 (качество изображения: HD / 1 Мп / 1280x720)',
                                        'tax' => '6',
                                        'equipments' =>
                                            array (
                                                '`4`' =>
                                                    array (
                                                        0 =>
                                                            array (
                                                                'id' => '0',
                                                                'description' => '1 Мп, цветная купольная уличная антивандальная с ИК подсветкой до 15м. –40°...+50°C.',
                                                                'price_trade' => '11111',
                                                                'price_small_trade' => '11111',
                                                                'price_special' => '11111',
                                                                'comment' => '11111',
                                                                'code' => '11111',
                                                                'name' => 'Камера видеонаблюдения RVi-HDC311VB-C (3.6 мм). ',
                                                                'points' => '11111',
                                                                'quantity' => '11111',
                                                                'price' => '11111',
                                                            ),
                                                        'type_name' => 'камеры',
                                                    ),
                                                '`5`' =>
                                                    array (
                                                        0 =>
                                                            array (
                                                                'id' => '0',
                                                                'description' => '',
                                                                'price_trade' => '11111',
                                                                'price_small_trade' => '11111',
                                                                'price_special' => '11111',
                                                                'comment' => '11111',
                                                                'code' => '123',
                                                                'name' => 'drel',
                                                                'points' => 'R',
                                                                'quantity' => '11111',
                                                                'price' => '11111',
                                                            ),
                                                        1 =>
                                                            array (
                                                                'id' => '1',
                                                                'description' => '',
                                                                'price_trade' => '11111',
                                                                'price_small_trade' => '11111',
                                                                'price_special' => '11111',
                                                                'comment' => '11111',
                                                                'code' => '123',
                                                                'name' => 'drel',
                                                                'points' => 'R',
                                                                'quantity' => '11111',
                                                                'price' => '11111',
                                                            ),
                                                        'type_name' => 'инструменты',
                                                    ),
                                                '`2`' =>
                                                    array (
                                                        0 =>
                                                            array (
                                                                'id' => '0',
                                                                'description' => '11111',
                                                                'price_trade' => '11111',
                                                                'price_small_trade' => '11111',
                                                                'price_special' => '11111',
                                                                'comment' => '11111',
                                                                'code' => '111',
                                                                'name' => 'w1',
                                                                'points' => '11111',
                                                                'quantity' => '11111',
                                                                'price' => '11111',
                                                            ),
                                                        'type_name' => 'работа',
                                                    ),
                                            ),
                                    ),
                                1 =>
                                    array (
                                        'name' => 'Вариант 2 (качество изображения: Full HD / 2 Мп / 1920x1080)',
                                        'tax' => '40',
                                        'equipments' =>
                                            array (
                                                '`3`' =>
                                                    array (
                                                        0 =>
                                                            array (
                                                                'id' => '0',
                                                                'description' => '11111',
                                                                'price_trade' => '11111',
                                                                'price_small_trade' => '11111',
                                                                'price_special' => '11111',
                                                                'comment' => '11111',
                                                                'code' => '11',
                                                                'name' => 'eq2',
                                                                'points' => '11111',
                                                                'quantity' => '11111',
                                                                'price' => '11111',
                                                            ),
                                                        'type_name' => 'жесткие диски',
                                                    ),
                                                '`5`' =>
                                                    array (
                                                        0 =>
                                                            array (
                                                                'id' => '0',
                                                                'description' => '',
                                                                'price_trade' => '11111',
                                                                'price_small_trade' => '11111',
                                                                'price_special' => '11111',
                                                                'comment' => '11111',
                                                                'code' => '12331',
                                                                'name' => 'otvertka',
                                                                'points' => 'R',
                                                                'quantity' => '11111',
                                                                'price' => '11111',
                                                            ),
                                                        1 =>
                                                            array (
                                                                'id' => '1',
                                                                'description' => '',
                                                                'price_trade' => '11111',
                                                                'price_small_trade' => '11111',
                                                                'price_special' => '11111',
                                                                'comment' => '11111',
                                                                'code' => '12331',
                                                                'name' => 'otvertka',
                                                                'points' => 'R',
                                                                'quantity' => '11111',
                                                                'price' => '11111',
                                                            ),
                                                        'type_name' => 'инструменты',
                                                    ),
                                                '`2`' =>
                                                    array (
                                                        0 =>
                                                            array (
                                                                'id' => '0',
                                                                'description' => '11111',
                                                                'price_trade' => '11111',
                                                                'price_small_trade' => '11111',
                                                                'price_special' => '11111',
                                                                'comment' => '11111',
                                                                'code' => '1111',
                                                                'name' => 'w2',
                                                                'points' => '11111',
                                                                'quantity' => '11111',
                                                                'price' => '11111',
                                                            ),
                                                        'type_name' => 'работа',
                                                    ),
                                            ),
                                    ),
                            ),
                    ),
            );
        dd($offer_group);
        $request = new Request();
        $request->offer_group = $offer_group;
        dd( self::saveOfferGroup($request));
    }
}
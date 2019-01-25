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
        $offer_group = [
            'name'=>'offergrouptest',
            'offers'=>[
                '0'=>[
                    'equipments'=>[//!!ВАЖНО, смотреть на названия полей! class!= comment (в новые эелементы нужен class и слаг type`a, в старых не обязателен)
                        '0'=>[
                            'id'=>'new',
                            'code'=>'11111',
                            'name'=>'eq1',
                            'description'=>'11111',//nullable
                            'quantity'=>'11111',
                            'points'=>'11111',
                            'price'=>'11111',
                            'price_trade'=>'11111',
                            'price_small_trade'=>'11111',
                            'price_special'=>'11111',
                            'class'=>'equipment',
                            'type'=>'telephon',
                            'comment'=>'11111',//nullable
                        ],
                        '1'=>[
                            'id'=>'1',
                            'code'=>'11111',
                            'name'=>'11111',
                            'description'=>'11111',//nullable
                            'quantity'=>'11111',
                            'points'=>'11111',
                            'price'=>'11111',
                            'price_trade'=>'11111',
                            'price_small_trade'=>'11111',
                            'price_special'=>'11111',
                            'comment'=>'11111',//nullable
                        ],
                    ],
                    'works'=>[
                        '0'=>[
                            'id'=>'new',
                            'code'=>'11111',
                            'name'=>'w1',
                            'description'=>'11111',
                            'quantity'=>'11111',
                            'points'=>'11111',
                            'price'=>'11111',
                            'price_trade'=>'11111',
                            'price_small_trade'=>'11111',
                            'price_special'=>'11111',
                            'class'=>'work',
                            'type'=>'telephon',
                            'comment'=>'11111',//nullable
                        ],
                        '1'=>[
                            'id'=>'1',
                            'code'=>'11111',
                            'name'=>'11111',
                            'description'=>'11111',
                            'quantity'=>'11111',
                            'points'=>'11111',
                            'price'=>'11111',
                            'price_trade'=>'11111',
                            'price_small_trade'=>'11111',
                            'price_special'=>'11111',
                            'comment'=>'11111',
                        ],
                    ],
                    'tax'=>6,
                    'name'=>'offer1',
                ],
                '1'=>[
                    'equipments'=>[
                        '0'=>[
                            'id'=>'new',
                            'code'=>'11111',
                            'name'=>'eq2',
                            'description'=>'11111',
                            'quantity'=>'11111',
                            'points'=>'11111',
                            'price'=>'11111',
                            'price_trade'=>'11111',
                            'price_small_trade'=>'11111',
                            'price_special'=>'11111',
                            'comment'=>'11111',//nullable
                            'class'=>'equipment',
                            'type'=>'telephon',
                        ],
                        '1'=>[
                            'id'=>'2',
                            'code'=>'11111',
                            'name'=>'11111',
                            'description'=>'11111',
                            'quantity'=>'11111',
                            'points'=>'11111',
                            'price'=>'11111',
                            'price_trade'=>'11111',
                            'price_small_trade'=>'11111',
                            'price_special'=>'11111',
                            'comment'=>'11111',
                        ],
                    ],
                    'works'=>[
                        '0'=>[
                            'id'=>'2',
                            'code'=>'11111',
                            'name'=>'11111',
                            'description'=>'11111',
                            'quantity'=>'11111',
                            'points'=>'11111',
                            'price'=>'11111',
                            'price_trade'=>'11111',
                            'price_small_trade'=>'11111',
                            'price_special'=>'11111',
                            'comment'=>'11111',
                        ],
                        '1'=>[
                            'id'=>'new',
                            'code'=>'11111',
                            'name'=>'w2',
                            'description'=>'11111',
                            'quantity'=>'11111',
                            'points'=>'11111',
                            'price'=>'11111',
                            'price_trade'=>'11111',
                            'price_small_trade'=>'11111',
                            'price_special'=>'11111',
                            'comment'=>'11111',//nullable
                            'class'=>'work',
                            'type'=>'telephon',
                        ],
                    ],
                    'tax'=>6,
                    'name'=>'offer2',
                ],
            ]
        ];
        $request = new Request();
        $request->offer_group = $offer_group;
        dd( self::saveOfferGroup($request));
    }
}
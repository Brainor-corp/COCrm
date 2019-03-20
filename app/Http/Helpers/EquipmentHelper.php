<?php
/**
 * Created by PhpStorm.
 * User: Brainor-PC-000001
 * Date: 19.02.2019
 * Time: 16:42
 */

namespace App\Http\Helpers;


class EquipmentHelper
{
    public static function getRealClassName($class) {
        switch ($class) {
            case 'equipment': return 'Оборудование'; break;
            case 'work': return 'Работы'; break;

            default: return $class;
        }
    }
    public static function getRealDisplayValues($display) {
        return $display ? 'Да' : 'Нет';
    }

    public static function getClassByTypeName($typeName) {
        $works = [
            'кабели и провода',
            'расходники',
            'монтажные и расходные материалы',
        ];

        return in_array(mb_strtolower($typeName), $works) ? 'works' : 'equipment';
    }
}
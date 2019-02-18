<?php

namespace Bradmin\Navigation;


class NavigationDefault
{
    public static function getNavigationList()
    {
        $navigation = [
            [
                'url' => '/bradmin/equipments',
                'icon' => 'fas fa-address-book',
                'text' => 'Оборудование'
            ],
            [
                'url' => '/bradmin/offergroups',
                'icon' => 'fas fa-address-book',
                'text' => 'Группы КП'
            ]
        ];

        return $navigation;
    }
}
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
            ],
            [
                'url' => '/bradmin/types',
                'icon' => 'fas fa-address-book',
                'text' => 'Типы'
            ],
            [
                'url' => '/bradmin/settings',
                'icon' => 'fas fa-cog',
                'text' => 'Настройки'
            ]
        ];

        return $navigation;
    }
}
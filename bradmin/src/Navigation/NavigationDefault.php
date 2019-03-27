<?php

namespace Bradmin\Navigation;


class NavigationDefault
{
    public static function getNavigationList()
    {
        $navigation = [
            [
                'url' => '/bradmin/Equipments',
                'icon' => 'fas fa-address-book',
                'text' => 'Оборудование | Работы'
            ],
            [
                'url' => '/bradmin/OfferGroups',
                'icon' => 'fas fa-address-book',
                'text' => 'Группы КП'
            ],
            [
                'url' => '/bradmin/Tabs',
                'icon' => 'fas fa-address-book',
                'text' => 'Вкладки'
            ],
            [
                'url' => '/bradmin/Settings',
                'icon' => 'fas fa-cog',
                'text' => 'Настройки'
            ],
            [
                'url' => '/bradmin/Users',
                'icon' => 'fas fa-address-book',
                'text' => 'Пользователи'
            ],
            [
                'url' => '/bradmin/ExcelUploads',
                'icon' => 'far fa-file-excel',
                'text' => 'Excel загрузка'
            ]
        ];

        return $navigation;
    }
}
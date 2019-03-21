<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class InsertDefaultValues extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $types = [
            [
                'name' => 'Работа',
                'slug' => 'rabota',
                'class' => 'work',
                'optional' => null
            ],
            [
                'name' => 'Оборудование',
                'slug' => 'oborudovanie',
                'class' => 'equipment',
                'optional' => null,
            ],
        ];

        $user = [
            [
                'name' => 'admin',
                'middle' => 'admin',
                'last' => 'admin',
                'email' => 'admin@arona.ru',
                'company' => 'arona',
                'position' => 'administrator',
                'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm',
                'phone_number' => '123',
                'mobile_number' => '321',
                'contact_email' => 'admin@arona.ru',
            ]
        ];

        $taxes = [
            [
                'name' => 'Минимальный налог',
                'slug' => 'minimalnyy-nalog',
                'class' => 'tax',
                'value' => 6,
            ],
            [
                'name' => 'Средний налог',
                'slug' => 'sredniy-nalog',
                'class' => 'tax',
                'value' => 37,
            ],
            [
                'name' => 'Максимальный налог',
                'slug' => 'maksimalnyy-nalog',
                'class' => 'tax',
                'value' => 40,
            ],
        ];

        $defaultTabs = [
            [
                'name' => 'Расходные материалы',
                'slug' => 'rashodnye-materialy',
                'order' => '0',
                'class' => 'equipment',
                'display' => true,
            ],
            [
                'name' => 'Работы',
                'slug' => 'raboty',
                'order' => '0',
                'class' => 'work',
                'display' => true,
            ],
        ];

        DB::table('types')->insert($types);
        DB::table('users')->insert($user);
        DB::table('settings')->insert($taxes);
        DB::table('default_tabs')->insert($defaultTabs);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}

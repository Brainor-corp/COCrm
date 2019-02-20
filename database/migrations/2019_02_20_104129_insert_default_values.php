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
            ],
            [
                'name' => 'Расходные материалы',
                'slug' => 'rashodnye-materialy',
                'class' => 'equipment',
                'optional' => 'default',
            ],
        ];

        DB::table('types')->insert($types);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
}

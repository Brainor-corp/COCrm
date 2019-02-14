<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEquipmentTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('equipment_type', function (Blueprint $table) {
            $table->bigInteger('equipment_id');
            $table->bigInteger('type_id');
            $table->integer('quantity')->default(1);
            $table->double('price')->default(0);
            $table->double('price_trade')->default(0);
            $table->double('price_small_trade')->default(0);
            $table->double('price_special')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('equipment_type');
    }
}

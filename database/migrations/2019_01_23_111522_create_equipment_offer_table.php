<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEquipmentOfferTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('equipment_offer', function (Blueprint $table) {
            $table->bigInteger('equipment_id');
            $table->bigInteger('offer_id');
            $table->integer('quantity');
            $table->double('price');
            $table->double('price_trade');
            $table->double('price_small_trade');
            $table->double('price_special');
            $table->double('counted_price');
            $table->text('comment')->nullable();
            $table->string('type');
            $table->bigInteger('type_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('equipment_offer');
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEquipmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('equipment', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->bigInteger('type_id')->nullable();
            $table->string('slug');
            $table->string('code')->unique()->nullable();
            $table->text('description')->nullable();
            $table->string('thumb', 1024)->nullable();
            $table->string('points', 32);
            $table->double('price');
            $table->double('price_trade')->nullable();
            $table->double('price_small_trade')->nullable();
            $table->double('price_special')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('equipment');
    }
}

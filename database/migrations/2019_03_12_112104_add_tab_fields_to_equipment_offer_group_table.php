<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTabFieldsToEquipmentOfferGroupTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('equipment_offer_group', function (Blueprint $table) {
            $table->string('tab_slug');
            $table->string('tab_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('equipment_offer_group', function (Blueprint $table) {
            $table->dropColumn('tab_slug');
            $table->dropColumn('tab_name');
        });
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeEquipmentOfferTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('equipment_offer', function (Blueprint $table) {
            $table->renameColumn('type', 'tab_slug');
            $table->dropColumn('type_id');
            $table->string('tab_name')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('equipment_offer', function (Blueprint $table) {
            $table->renameColumn('tab_slug', 'type');
            $table->bigInteger('type_id');
            $table->dropColumn('tab_name');
        });
    }
}

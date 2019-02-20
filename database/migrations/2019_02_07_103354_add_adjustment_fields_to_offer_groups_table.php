<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAdjustmentFieldsToOfferGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('offer_groups', function (Blueprint $table) {
            $table->double('adjusters_no_tax')->nullable();
            $table->integer('adjusters_number')->nullable()->default(1);
            $table->integer('adjustments_days')->nullable()->default(1);
            $table->integer('fuel_number')->nullable()->default(0);
            $table->double('adjusters_wage')->nullable();
            $table->double('pay_percentage')->nullable();
            $table->boolean('template')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('offer_groups', function (Blueprint $table) {
            $table->dropColumn('adjusters_no_tax');
            $table->dropColumn('adjusters_number');
            $table->dropColumn('adjustments_days');
            $table->dropColumn('fuel_number');
            $table->dropColumn('adjusters_wage');
            $table->dropColumn('pay_percentage');
            $table->dropColumn('template');
        });
    }
}

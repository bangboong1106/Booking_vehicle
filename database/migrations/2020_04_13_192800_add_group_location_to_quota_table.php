<?php

use App\Database\Migration\Base;
use App\Database\Migration\CustomBlueprint as Blueprint;

class AddGroupLocationToQuotaTable extends Base
{
    protected $_table = 'quota';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->getTable(), function (Blueprint $table) {
            $table->integer('location_destination_group_id')->nullable()->after('location_destination_id');
            $table->integer('location_arrival_group_id')->nullable()->after('location_arrival_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table($this->getTable(), function (Blueprint $table) {
            $table->dropColumn('location_destination_group_id');
            $table->dropColumn('location_arrival_group_id');
        });
    }
}

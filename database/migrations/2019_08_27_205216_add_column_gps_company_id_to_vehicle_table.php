<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;

class AddColumnGpsCompanyIdToVehicleTable extends \App\Database\Migration\Create
{
    protected $_table = 'vehicle';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->getTable(), function (Blueprint $table) {
            $table->integer('gps_company_id')->nullable()->after('group_id');
        });
    }

    public function down()
    {
        Schema::table($this->getTable(), function (Blueprint $table) {
            $table->dropColumn('group_id');
        });
    }
}

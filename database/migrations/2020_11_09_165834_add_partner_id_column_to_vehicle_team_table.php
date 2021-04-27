<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;
use \App\Database\Migration\Base;

class AddPartnerIdColumnToVehicleTeamTable extends Base
{
    protected $_table = 'vehicle_team';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn($this->getTable(), 'partner_id')) {
            Schema::table($this->getTable(), function (Blueprint $table) {
                $table->integer('partner_id')->nullable()->after('capital_driver_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn($this->getTable(), 'partner_id')) {
            Schema::table($this->getTable(), function (Blueprint $table) {
                $table->dropColumn('partner_id');
            });
        }
    }
}

<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;
use \App\Database\Migration\Base;

class AddPartnerIdColumnToVehicleGroupTable extends Base
{
    protected $_table = 'm_vehicle_group';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn($this->getTable(), 'partner_id')) {
            Schema::table($this->getTable(), function (Blueprint $table) {
                $table->integer('partner_id')->nullable()->after('parent_id');
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

<?php

use App\Database\Migration\Base;
use App\Database\Migration\CustomBlueprint as Blueprint;

class AddRepairColumnVehicleTable extends Base
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
            $table->decimal('repair_distance', 18, 4)->nullable()->after('active');
            $table->date('repair_date')->nullable()->after('repair_distance');
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
            $table->dropColumn('repair_distance');
            $table->dropColumn('repair_date');
        });
    }
}

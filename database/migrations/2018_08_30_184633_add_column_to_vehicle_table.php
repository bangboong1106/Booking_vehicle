<?php

use App\Database\Migration\Base;
use App\Database\Migration\CustomBlueprint as Blueprint;

class AddColumnToVehicleTable extends Base
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
            $table->decimal('volume', 10, 4)->nullable()->after('current_location');
            $table->decimal('weight', 10, 4)->nullable()->after('volume');
            $table->decimal('length', 10, 4)->nullable()->after('weight');
            $table->decimal('height', 10, 4)->nullable()->after('length');
            $table->decimal('width', 10, 4)->nullable()->after('height');

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
            $table->dropColumn('volume');
            $table->dropColumn('weight');
            $table->dropColumn('length');
            $table->dropColumn('height');
            $table->dropColumn('width');
        });
    }
}

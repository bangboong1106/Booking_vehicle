<?php

use App\Database\Migration\Base;
use App\Database\Migration\CustomBlueprint as Blueprint;

class AddColumnWeightVolumeToTripTable extends Base
{
    protected $_table = 'trip';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->getTable(), function (Blueprint $table) {
            $table->decimal('weight', 18, 4)->nullable()->after('trip_no')->default(0);
            $table->decimal('volume', 18, 4)->nullable()->after('weight')->default(0);

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
            $table->dropColumn('weight');
            $table->dropColumn('volume');
        });
    }
}

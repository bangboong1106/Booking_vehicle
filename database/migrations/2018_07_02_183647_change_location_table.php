<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;
use \App\Database\Migration\Base;

class ChangeLocationTable extends Base
{
    protected $_table = 'locations';

    public function up()
    {
        Schema::table($this->getTable(), function (Blueprint $table) {
            $table->char('longitude', 50)->after('ward_id')->nullable();
            $table->char('latitude', 50)->after('ward_id')->nullable();

            $table->dropColumn('location');
        });
    }

    public function down()
    {
        Schema::table($this->getTable(), function (Blueprint $table) {
            $table->string('location', 250)->nullable();

            $table->dropColumn('longitude');
            $table->dropColumn('latitude');
        });
    }
}

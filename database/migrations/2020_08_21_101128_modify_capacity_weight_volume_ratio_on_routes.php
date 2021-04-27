<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;
use \App\Database\Migration\Base;

class ModifyCapacityWeightVolumeRatioOnRoutes extends Base
{
    protected $_table = 'routes';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->getTable(), function (Blueprint $table) {
            $table->decimal('capacity_weight_ratio', 18, 4)->change();
            $table->decimal('capacity_volume_ratio', 18, 4)->change();
        });

    }

    public function down()
    {
        Schema::table($this->getTable(), function (Blueprint $table) {
            $table->integer('capacity_weight_ratio')->change();
            $table->integer('capacity_volume_ratio')->change();
        });
    }
}

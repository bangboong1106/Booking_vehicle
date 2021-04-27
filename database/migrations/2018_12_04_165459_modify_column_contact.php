<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;
use \App\Database\Migration\Base;
class ModifyColumnContact extends  Base
{
    protected $_table = 'contact';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->getTable(), function (Blueprint $table) {
            $table->dropColumn('full_address');
            $table->dropColumn('address');
            $table->dropColumn('province_id');
            $table->dropColumn('district_id');
            $table->dropColumn('ward_id');
            $table->dropColumn('longitude');
            $table->dropColumn('latitude');
            $table->integer('location_id')->nullable()->after('email');
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
            $table->string('full_address','500')->nullable();
            $table->string('address', 255)->nullable();
            $table->string('province_id', 5)->nullable();
            $table->string('district_id', 5)->nullable();
            $table->string('ward_id', 5)->nullable();
            $table->char('longitude', 50)->nullable();
            $table->char('latitude', 50)->nullable();
        });
    }
}

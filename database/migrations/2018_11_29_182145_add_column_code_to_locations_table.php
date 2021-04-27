<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;
use \App\Database\Migration\Base;

class AddColumnCodeToLocationsTable extends Base
{
    protected $_table = 'locations';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->getTable(), function (Blueprint $table) {
            $table->string('code', 50)->after('title')->nullable();
        });
        Schema::table('orders', function (Blueprint $table) {
            $table->string('location_destination_id', 50)->nullable()->change();
            $table->string('location_arrival_id', 50)->nullable()->change();
        });
        Schema::table('trip', function (Blueprint $table) {
            $table->string('location_destination_id', 50)->nullable()->change();
            $table->string('location_arrival_id', 50)->nullable()->change();
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
            $table->dropColumn('code');
        });
        Schema::table('orders', function (Blueprint $table) {
            $table->integer('location_destination_id')->nullable()->change();
            $table->integer('location_arrival_id')->nullable()->change();
        });
        Schema::table('trip', function (Blueprint $table) {
            $table->integer('location_destination_id')->nullable()->change();
            $table->integer('location_arrival_id')->nullable()->change();
        });
    }
}

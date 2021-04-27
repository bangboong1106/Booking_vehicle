<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;
use \App\Database\Migration\Base;

class AddColumnCustomerIdToContactTable extends Base
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
            $table->integer('customer_id')->nullable()->after('location_id');
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
            $table->dropColumn('customer_id');
        });
    }
}

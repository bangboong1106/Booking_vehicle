<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;
use \App\Database\Migration\Base;

class ModifyRouteCost extends Base
{
    protected $_table = 'route_cost';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->getTable(), function (Blueprint $table) {
            $table->decimal('amount_admin', 18, 4)->nullable()->after('amount');
            $table->decimal('amount_driver', 18, 4)->nullable()->after('amount_admin');
            $table->char('approved')->after('amount_driver');
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
            $table->dropColumn('amount_admin');
            $table->dropColumn('amount_driver');
            $table->dropColumn('approved');
        });
    }
}

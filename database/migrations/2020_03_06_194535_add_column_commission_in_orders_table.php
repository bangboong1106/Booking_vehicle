<?php

use App\Database\Migration\Base;
use App\Database\Migration\CustomBlueprint as Blueprint;

class AddColumnCommissionInOrdersTable extends Base
{
    protected $_table = 'orders';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->getTable(), function (Blueprint $table) {
            $table->decimal('commission_amount', 18, 4)->nullable()->after('amount');
            $table->integer('commission_currency_id')->nullable()->after('commission_amount');
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
            $table->dropColumn('commission_amount');
            $table->dropColumn('commission_currency_id');
        });
    }
}

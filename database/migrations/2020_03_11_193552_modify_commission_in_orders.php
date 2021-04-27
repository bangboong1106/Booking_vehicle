<?php

use App\Database\Migration\Base;
use App\Database\Migration\CustomBlueprint as Blueprint;

class ModifyCommissionInOrders extends Base
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
            $table->renameColumn('commission_currency_id', 'commission_type');
            $table->decimal('commission_value', 18, 4)->nullable()->after('commission_amount');

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
            $table->renameColumn('commission_type', 'commission_currency_id');
            $table->dropColumn('commission_value');
        });
    }
}

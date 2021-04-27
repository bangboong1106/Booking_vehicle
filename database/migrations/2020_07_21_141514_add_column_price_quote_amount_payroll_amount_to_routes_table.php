<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;
use \App\Database\Migration\Base;

class AddColumnPriceQuoteAmountPayrollAmountToRoutesTable extends Base
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
            $table->decimal('price_quote_amount', 18, 4)->after('final_cost');
            $table->decimal('payroll_amount', 18, 4)->after('price_quote_amount');
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
            $table->dropColumn('price_quote_amount');
            $table->dropColumn('payroll_amount');
        });
    }
}

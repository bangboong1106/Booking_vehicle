<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;
use \App\Database\Migration\Base;

class AddAmountToReceiptPaymentTable extends Base
{
    protected $_table = 'm_receipt_payment';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn($this->getTable(), 'amount')) {
            Schema::table($this->getTable(), function (Blueprint $table) {
                $table->string('amount')->nullable()->before('sort_order');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn($this->getTable(), 'sort_order')) {
            Schema::table($this->getTable(), function (Blueprint $table) {
                $table->dropColumn('amount');
            });
        }
    }
}

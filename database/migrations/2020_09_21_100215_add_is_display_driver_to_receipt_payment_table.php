<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;
use \App\Database\Migration\Base;

class AddIsDisplayDriverToReceiptPaymentTable extends Base
{
    protected $_table = 'm_receipt_payment';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn($this->getTable(), 'is_display_driver')) {
            Schema::table($this->getTable(), function (Blueprint $table) {
                $table->char('is_display_driver')->nullable()->default(1);
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
        if (Schema::hasColumn($this->getTable(), 'is_display_driver')) {
            Schema::table($this->getTable(), function (Blueprint $table) {
                $table->dropColumn('is_display_driver');
            });
        }
    }
}

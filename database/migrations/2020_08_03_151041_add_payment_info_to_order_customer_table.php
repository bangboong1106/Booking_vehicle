<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;
use \App\Database\Migration\Base;

class AddPaymentInfoToOrderCustomerTable extends Base
{
    protected $_table = 'order_customer';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->getTable(), function (Blueprint $table) {

            $table->char('payment_type', 1)->default(1)->after('commission_value');
            $table->integer('payment_user_id')->nullable()->after('payment_type');
            $table->decimal('goods_amount', 18, 4)->default(0)->after('payment_user_id');
            $table->char('vat', 1)->default(0)->after('goods_amount');
            $table->decimal('fee_ship', 18, 4)->default(0)->after('vat');
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
            $table->dropColumn('payment_type');
            $table->dropColumn('payment_user_id');
            $table->dropColumn('goods_amount');
            $table->dropColumn('vat');
            $table->dropColumn('fee_ship');
        });
    }
}

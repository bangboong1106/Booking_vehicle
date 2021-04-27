<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;
use \App\Database\Migration\Base;

class AddFieldToOrderTable extends Base
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
            $table->string('order_code',100)->nullable()->after('id');
            $table->string('order_purchasing_no', 100)->nullable()->after('order_no');
            $table->string('bill_no',100)->nullable()->after('order_purchasing_no');
            $table->date('order_date')->nullable()->after('status');
            $table->string('good_code', 100)->nullable()->after('goods_type');
            $table->string('contract_no', 100)->nullable()->after('order_date');

            $table->string('contact_email_destination', 250)->nullable()->after('contact_mobile_no_destination');
            $table->string('contact_email_arrival',250)->nullable()->after('contact_mobile_no_arrival');
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
            $table->dropColumn('order_code');
            $table->dropColumn('order_purchasing_no');
            $table->dropColumn('bill_no');
            $table->dropColumn('order_date');
            $table->dropColumn('good_code');
            $table->dropColumn('contact_email_destination');
            $table->dropColumn('contact_email_arrival');
            $table->dropColumn('contract_no');
        });
    }
}

<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;
use \App\Database\Migration\Base;

class ModifyFeeShipColumnOrderTable extends Base
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_payment', function (Blueprint $table) {
            $table->dropColumn('fee_ship');
            $table->decimal('anonymous_amount', 18, 4)->default(0)->after('vat');
        });

        Schema::table('order_customer', function (Blueprint $table) {
            $table->dropColumn('fee_ship');
            $table->decimal('anonymous_amount', 18, 4)->default(0)->after('vat');
        });
    }

    public function down()
    {
        Schema::table('order_payment', function (Blueprint $table) {
            $table->dropColumn('anonymous_amount');
            $table->decimal('fee_ship', 18, 4)->default(0)->after('vat');
        });

        Schema::table('order_customer', function (Blueprint $table) {
            $table->dropColumn('anonymous_amount');
            $table->decimal('fee_ship', 18, 4)->default(0)->after('vat');
        });
    }
}

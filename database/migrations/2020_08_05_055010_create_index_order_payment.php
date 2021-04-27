<?php

use App\Database\Migration\Base;
use App\Database\Migration\CustomBlueprint as Blueprint;

class CreateIndexOrderPayment extends Base
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_payment', function (Blueprint $table) {
            $table->index(['order_id']);
            $table->index(['payment_user_id']);
        });
    }

    public function down()
    {
        Schema::table('order_payment', function (Blueprint $table) {
            $table->dropIndex(['order_id']);
            $table->dropIndex(['payment_user_id']);
        });
    }
}

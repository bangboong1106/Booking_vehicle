<?php

use App\Database\Migration\Base;
use App\Database\Migration\CustomBlueprint as Blueprint;

class CreateIndexCustomerGroup extends Base
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customer_group_customer', function (Blueprint $table) {
            $table->index(['customer_group_id']);
            $table->index(['customer_id']);
        });

        Schema::table('admin_users_customer_group', function (Blueprint $table) {
            $table->index(['admin_user_id']);
            $table->index(['customer_group_id']);
        });

    }

    public function down()
    {
        Schema::table('customer_group_customer', function (Blueprint $table) {
            $table->dropIndex(['customer_group_id']);
            $table->dropIndex(['customer_id']);
        });

        Schema::table('admin_users_customer_group', function (Blueprint $table) {
            $table->dropIndex(['admin_user_id']);
            $table->dropIndex(['customer_group_id']);
        });
    }
}

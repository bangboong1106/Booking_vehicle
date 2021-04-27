<?php

use App\Database\Migration\CustomBlueprint as Blueprint;

class CreateAdminUsersCustomerGroupTable extends \App\Database\Migration\Create
{
    protected $_table = 'admin_users_customer_group';

    public function up()
    {
        if (Schema::hasTable($this->getTable())) {
            return;
        }

        Schema::create($this->getTable(), function (Blueprint $table) {
            $table->increments('id');

            $table->integer('admin_user_id');
            $table->integer('customer_group_id');

            $table->actionBy();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('admin_users_customer_group');
    }
}

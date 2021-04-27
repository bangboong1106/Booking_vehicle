<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;
use \App\Database\Migration\Base;

class ModifyColumnAdminUsersTable extends Base
{
    protected $_table = 'admin_users';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->getTable(), function (Blueprint $table) {
            $table->string('email', 256)->nullable()->change();
            $table->integer('avatar_id')->nullable()->change();
            $table->string('role', 64)->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table($this->getTable(), function (Blueprint $table) {
            $table->string('email', 256)->nullable()->change();
            $table->integer('avatar_id')->nullable()->change();
            $table->string('role', 64)->nullable()->change();
        });
    }
}

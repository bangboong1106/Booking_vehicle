<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;

class CreateAdminUsersTable extends \App\Database\Migration\Create
{
    protected $_table = 'admin_users';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable($this->getTable())) {
            return;
        }

        Schema::create($this->getTable(), function (Blueprint $table) {
            $table->increments('id');
            $table->string('email', 256);
            $table->integer('avatar_id');
            $table->string('password', 64);
            $table->string('role', 64);
            $table->actionBy();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}

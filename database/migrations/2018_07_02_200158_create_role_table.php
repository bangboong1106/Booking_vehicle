<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;

class CreateRoleTable extends \App\Database\Migration\Create
{
    protected $_table = 'role';
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

            $table->string('role_name', 255);

            $table->actionBy();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}

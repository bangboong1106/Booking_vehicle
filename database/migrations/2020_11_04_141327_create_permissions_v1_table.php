<?php

use App\Database\Migration\CustomBlueprint as Blueprint;

class CreatePermissionsV1Table extends \App\Database\Migration\Create
{
    protected $_table = 'permissions_v1';

    public function up()
    {
        if (Schema::hasTable($this->getTable())) {
            return;
        }

        Schema::create('permissions_v1', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 255);
            $table->string('guard_name', 255);
            $table->string('group', 255)->nullable()->comment('Group permission');
            $table->integer('display');
            $table->integer('ins_id')->comment('Created by column');
            $table->integer('upd_id')->comment('Updated by column');
            $table->char('del_flag')->default(0)->comment('Delete flag column');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('permissions_v1');
    }
}

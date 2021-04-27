<?php

use App\Database\Migration\CustomBlueprint as Blueprint;

class CreateAppInfoTable extends \App\Database\Migration\Create
{
    protected $_table = 'app_info';

    public function up()
    {
        if (Schema::hasTable($this->getTable())) {
            return;
        }


        Schema::create('app_info', function (Blueprint $table) {
            $table->integer('id');
            $table->string('name')->nullable();
            $table->string('version_android')->nullable();
            $table->string('version_ios')->nullable();
            $table->string('what_new', 500)->nullable();
            $table->string('play_store_id')->nullable();
            $table->string('app_store_id')->nullable();
            $table->tinyInteger('force_update')->nullable();
            $table->tinyInteger('del_flag')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('app_info');
    }
}

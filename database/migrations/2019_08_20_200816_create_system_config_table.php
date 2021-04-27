<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;

class CreateSystemConfigTable extends \App\Database\Migration\Create
{
    protected $_table = 'system_config';

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

            $table->string('key',1000)->nullable();
            $table->string('value',5000)->nullable();
            $table->string('description',5000)->nullable();

            $table->actionBy();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('system_config');
    }
}

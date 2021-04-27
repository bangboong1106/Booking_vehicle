<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;

class CreateAlertLogsTable extends \App\Database\Migration\Create
{
    protected $_table = 'alert_logs';

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

            $table->string('name')->nullable();
            $table->string('title')->nullable();
            $table->string('content')->nullable();

            $table->actionBy();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('alert_logs');
    }
}

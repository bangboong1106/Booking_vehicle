<?php

use App\Database\Migration\CustomBlueprint as Blueprint;

class CreateNotificationLogsDriverTable extends \App\Database\Migration\Create
{
    protected $_table = 'notification_logs_driver';

    public function up()
    {
        if (Schema::hasTable($this->getTable())) {
            return;
        }

        Schema::create($this->getTable(), function (Blueprint $table) {
            $table->increments('id');

            $table->string('title')->nullable();
            $table->string('message')->nullable();
            $table->text('data')->nullable();
            $table->string('driver_id')->nullable();
            $table->integer('read_status')->nullable();
            $table->string('action_id')->nullable();
            $table->string('action_type')->nullable();
            $table->string('action_screen')->nullable();

            $table->actionBy();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('notification_logs_driver');
    }
}

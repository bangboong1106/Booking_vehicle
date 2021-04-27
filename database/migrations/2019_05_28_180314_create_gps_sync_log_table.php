<?php

use App\Database\Migration\CustomBlueprint as Blueprint;

class CreateGpsSyncLogTable extends \App\Database\Migration\Create
{
    protected $_table = 'gps_sync_logs';

    public function up()
    {
        if (Schema::hasTable($this->getTable())) {
            return;
        }

        Schema::create($this->getTable(), function (Blueprint $table) {
            $table->increments('id');

            $table->text('request')->nullable();
            $table->text('response')->nullable();
            $table->string('error_code')->nullable();
            $table->string('error_message')->nullable();

            $table->actionBy();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('gps_sync_logs');
    }
}

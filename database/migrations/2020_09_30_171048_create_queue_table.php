<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;
use \App\Database\Migration\Base;

class CreateQueueTable extends Base
{
    protected $_table = 'queue';

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
            $table->bigIncrements('id');
            $table->string('event');
            $table->longText('data');
            $table->string('config', 1000)->nullable();

            $table->unsignedTinyInteger('attempts');
            $table->dateTime('updated_at')->nullable();
            $table->dateTime('created_at');

        });
    }

    public function down()
    {
        Schema::dropIfExists('queue');
    }
}
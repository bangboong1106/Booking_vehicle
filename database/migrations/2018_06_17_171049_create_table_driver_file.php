<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;

class CreateTableDriverFile extends \App\Database\Migration\Create
{
    protected $_table = 'driver_file';

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
            $table->integer('driver_id');
            $table->integer('driver_config_file_id');
            $table->integer('file_id')->nullable();
            $table->char('ref_no', 100)->nullable();
            $table->text('note')->nullable();
            $table->date('expire_date')->nullable();
            $table->date('register_date')->nullable();

            $table->actionBy();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}

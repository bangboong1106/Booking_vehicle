<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;

class CreateRouteFileTable extends \App\Database\Migration\Create
{
    protected $_table = 'route_file';

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

            $table->integer('route_id');
            $table->string('file_id', 50)->nullable();
            $table->text('note')->nullable();

            $table->actionBy();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}

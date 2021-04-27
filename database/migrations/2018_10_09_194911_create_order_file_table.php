<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;

class CreateOrderFileTable extends \App\Database\Migration\Create
{
    protected $_table = 'order_file';

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

            $table->integer('order_id');
            $table->integer('order_status');
            $table->string('file_id', 50)->nullable();
            $table->text('reason')->nullable();
            $table->text('note')->nullable();
            $table->date('expire_date')->nullable();
            $table->date('register_date')->nullable();

            $table->actionBy();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}

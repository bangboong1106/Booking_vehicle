<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;

class CreateRepairTicketItemTable extends \App\Database\Migration\Create
{
    protected $_table = 'repair_ticket_item';

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
            $table->integer('repair_ticket_id');
            $table->integer('accessory_id');
            $table->integer('quantity')->nullable();
            $table->decimal('price', 18, 4)->nullable();
            $table->decimal('amount', 18, 4)->nullable();
            $table->integer('next_repair_type')->nullable();
            $table->date('next_repair_date')->nullable();
            $table->decimal('next_repair_distance', 18, 4)->nullable();

            $table->actionBy();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}

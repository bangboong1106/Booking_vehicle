<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;

class CreateRepairTicketTable extends \App\Database\Migration\Create
{
    protected $_table = 'repair_ticket';

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
            $table->string('code', 255);
            $table->string('name', 255);
            $table->integer('driver_id');
            $table->integer('vehicle_id');
            $table->date('repair_date');
            $table->string('description', 255)->nullable();
            $table->decimal('amount', 18, 4)->default();
            $table->char('is_approved')->default(0);
            $table->integer('approved_id')->nullable();
            $table->date('approved_date')->nullable();
            $table->string('approved_note', 255)->nullable();

            $table->actionBy();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}

<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;

class CreateOrderTable extends \App\Database\Migration\Create
{
    protected $_table = 'orders';
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

            $table->string('order_no', 100)->nullable();
            $table->string('status', 30)->nullable();
            $table->integer('customer_id')->nullable();
            $table->string('customer_name', 100)->nullable();
            $table->string('customer_mobile_no', 20)->nullable();

            $table->dateTime('ETD')->nullable();
            $table->integer('location_destination_id')->nullable();
            $table->string('contact_name_destination', 100)->nullable();
            $table->string('contact_mobile_no_destination', 20)->nullable();

            $table->string('ETA')->nullable();
            $table->integer('location_arrival_id')->nullable();
            $table->string('contact_name_arrival')->nullable();
            $table->string('contact_mobile_no_arrival')->nullable();

            $table->string('goods_type', 50)->nullable();
            $table->decimal('amount', 18, 4)->nullable();
            $table->decimal('quantity', 18, 4)->nullable();
            $table->decimal('volume', 18, 4)->nullable();

            $table->text('note');
            $table->text('description');

            $table->actionBy();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}

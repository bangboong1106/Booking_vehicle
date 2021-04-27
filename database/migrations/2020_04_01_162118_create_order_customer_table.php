<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;
use \App\Database\Migration\Base;

class CreateOrderCustomerTable extends Base
{
    protected $_table = 'order_customer';

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
            $table->string('code')->nullable();
            $table->string('name')->nullable();
            $table->string('order_no')->nullable();
            $table->integer('client_id')->nullable();
            $table->integer('customer_id')->nullable();
            $table->string('customer_name')->nullable();
            $table->string('customer_mobile_no')->nullable();
            $table->date('order_date')->nullable();
            $table->integer('location_destination_id')->nullable();
            $table->integer('location_arrival_id')->nullable();
            $table->date('ETD_date')->nullable();
            $table->time('ETD_time')->nullable();
            $table->date('ETA_date')->nullable();
            $table->time('ETA_time')->nullable();
            $table->date('ETD_date_reality')->nullable();
            $table->time('ETD_time_reality')->nullable();
            $table->date('ETA_date_reality')->nullable();
            $table->time('ETA_time_reality')->nullable();
            $table->decimal('distance', 18, 4)->nullable();
            $table->integer('vehicle_group_id')->nullable();
            $table->integer('vehicle_number')->nullable();
            $table->integer('route_number')->nullable();
            $table->decimal('weight', 18, 4)->nullable();
            $table->decimal('volume', 18, 4)->nullable();
            $table->decimal('amount', 18, 4)->nullable();
            $table->integer('currency_id')->nullable();
            $table->decimal('commission_amount', 18, 4)->nullable();
            $table->integer('commission_type')->default(2);
            $table->decimal('commission_value', 18, 4)->default(0);
            $table->integer('status')->default(0);

            $table->actionBy();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_customer');
    }
}

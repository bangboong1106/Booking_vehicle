<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;
use \App\Database\Migration\Base;

class Create3PActionSyncTable extends Base
{
    protected $_table = '3p_action_sync';

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
            $table->string('partner_name')->nullable();
            $table->integer('order_id')->nullable();
            $table->string('order_no')->nullable();
            $table->string('order_code')->nullable();
            $table->string('bill_no')->nullable();
            $table->integer('status')->nullable();
            $table->date('ETD_date_reality')->nullable();
            $table->time('ETD_time_reality')->nullable();
            $table->date('ETA_date_reality')->nullable();
            $table->time('ETA_time_reality')->nullable();
            $table->decimal('amount', 18, 4)->nullable();
            $table->text('note')->nullable();
            $table->text('request_description')->nullable();
            $table->string('response_code')->nullable();
            $table->text('response_description')->nullable();
            $table->integer('sended')->default(0);

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
        Schema::dropIfExists('3p_action_sync');
    }
}

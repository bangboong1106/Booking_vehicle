<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;
use \App\Database\Migration\Base;

class CreateRouteApprovalHistoryTable extends Base
{
    protected $_table = 'route_approval_history';

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

            $table->integer('id')->nullable();
            $table->integer('route_id')->nullable();
            $table->integer('approved_id')->nullable();
            $table->dateTime('approved_date')->nullable();
            $table->string('approved_note')->nullable();


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
        Schema::dropIfExists('route_approval_history');
    }
}

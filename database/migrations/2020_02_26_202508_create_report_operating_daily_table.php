<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;
use \App\Database\Migration\Base;

class CreateReportOperatingDailyTable extends \App\Database\Migration\Create
{
    protected $_table = 'report_operating_daily';

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
            $table->string('label')->nullable();
            $table->decimal('value', 18, 4)->nullable();
            $table->integer('date')->nullable();
            $table->integer('type')->nullable();


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
        Schema::dropIfExists('report_operating_daily');
    }
}

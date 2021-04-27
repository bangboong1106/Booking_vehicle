<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;

class CreatePartnerTable extends \App\Database\Migration\Create
{
    protected $_table = 'partner';

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

        Schema::create($this->_table, function (Blueprint $table) {
            $table->increments('id');
            $table->string('code', 50);
            $table->char('active', '1')->default(1);
            $table->char('mobile_no', 20)->nullable();
            $table->string('email', 255);
            $table->string('full_name', 250);
            $table->string('tax_code', 250);
            $table->string('delegate', 500)->nullable();
            $table->string('address', 500)->nullable();
            $table->string('current_address', 500)->nullable();
            $table->string('province_id', 5)->nullable();
            $table->string('district_id', 5)->nullable();
            $table->string('ward_id', 5)->nullable();
            $table->char('longitude', 50)->nullable();
            $table->char('latitude', 50)->nullable();
            $table->text('note')->nullable();

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
        Schema::dropIfExists('partner');
    }
}

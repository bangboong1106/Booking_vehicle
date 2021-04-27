<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;

class CreateCustomer extends \App\Database\Migration\Create
{
    protected $_table = 'customer';

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
            $table->string('customer_code', 50);
            $table->char('active', '1')->default(1);
            $table->char('mobile_no', 20)->nullable();
            $table->string('identity_no', 20)->nullable();
            $table->string('full_name', 250);
            $table->string('address', 255)->nullable();
            $table->string('current_address', 255)->nullable();
            $table->date('birth_date')->nullable();
            $table->string('email', 255)->nullable();

            $table->char('sex', 10);
            $table->string('full_name_accent', 250)->nullable();
            $table->char('standard_mobile_no', 20)->nullable();
            $table->text('note')->nullable();
            $table->string('province_id', 5)->nullable();
            $table->string('district_id', 5)->nullable();
            $table->string('ward_id', 5)->nullable();
            $table->char('longitude', 50)->nullable();
            $table->char('latitude', 50)->nullable();

            $table->actionBy();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}

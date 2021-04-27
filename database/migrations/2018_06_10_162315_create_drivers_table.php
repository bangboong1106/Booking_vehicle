<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;

class CreateDriversTable extends \App\Database\Migration\Create
{
    protected $_table = 'drivers';
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
            $table->integer('user_id');
            $table->char('active', '1')->default(1);
            $table->char('mobile_no', 20)->nullable();
            $table->string('identity_no', 20)->nullable();
            $table->string('full_name', 250);
            $table->string('address', 255)->nullable();
            $table->string('current_address', 255)->nullable();
            $table->date('birth_date')->nullable();
            $table->char('sex', 10);
            $table->string('full_name_accent', 250)->nullable();
            $table->char('standard_mobile_no', 20)->nullable();
            $table->text('note')->nullable();
            $table->integer('working_status')->nullable();
            $table->actionBy();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}

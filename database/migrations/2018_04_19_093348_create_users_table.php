<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;

class CreateUsersTable extends \App\Database\Migration\Create
{
	protected $_table = 'users';
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
		    $table->string('name', 256);
		    $table->string('email', 256);
		    $table->string('password', 64);
		    $table->string('phone_number', 20);
		    $table->tinyInteger('dob')->default(0);
		    $table->tinyInteger('mob')->default(0);
		    $table->smallInteger('yob')->default(0);
		    $table->string('fbId', 64)->nullable();
		    $table->actionBy();
		    $table->timestamps();
		    $table->softDeletes();
	    });
    }
}

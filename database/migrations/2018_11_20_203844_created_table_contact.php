<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;

class CreatedTableContact extends \App\Database\Migration\Create
{
    protected $_table = 'contact';

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

            $table->string('contact_name', '250');
            $table->string('phone_number', '15');
            $table->string('email', '250')->nullable();
            $table->string('full_address','500')->nullable();
            $table->string('address', 255)->nullable();
            $table->char('active', 1)->default(1);

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

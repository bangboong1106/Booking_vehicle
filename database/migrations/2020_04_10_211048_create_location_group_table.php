<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;

class CreateLocationGroupTable extends \App\Database\Migration\Create
{
    protected $_table = 'location_group';
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
            $table->string('code', 100)->nullable();
            $table->string('title', 255)->nullable();
            $table->text('description');

            $table->actionBy();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('locations', function (Blueprint $table) {
            $table->integer('location_group_id')->nullable()->after('limited_day');
        });
    }
}

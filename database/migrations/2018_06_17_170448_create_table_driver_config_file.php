<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;

class CreateTableDriverConfigFile extends \App\Database\Migration\Create
{
    protected $_table = 'driver_config_file';
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
            $table->char('active', 1)->default('1');
            $table->string('file_name', 255)->nullable();
            $table->char('is_required', 1)->default('0');
            $table->string('allow_extension', 255)->nullable();
            $table->char('is_show_expired', 1)->default(0);
            $table->char('is_show_register', 1)->default(0);
            $table->text('note')->nullable();

            $table->actionBy();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}

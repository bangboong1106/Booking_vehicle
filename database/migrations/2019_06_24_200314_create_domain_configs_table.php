<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;

class CreateDomainConfigsTable extends \App\Database\Migration\Create
{
    protected $_table = 'domain_configs';

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

            $table->string('code')->nullable();
            $table->string('domain')->nullable();
            $table->string('description')->nullable();

            $table->actionBy();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('domain_configs');
    }
}

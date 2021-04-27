<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;

class CreateLpCompanySupportsTable extends \App\Database\Migration\Create
{
    protected $_table = 'lp_company_supports';

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

            $table->string('company_name')->nullable();
            $table->string('full_name')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('type')->nullable();
            $table->string('remark')->nullable();
            $table->integer('status')->default(0)->nullable();

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

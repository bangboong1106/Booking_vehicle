<?php

use App\Database\Migration\Base;
use App\Database\Migration\CustomBlueprint as Blueprint;

class Create3PApiConfigTable extends Base
{
    protected $_table = '3p_api_configs';

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
            $table->string('name')->nullable();
            $table->string('client_id')->nullable();
            $table->string('client_secret')->nullable();
            $table->string('grant_type')->nullable();
            $table->string('username')->nullable();
            $table->string('password')->nullable();
            $table->string('env')->nullable()->comment("0-dev 1-prod");
            $table->string('access_token')->nullable();
            $table->string('token_type')->nullable();
            $table->string('refresh_token')->nullable();
            $table->string('expires_in')->nullable();
            $table->string('scope')->nullable();
            $table->string('expired')->nullable();
            $table->string('request_header_authen')->nullable();
            $table->string('note')->nullable();

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
        Schema::dropIfExists('3p_api_configs');
    }
}

<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;

class CreateFcmTokensTable extends \App\Database\Migration\Create
{
    protected $_table = 'fcm_tokens';

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

            $table->string('fcm_token')->nullable();
            $table->string('user_id')->nullable();
            $table->string('driver_id')->nullable();
            $table->timestamp('expire_date')->nullable();

            $table->actionBy();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('fcm_tokens');
    }
}

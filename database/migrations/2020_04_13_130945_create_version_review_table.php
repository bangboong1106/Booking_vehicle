<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;

class CreateVersionReviewTable extends \App\Database\Migration\Create
{
    protected $_table = 'version_review';

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

            $table->string('version', 255)->nullable();
            $table->integer('reviewed')->nullable();
            $table->actionBy();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}

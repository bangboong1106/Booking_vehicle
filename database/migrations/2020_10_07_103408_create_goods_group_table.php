<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;

class CreateGoodsGroupTable extends \App\Database\Migration\Create
{
    protected $_table = 'goods_group';
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

        Schema::create('goods_group', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code', 256);
            $table->string('name', 256);
            $table->integer('parent_id')->nullable()->index();
            $table->integer('lidx')->nullable()->index();
            $table->integer('ridx')->nullable()->index();
            $table->integer('depth')->nullable();

            $table->actionBy();
            $table->softDeletes();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('goods_group');
    }

}

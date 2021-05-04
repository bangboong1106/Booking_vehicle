<?php


use \App\Database\Migration\CustomBlueprint as Blueprint;
use \App\Database\Migration\Base;

class AddColumnTypeShipsToGoodTypeTable extends Base
{
    protected $_table = 'goods_type';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn($this->getTable(), 'type_ships')) {
            Schema::table($this->getTable(), function (Blueprint $table) {
                $table->integer('type_ships')->nullable();
            });
        }

       
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table($this->getTable(), function (Blueprint $table) {
            $table->dropColumn('type_ships');
        });
        
    }
}

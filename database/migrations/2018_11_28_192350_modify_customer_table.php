<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;
use \App\Database\Migration\Base;


class ModifyCustomerTable extends Base
{
    protected $_table = 'customer';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->getTable(), function (Blueprint $table) {
            $table->dropColumn('user_id');
            $table->string('avatar_id', 100)->nullable()->after('id');
            $table->string('username', 250)->after('avatar_id');
            $table->string('password', 64)->after('username');;
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table($this->getTable(), function (Blueprint $table) {
            $table->integer('user_id')->after('id');
            $table->dropColumn('avatar_id');
            $table->dropColumn('username');
            $table->dropColumn('password');
        });
    }
}

<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;
use \App\Database\Migration\Base;

class AddGroupToRolesTable extends Base
{
    protected $_table = 'roles';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn($this->getTable(), 'group')) {
            Schema::table($this->getTable(), function (Blueprint $table) {
                $table->string('group', 250)->default('admin')->after('guard_name');
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
        if (Schema::hasColumn($this->getTable(), 'group')) {
            Schema::table($this->getTable(), function (Blueprint $table) {
                $table->dropColumn('group');
            });
        }
    }
}

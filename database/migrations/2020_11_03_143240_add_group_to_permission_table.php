<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;
use \App\Database\Migration\Base;

class AddGroupToPermissionTable extends Base
{
    protected $_table = 'permissions';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn($this->getTable(), 'group')) {
            Schema::table($this->getTable(), function (Blueprint $table) {
                $table->string('group', 255)->nullable()->after('guard_name')->comment('group permissions');
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

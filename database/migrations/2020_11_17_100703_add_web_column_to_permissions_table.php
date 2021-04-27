<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;
use \App\Database\Migration\Base;

class AddWebColumnToPermissionsTable extends Base
{
    protected $_table = 'permissions';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn($this->getTable(), 'web')) {
            Schema::table($this->getTable(), function (Blueprint $table) {
                $table->string('web', 250)->default('admin');
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
        if (Schema::hasColumn($this->getTable(), 'web')) {
            Schema::table($this->getTable(), function (Blueprint $table) {
                $table->dropColumn('web');
            });
        }
    }
}

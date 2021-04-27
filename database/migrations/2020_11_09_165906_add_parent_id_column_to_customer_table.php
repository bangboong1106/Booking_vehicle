<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;
use \App\Database\Migration\Base;

class AddParentIdColumnToCustomerTable extends Base
{
    protected $_table = 'customer';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn($this->getTable(), 'parent_id')) {
            Schema::table($this->getTable(), function (Blueprint $table) {
                $table->integer('parent_id')->nullable()->after('user_id');
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
        if (Schema::hasColumn($this->getTable(), 'parent_id')) {
            Schema::table($this->getTable(), function (Blueprint $table) {
                $table->dropColumn('parent_id');
            });
        }
    }
}

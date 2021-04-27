<?php

use App\Database\Migration\Base;
use App\Database\Migration\CustomBlueprint as Blueprint;

class AddColumnToCustomerTable extends Base
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
            $table->string('delegate')->nullable()->after('full_name');
            $table->string('tax_code')->nullable()->after('delegate');
            $table->string('type')->nullable()->after('tax_code');
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
            $table->dropColumn('delegate');
            $table->dropColumn('tax_code');
            $table->dropColumn('type');
        });
    }
}

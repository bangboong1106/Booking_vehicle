<?php

use App\Database\Migration\Base;
use App\Database\Migration\CustomBlueprint as Blueprint;

class AddColumnFileIdToContractsTable extends Base
{
    protected $_table = 'contracts';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->getTable(), function (Blueprint $table) {
            $table->string('file_id', 50)->nullable()->after('customer_id');

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
            $table->dropColumn('file_id');
        });
    }
}

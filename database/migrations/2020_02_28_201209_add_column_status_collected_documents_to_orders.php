<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;
use \App\Database\Migration\Base;

class AddColumnStatusCollectedDocumentsToOrders extends Base
{
    protected $_table = 'orders';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->getTable(), function (Blueprint $table) {
            $table->integer('status_collected_documents')->nullable()->after('is_collected_documents');
            $table->date('date_collected_documents')->nullable()->after('status_collected_documents');
            $table->time('time_collected_documents')->nullable()->after('date_collected_documents');
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
            $table->dropColumn('status_collected_documents');
            $table->dropColumn('date_collected_documents');
            $table->dropColumn('time_collected_documents');
        });
    }
}

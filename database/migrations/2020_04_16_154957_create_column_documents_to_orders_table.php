<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;

class CreateColumnDocumentsToOrdersTable extends \App\Database\Migration\Create
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
            $table->integer('num_of_document_page')->nullable()->after('time_collected_documents');
            $table->date('date_collected_documents_reality')->nullable()->after('num_of_document_page');
            $table->time('time_collected_documents_reality')->nullable()->after('date_collected_documents_reality');
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
            $table->dropColumn('num_of_document_page');
            $table->dropColumn('date_collected_documents_reality');
            $table->dropColumn('time_collected_documents_reality');
        });
    }
}

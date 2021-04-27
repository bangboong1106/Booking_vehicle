<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;

class CreateColumnDocumentsTypeToOrdersTable extends \App\Database\Migration\Create
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
            $table->string('document_type')->nullable()->after('time_collected_documents_reality');
            $table->string('document_note')->nullable()->after('document_type');
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
            $table->dropColumn('document_type');
            $table->dropColumn('document_note');
        });
    }
}

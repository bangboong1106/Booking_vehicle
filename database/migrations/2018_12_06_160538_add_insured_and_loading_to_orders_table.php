<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;
use \App\Database\Migration\Base;

class AddInsuredAndLoadingToOrdersTable extends Base
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
            $table->smallInteger('insured_goods')->default(0)->after('precedence');
            $table->smallInteger('loading_destination')->default(0)->after('contact_email_destination');
            $table->smallInteger('loading_arrival')->default(0)->after('contact_email_arrival');

            $table->decimal('loading_destination_fee', 18, 4)->after('loading_destination')->default(0);
            $table->decimal('loading_arrival_fee', 18, 4)->after('loading_arrival')->default(0);
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
            $table->dropColumn('insured_goods');
            $table->dropColumn('loading_destination');
            $table->dropColumn('loading_arrival');
            $table->dropColumn('loading_destination_fee');
            $table->dropColumn('loading_arrival_fee');
        });
    }
}

<?php

use App\Database\Migration\Base;
use App\Database\Migration\CustomBlueprint as Blueprint;

class AddColumnInformationToOrderTable extends Base
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
            $table->text('informative_destination')->after('loading_destination_fee')->nullable();
            $table->text('informative_arrival')->after('loading_arrival_fee')->nullable();
            $table->text('good_details')->after('good_code')->nullable();
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
            $table->dropColumn('informative_destination');
            $table->dropColumn('informative_arrival');
            $table->dropColumn('good_details');
        });
    }
}

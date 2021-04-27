<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;
use \App\Database\Migration\Base;

class ModifyReportSchedulesTable extends Base
{
    protected $_table = 'report_schedules';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->getTable(), function (Blueprint $table) {
            $table->text("email")->change();
            $table->string('report_type', 500)->nullable()->after('email');
        });
    }

    public function down()
    {
        Schema::table($this->getTable(), function (Blueprint $table) {
            $table->string('email', 300)->change();
            $table->dropColumn('report_type');
        });
    }
}

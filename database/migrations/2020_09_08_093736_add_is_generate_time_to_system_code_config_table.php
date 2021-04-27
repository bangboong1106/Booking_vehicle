<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;
use \App\Database\Migration\Base;

class AddIsGenerateTimeToSystemCodeConfigTable extends Base
{
    protected $_table = 'system_code_config';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->getTable(), function (Blueprint $table) {
            $table->text('time_format')->nullable();
            $table->char('is_generate_time')->default(0)->nullable();
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
            $table->dropColumn('is_generate_time');
            $table->dropColumn('time_format');
        });
    }
}

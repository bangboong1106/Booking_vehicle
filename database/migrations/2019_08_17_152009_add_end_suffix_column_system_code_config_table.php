<?php

use App\Database\Migration\Base;
use App\Database\Migration\CustomBlueprint as Blueprint;

class AddEndSuffixColumnSystemCodeConfigTable extends Base
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
            $table->string('end_suffix', 250)->nullable()->after('type');
            $table->string('code_tmp', 250)->nullable()->after('end_suffix');
            $table->string('suffix_tmp', 250)->nullable()->after('code_tmp');
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
            $table->dropColumn('end_suffix_tmp');
            $table->dropColumn('code_tmp');
            $table->dropColumn('suffix_tmp');
        });
    }
}

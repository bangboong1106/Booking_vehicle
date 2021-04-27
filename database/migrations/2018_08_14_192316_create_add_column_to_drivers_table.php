<?php

use App\Database\Migration\Base;
use App\Database\Migration\CustomBlueprint as Blueprint;

class CreateAddColumnToDriversTable extends Base
{
    protected $_table = 'drivers';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->getTable(), function (Blueprint $table) {
            $table->string('hometown', 255)->nullable()->after('working_status');
            $table->integer('vehicle_team_id')->nullable()->after('hometown');
            $table->integer('experience_drive')->nullable()->after('vehicle_team_id');
            $table->integer('experience_work')->nullable()->after('experience_drive');
            $table->date('work_date')->nullable()->after('experience_work');
            $table->integer('driver_vehicle_id')->nullable()->after('work_date');
            $table->string('vehicle_old', 100)->nullable()->after('driver_vehicle_id');
            $table->string('evaluate', 1000)->nullable()->after('vehicle_old');
            $table->string('rank', 255)->nullable()->after('evaluate');
            $table->string('work_description', 1000)->nullable()->after('rank');
            $table->string('id_no', 20)->nullable()->after('work_description');
            $table->string('driver_license', 100)->nullable()->after('id_no');
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
            $table->dropColumn('hometown');
            $table->dropColumn('vehicle_team_id');
            $table->dropColumn('experience_drive');
            $table->dropColumn('experience_work');
            $table->dropColumn('work_date');
            $table->dropColumn('driver_vehicle_id');
            $table->dropColumn('vehicle_old');
            $table->dropColumn('evaluate');
            $table->dropColumn('rank');
            $table->dropColumn('work_description');
            $table->dropColumn('id_no');
            $table->dropColumn('driver_license');
        });
    }
}

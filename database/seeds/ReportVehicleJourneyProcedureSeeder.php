<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReportVehicleJourneyProcedureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $sql = file_get_contents(database_path() . '/scripts/report_vehicle_journey.sql');
        DB::unprepared($sql);
    }
}

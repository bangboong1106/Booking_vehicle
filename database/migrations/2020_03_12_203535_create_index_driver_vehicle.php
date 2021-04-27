<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIndexDriverVehicle extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('
            drop procedure if exists CreateIndexOnDriveVehicle;
            create procedure CreateIndexOnDriveVehicle()
            begin
                IF NOT EXISTS (SELECT * FROM INFORMATION_SCHEMA.STATISTICS  WHERE TABLE_NAME = \'driver_vehicle\'
                                        AND INDEX_NAME = \'idx_driver_vehicle_driver_id\' AND INDEX_SCHEMA= DATABASE()) THEN
                              ALTER TABLE `driver_vehicle` ADD INDEX `idx_driver_vehicle_driver_id` (`driver_id`);
                END IF; 
                
                IF NOT EXISTS (SELECT * FROM INFORMATION_SCHEMA.STATISTICS  WHERE TABLE_NAME = \'driver_vehicle\'
                                        AND INDEX_NAME = \'idx_driver_vehicle_vehicle_id\' AND INDEX_SCHEMA= DATABASE()) THEN
                              ALTER TABLE `driver_vehicle` ADD INDEX `idx_driver_vehicle_vehicle_id` (`vehicle_id`);
                END IF; 
            END;
            call CreateIndexOnDriveVehicle();
            drop procedure if exists CreateIndexOnDriveVehicle;
                    ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}

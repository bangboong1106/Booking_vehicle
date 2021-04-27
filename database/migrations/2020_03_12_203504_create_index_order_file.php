<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIndexOrderFile extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('
            drop procedure if exists CreateIndexOnOrderFile;
            create procedure CreateIndexOnOrderFile()
            begin
                IF NOT EXISTS (SELECT * FROM INFORMATION_SCHEMA.STATISTICS  WHERE TABLE_NAME = \'order_file\'
                                        AND INDEX_NAME = \'idx_order_file_order_id\' AND INDEX_SCHEMA= DATABASE()) THEN
                              ALTER TABLE `order_file` ADD INDEX `idx_order_file_order_id` (`order_id`);
                END IF; 
            END;
            call CreateIndexOnOrderFile();
            drop procedure if exists CreateIndexOnOrderFile;
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

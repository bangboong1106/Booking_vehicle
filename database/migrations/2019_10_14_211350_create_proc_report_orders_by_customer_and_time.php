<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProcReportOrdersByCustomerAndTime extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('
            DROP PROCEDURE IF EXISTS `proc_report_orders_by_customer_and_time`;
            CREATE PROCEDURE `proc_report_orders_by_customer_and_time`(IN customer_id int, IN from_date date, IN to_date date)
                BEGIN
                    CREATE TEMPORARY TABLE IF NOT EXISTS `temp_day`	
                    select DATE_ADD(from_date, interval N DAY) AS `order_date`
                    from tally_number
                    WHERE DATE_ADD(from_date, interval N DAY) <= to_date;
                    
                    SELECT
                        COUNT(o.id) AS `count`,
                        td.`order_date`
                    FROM temp_day td
                    LEFT JOIN orders o on o.`order_date` = td.`order_date`
                    WHERE o.customer_id = customer_id
                        OR o.id IS NULL
                    GROUP BY td.`order_date`
                    ORDER BY td.`order_date`;
                END
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS proc_report_orders_by_customer_and_time');
    }
}

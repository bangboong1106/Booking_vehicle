<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateDataColumnConfigTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("
        update column_config set config = REPLACE(config, 'customer_id', 'name_of_customer_id')  where table_id = 1 and config not like '%name_of_customer_id%';
        update column_config set config = REPLACE(config, 'location_destination_id', 'name_of_location_destination_id')  where table_id = 5 and config not like '%name_of_location_destination_id%';
        update column_config set config = REPLACE(config, 'location_arrival_id', 'name_of_location_arrival_id')  where table_id = 5 and config not like '%name_of_location_arrival_id%';
        update column_config set config = REPLACE(config, 'customer_id', 'name_of_customer_id')  where table_id = 7 and config not like '%name_of_customer_id%';
        update column_config set config = REPLACE(config, 'customer_id', 'name_of_customer_id')  where table_id = 7 and config not like '%name_of_customer_id%';
        update column_config set config = REPLACE(config, 'customer_full_name', 'name_of_customer_id')  where table_id = 7 and config not like '%name_of_customer_id%';
        update column_config set config = REPLACE(config, 'payment_user_id', 'name_of_payment_user_id') where table_id = 7 and config not like '%name_of_payment_user_id%';
        update column_config set config = REPLACE(config, 'ins_id', 'name_of_ins_id') where config not like '%name_of_ins_id%';
        update column_config set config = REPLACE(config, 'upd_id', 'name_of_upd_id') where config not like '%name_of_upd_id%';
        update column_config set config = REPLACE(config, 'total_order', 'count_order') where config not like '%count_order%';
        update column_config set config = REPLACE(config, '\"order\"', '\"order_codes\"') where table_id = 5 and config not like '%order_codes%';

        update column_config set config = REPLACE(config, 'customer_id', 'name_of_customer_id')  where table_id = 31 and config not like '%name_of_customer_id%';
         
        update column_config set config = REPLACE(config, 'location_destination', 'name_of_location_destination_id') where table_id = 6 and  config not like '%name_of_location_destination_id%';
        update column_config set config = REPLACE(config, 'location_arrival', 'name_of_location_arrival_id') where table_id = 6 and  config not like '%name_of_location_arrival_id%';

        update column_config set config = REPLACE(config, 'destination_location_title', 'name_of_location_destination_id') where table_id = 7 and  config not like '%name_of_location_destination_id%';
        update column_config set config = REPLACE(config, 'arrival_location_title', 'name_of_location_arrival_id') where table_id = 7 and  config not like '%name_of_location_arrival_id%';
        

        "
        );
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

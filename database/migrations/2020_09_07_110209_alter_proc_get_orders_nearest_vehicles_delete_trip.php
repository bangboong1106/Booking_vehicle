<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterProcGetOrdersNearestVehiclesDeleteTrip extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('
        DROP PROCEDURE IF EXISTS `proc_get_orders_nearest_vehicles`;
        CREATE PROCEDURE `proc_get_orders_nearest_vehicles`()
BEGIN
	drop temporary table if exists `tmp_location`;
    create temporary table `tmp_location` (
		vehicle_id int,
		latitude CHAR(50),
		longitude CHAR(50),
        valid bit(1)
    );
    
	drop temporary table if exists `tmp_vechile_location`;
    create temporary table `tmp_vechile_location` (
		location_id int,
        distance_in_km decimal
    );
    
    drop temporary table if exists `tmp_duplicate_vechile_location`;
    create temporary table  `tmp_duplicate_vechile_location` (
		location_id int,
        distance_in_km decimal
    );
    
	drop temporary table if exists `tmp_order`;
    create temporary table  `tmp_order` (
		order_id int,
        ins_id int,
        customer_id int,
        driver_id int,
        vehicle_id int,
        location_id int,
        location_type int
    );
    set @distance = (select CAST(`value` as decimal(18,4)) from system_config where `key` =\'Notification.DistanceUnit\' limit 1);
    if(@distance = \'\' or @distance is null)
    then
		set @distance = 10;
    end if;
    
    insert into `tmp_location`
	select  v.id as vehicle_id,
			v.`latitude` as latitude,
			v.`longitude` as longitude,
            0 as valid
	from `vehicle` as v
	where v.del_flag = 0;

	While exists(Select `vehicle_id` From `tmp_location` Where `valid` = 0) Do

		Select `vehicle_id`, `latitude`, `longitude`
        Into @vehicleId, @latitude, @longitude
        From tmp_location 
        Where `valid` = 0 Limit 1;

		delete from `tmp_vechile_location`;
        delete from `tmp_duplicate_vechile_location`;
        
		INSERT INTO tmp_vechile_location
		SELECT 
                z.id as location_id,
				p.distance_unit
                 * DEGREES(ACOS(COS(RADIANS(p.latpoint))
                 * COS(RADIANS(z.latitude))
                 * COS(RADIANS(p.longpoint) - RADIANS(z.longitude))
                 + SIN(RADIANS(p.latpoint))
                 * SIN(RADIANS(z.latitude)))) AS distance_in_km
		  FROM locations AS z
		  JOIN (   /* these are the query parameters */
				SELECT  @latitude  AS latpoint,  @longitude AS longpoint,
						@distance AS radius,      111.045 AS distance_unit
			) AS p ON 1=1
		  WHERE z.latitude
			 BETWEEN p.latpoint  - (p.radius / p.distance_unit)
				 AND p.latpoint  + (p.radius / p.distance_unit)
			AND z.longitude
			 BETWEEN p.longpoint - (p.radius / (p.distance_unit * COS(RADIANS(p.latpoint))))
				 AND p.longpoint + (p.radius / (p.distance_unit * COS(RADIANS(p.latpoint))))
		  ORDER BY distance_in_km
		  LIMIT 5;
          
          insert into tmp_duplicate_vechile_location
          select * from tmp_vechile_location;
          
          INSERT INTO `tmp_order`(
				order_id ,
				ins_id ,
				customer_id ,
				driver_id ,
				vehicle_id ,
				location_id ,
				location_type 
		)
          SELECT 
			o.id as order_id,
            o.ins_id as ins_id,
            o.customer_id as customer_id,
            o.primary_driver_id as driver_id,
            @vehicleId as vehicle_id,
           case when o.status = 3 then T1.location_id else T2.location_id end as location_id,
           case when o.status = 3 then 1 else 2 end as location_type
          FROM `orders` as o
            left join tmp_duplicate_vechile_location as T1 on o.location_destination_id = T1.location_id
            left join tmp_vechile_location as T2 on o.location_arrival_id = T2.location_id
		  WHERE o.del_flag = 0
            AND o.status in (3,4)
            AND o.primary_driver_id != 0
            AND o.vehicle_id = @vehicleId
            AND( (o.status = 3 and T1.location_id is not null)
				or (o.status = 4 and T2.location_id is not null)
            );
            
		UPDATE `tmp_location` 
		SET 
			`valid` = 1
		WHERE
			`vehicle_id` = @vehicleId;
	END WHILE;
    
    insert into order_notification (order_id, is_notify_destination_location, is_notify_arrival_location, created_at, updated_at)
    select distinct order_id, 0, 0, CURDATE(), CURDATE() from tmp_order t1
    where not exists (
		SELECT 1 
		FROM order_notification t2 WHERE t1.order_id = t2.order_id
	);

    select  distinct o.* 
    from tmp_order o
    join order_notification ot on o.order_id = ot.order_id
    where (o.location_type = 1 and ot.is_notify_destination_location = 0) 
    or (o.location_type = 2 and ot.is_notify_arrival_location = 0 );
    
UPDATE order_notification t
        INNER JOIN
    (select distinct t1.* from tmp_order as t1) as o ON t.order_id = o.order_id 
	SET 
   t.is_notify_destination_location  = (case when o.location_type = 1 then 1 else  t.is_notify_destination_location end),
   t.is_notify_arrival_location  = (case when o.location_type = 2 then 1 else  t.is_notify_arrival_location end),
   t.updated_at = CURDATE();
END'
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS proc_get_orders_nearest_vehicles');
    }
}

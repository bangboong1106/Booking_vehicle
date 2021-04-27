SELECT if(
    exists(
        SELECT DISTINCT index_name
        FROM information_schema.statistics
        WHERE table_schema = database()
              AND table_name = 'order_trip' AND index_name LIKE 'order_trip_order_id_idx'
    )
    , 'select ''index order_trip_order_id_idx exists'' _______;'
    , 'create index order_trip_order_id_idx on order_trip(order_id)')
INTO @a;
PREPARE stmt1 FROM @a;
EXECUTE stmt1;
DEALLOCATE PREPARE stmt1;

SELECT if(
    exists(
        SELECT DISTINCT index_name
        FROM information_schema.statistics
        WHERE table_schema = database()
              AND table_name = 'order_trip' AND index_name LIKE 'order_trip_trip_id_idx'
    )
    , 'select ''index order_trip_trip_id_idx exists'' _______;'
    , 'create index order_trip_trip_id_idx on order_trip(trip_id)')
INTO @a;
PREPARE stmt1 FROM @a;
EXECUTE stmt1;
DEALLOCATE PREPARE stmt1;

SELECT if(
    exists(
        SELECT DISTINCT index_name
        FROM information_schema.statistics
        WHERE table_schema = database()
              AND table_name = 'driver_trip' AND index_name LIKE 'driver_trip_vehicle_id_idx'
    )
    , 'select ''index driver_trip_vehicle_id_idx exists'' _______;'
    , 'create index driver_trip_vehicle_id_idx on driver_trip(vehicle_id)')
INTO @a;
PREPARE stmt1 FROM @a;
EXECUTE stmt1;
DEALLOCATE PREPARE stmt1;

SELECT if(
    exists(
        SELECT DISTINCT index_name
        FROM information_schema.statistics
        WHERE table_schema = database()
              AND table_name = 'driver_trip' AND index_name LIKE 'driver_trip_vehicle_id_idx'
    )
    , 'select ''index driver_trip_driver_id_idx exists'' _______;'
    , 'create index driver_trip_driver_id_idx on driver_trip(driver_id)')
INTO @a;
PREPARE stmt1 FROM @a;
EXECUTE stmt1;
DEALLOCATE PREPARE stmt1;


SELECT if(
    exists(
        SELECT DISTINCT index_name
        FROM information_schema.statistics
        WHERE table_schema = database()
              AND table_name = 'driver_trip' AND index_name LIKE 'driver_trip_trip_id_idx'
    )
    , 'select ''index driver_trip_trip_id_idx exists'' _______;'
    , 'create index driver_trip_trip_id_idx on driver_trip(trip_id)')
INTO @a;
PREPARE stmt1 FROM @a;
EXECUTE stmt1;
DEALLOCATE PREPARE stmt1;


SELECT if(
    exists(
        SELECT DISTINCT index_name
        FROM information_schema.statistics
        WHERE table_schema = database()
              AND table_name = 'orders' AND index_name LIKE 'order_location_arrival_id_idx'
    )
    , 'select ''index order_location_arrival_id_idx exists'' _______;'
    , 'create index order_location_arrival_id_idx on orders(location_arrival_id)')
INTO @a;
PREPARE stmt1 FROM @a;
EXECUTE stmt1;
DEALLOCATE PREPARE stmt1;


SELECT if(
    exists(
        SELECT DISTINCT index_name
        FROM information_schema.statistics
        WHERE table_schema = database()
              AND table_name = 'orders' AND index_name LIKE 'order_location_destination_id_idx'
    )
    , 'select ''index order_location_destination_id_idx exists'' _______;'
    , 'create index order_location_destination_id_idx on orders(location_destination_id)')
INTO @a;
PREPARE stmt1 FROM @a;
EXECUTE stmt1;
DEALLOCATE PREPARE stmt1;  

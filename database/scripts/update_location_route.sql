UPDATE routes r INNER JOIN
( SELECT DISTINCT rl1.route_id,rl1.destination_location_id,rl1.destination_location_date,rl1.destination_location_time
FROM route_location rl1
INNER JOIN (SELECT rl.route_id, MIN(CONCAT(rl.destination_location_date,' ',rl.destination_location_time)) min_date
						, MAX(CONCAT(rl.arrival_location_date,' ',rl.arrival_location_time)) max_date
						FROM route_location rl WHERE rl.del_flag = 0 GROUP BY rl.route_id) tmp1 ON tmp1.route_id = rl1.route_id
						AND tmp1.min_date = CONCAT(rl1.destination_location_date,' ',rl1.destination_location_time)
) tmp2 ON r.id = tmp2.route_id
INNER JOIN
( SELECT DISTINCT rl1.route_id,rl1.arrival_location_id,rl1.arrival_location_date,rl1.arrival_location_time
FROM route_location rl1
INNER JOIN (SELECT rl.route_id, MIN(CONCAT(rl.destination_location_date,' ',rl.destination_location_time)) min_date
						, MAX(CONCAT(rl.arrival_location_date,' ',rl.arrival_location_time)) max_date
						FROM route_location rl WHERE rl.del_flag = 0 GROUP BY rl.route_id) tmp1 ON tmp1.route_id = rl1.route_id
						AND tmp1.max_date = CONCAT(rl1.arrival_location_date,' ',rl1.arrival_location_time)
) tmp3 ON r.id = tmp3.route_id
SET r.location_destination_id = tmp2.destination_location_id , r.location_arrival_id = tmp3.arrival_location_id,
r.ETD_date = tmp2.destination_location_date, r.ETD_time = tmp2.destination_location_time,
r.ETA_date = tmp3.arrival_location_date, r.ETA_time = tmp3.arrival_location_time
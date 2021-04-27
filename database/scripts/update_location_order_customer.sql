UPDATE order_customer oc INNER JOIN
( SELECT DISTINCT tmp1.order_customer_id,ol1.location_id,ol1.date,ol1.time
FROM order_customer_order oco1
INNER JOIN order_locations ol1 ON oco1.order_id = ol1.order_id
INNER JOIN (SELECT oco.order_customer_id, MIN(CONCAT(ol.date,' ',ol.time)) min_date
						FROM order_customer_order oco
						INNER JOIN order_locations ol ON oco.order_id = ol.order_id AND type = 1
						WHERE oco.del_flag = 0
						GROUP BY oco.order_customer_id) tmp1 ON tmp1.order_customer_id = oco1.order_customer_id
						AND tmp1.min_date = CONCAT(ol1.date,' ',ol1.time)
) tmp2 ON oc.id = tmp2.order_customer_id
INNER JOIN
( SELECT DISTINCT tmp1.order_customer_id,ol1.location_id,ol1.date,ol1.time
FROM order_customer_order oco1
INNER JOIN order_locations ol1 ON oco1.order_id = ol1.order_id
INNER JOIN (SELECT oco.order_customer_id, MIN(CONCAT(ol.date,' ',ol.time)) min_date
						FROM order_customer_order oco
						INNER JOIN order_locations ol ON oco.order_id = ol.order_id AND type = 2
						WHERE oco.del_flag = 0
						GROUP BY oco.order_customer_id) tmp1 ON tmp1.order_customer_id = oco1.order_customer_id
						AND tmp1.min_date = CONCAT(ol1.date,' ',ol1.time)
) tmp3 ON oc.id = tmp3.order_customer_id
SET oc.location_destination_id = tmp2.location_id , oc.location_arrival_id = tmp3.location_id,
oc.ETD_date = tmp2.date, oc.ETD_time = tmp2.time,
oc.ETA_date = tmp3.date, oc.ETA_time = tmp3.time
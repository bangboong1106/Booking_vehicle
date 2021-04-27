UPDATE routes rt JOIN (SELECT r.id, r.route_code
, STR_TO_DATE(MIN(CONCAT(o.ETD_date_reality,' ',o.ETD_time_reality)),'%Y-%m-%d %H:%i:%s') min_datetime FROM routes r
LEFT JOIN route_order ro ON ro.route_id = r.id
LEFT JOIN orders o ON o.id = ro.order_id
WHERE r.del_flag = 0 AND ro.del_flag = 0 AND o.del_flag = 0 AND r.ETD_date_reality IS NULL
GROUP BY r.id ) tmp ON rt.id = tmp.id AND tmp.min_datetime IS NOT NULL
SET rt.ETD_date_reality = DATE(tmp.min_datetime), rt.ETD_time_reality = TIME(tmp.min_datetime)
WHERE rt.del_flag = 0 AND rt.ETD_date_reality IS NULL ;
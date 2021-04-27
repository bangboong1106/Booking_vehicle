UPDATE order_customer oct JOIN (SELECT oc.id, oc.code
, STR_TO_DATE(MIN(CONCAT(o.ETD_date_reality,' ',o.ETD_time_reality)),'%Y-%m-%d %H:%i:%s') min_datetime FROM order_customer oc
LEFT JOIN order_customer_order oco ON oco.order_customer_id = oc.id
LEFT JOIN orders o ON o.id = oco.order_id
WHERE oc.del_flag = 0 AND oco.del_flag = 0 AND o.del_flag = 0 AND oc.ETD_date_reality IS NULL
GROUP BY oco.id ) tmp ON oct.id = tmp.id AND tmp.min_datetime IS NOT NULL
SET oct.ETD_date_reality = DATE(tmp.min_datetime), oct.ETD_time_reality = TIME(tmp.min_datetime)
WHERE oct.del_flag = 0 AND oct.ETD_date_reality IS NULL ;
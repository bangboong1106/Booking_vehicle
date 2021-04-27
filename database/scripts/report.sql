/*
 Navicat Premium Data Transfer

 Source Server         : 45.118.145.27
 Source Server Type    : MySQL
 Source Server Version : 50650
 Source Host           : 45.118.145.27:3388
 Source Schema         : dev_c20

 Target Server Type    : MySQL
 Target Server Version : 50650
 File Encoding         : 65001

 Date: 14/01/2021 14:32:38
*/

-- ----------------------------
-- Procedure structure for proc_calc_status_documents
-- ----------------------------
DROP PROCEDURE IF EXISTS `proc_calc_status_documents`;

CREATE PROCEDURE `proc_calc_status_documents`()
BEGIN

	DECLARE done INT DEFAULT 0;
	DECLARE id LONG;
	DECLARE status_collected_documents INT;
	DECLARE date_collected_documents DATE;
	DECLARE time_collected_documents TIME;
  DECLARE cur_orders CURSOR FOR SELECT o.id, o.status_collected_documents, o.date_collected_documents, o.time_collected_documents
															FROM orders o WHERE o.status_collected_documents != 2 AND o.date_collected_documents IS NOT NULL AND o.id = 3277;
	OPEN cur_orders;
	REPEAT

		FETCH cur_orders INTO id, status_collected_documents, date_collected_documents, time_collected_documents;

		IF DATE_FORMAT(CONCAT(date_collected_documents,' ',time_collected_documents), '%Y-%m-%d %H:%i') < DATE_FORMAT(NOW(), '%Y-%m-%d %H:%i')THEN
			-- Quá hạn
			UPDATE orders o SET o.status_collected_documents = 3 WHERE o.id = id;

		ELSEIF DATE_FORMAT(CONCAT(date_collected_documents), '%Y-%m-%d') = DATE_FORMAT(DATE_ADD(NOW(), INTERVAL 1 DAY), '%Y-%m-%d') THEN
		-- Đến hạn vào hôm sau
			UPDATE orders o SET o.status_collected_documents = 4 WHERE o.id = id;

		ELSEIF DATE_FORMAT(CONCAT(date_collected_documents), '%Y-%m-%d') = DATE_FORMAT(NOW(), '%Y-%m-%d') THEN
		-- Đến hạn vào hôm nay
			UPDATE orders o SET o.status_collected_documents = 5 WHERE o.id = id;

		END IF;

	UNTIL done END REPEAT;
	CLOSE cur_orders;

END
;


-- ----------------------------
-- Procedure structure for proc_calc_status_documents_daily
-- ----------------------------
DROP PROCEDURE IF EXISTS `proc_calc_status_documents_daily`;

CREATE PROCEDURE `proc_calc_status_documents_daily`()
BEGIN

	DECLARE done INT DEFAULT 0;
	DECLARE id LONG;
	DECLARE status_collected_documents INT;
	DECLARE date_collected_documents DATE;
	DECLARE time_collected_documents TIME;
  DECLARE cur_orders CURSOR FOR SELECT o.id, o.status_collected_documents, o.date_collected_documents, o.time_collected_documents
															FROM orders o WHERE o.status_collected_documents != 2 AND o.date_collected_documents IS NOT NULL;
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

	OPEN cur_orders;
	REPEAT

		FETCH cur_orders INTO id, status_collected_documents, date_collected_documents, time_collected_documents;

		IF DATE_FORMAT(CONCAT(date_collected_documents,' ',time_collected_documents), '%Y-%m-%d %H:%i') < DATE_FORMAT(NOW(), '%Y-%m-%d %H:%i')THEN
			-- Quá hạn
			UPDATE orders o SET o.status_collected_documents = 3 WHERE o.id = id;

		ELSEIF DATE_FORMAT(CONCAT(date_collected_documents), '%Y-%m-%d') = DATE_FORMAT(DATE_ADD(NOW(), INTERVAL 1 DAY), '%Y-%m-%d') THEN
		-- Đến hạn vào hôm sau
			UPDATE orders o SET o.status_collected_documents = 4 WHERE o.id = id;

		ELSEIF DATE_FORMAT(CONCAT(date_collected_documents), '%Y-%m-%d') = DATE_FORMAT(NOW(), '%Y-%m-%d') THEN
		-- Đến hạn vào hôm nay
			UPDATE orders o SET o.status_collected_documents = 5 WHERE o.id = id;

		END IF;

	UNTIL done END REPEAT;
	CLOSE cur_orders;

END
;


-- ----------------------------
-- Procedure structure for proc_get_orders_nearest_vehicles
-- ----------------------------
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
    set @distance = (select CAST(`value` as decimal(18,4)) from system_config where `key` ='Notification.DistanceUnit' limit 1);
    if(@distance = '' or @distance is null)
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
END
;


-- ----------------------------
-- Procedure structure for proc_report_by_income
-- ----------------------------
DROP PROCEDURE IF EXISTS `proc_report_by_income`;

CREATE PROCEDURE `proc_report_by_income`(IN summary BOOLEAN,IN from_date TEXT,IN to_date TEXT,IN dayCondition TEXT, IN partner_ids TEXT)
BEGIN

  DECLARE query TEXT;
	DECLARE summaryQuery TEXT;

	SET query = "SELECT tmp1.id as entity_id, tmp1.full_name as entity_name,DATE_FORMAT(tmp1.date,'%d-%m-%Y') as date,tmp2.status_complete";

	SET query = CONCAT(query," ","FROM (SELECT c.id,c.full_name,cal.date FROM ((SELECT cu.id,CONCAT(cu.customer_code,'|||',cu.full_name) full_name FROM customer cu) UNION (SELECT 0 as id ,'*Chưa gán khách hàng' as full_name)) c");
	SET query = CONCAT(query," ","CROSS JOIN (
										SELECT selected_date as date FROM (
													SELECT adddate('1970-01-01',t4.i*10000 + t3.i*1000 + t2.i*100 + t1.i*10 + t0.i) selected_date
													FROM
														 (SELECT 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t0,
														 (SELECT 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t1,
														 (SELECT 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t2,
														 (SELECT 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t3,
														 (SELECT 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t4
										) d
										WHERE selected_date BETWEEN '",from_date,"' AND '",to_date,"'
										) cal");

	SET query = CONCAT(query," ",") tmp1 LEFT JOIN (SELECT tmp.date,tmp.customer_id,SUM(tmp.amount) as status_complete");

	SET query = CONCAT(query," "," FROM (SELECT (CASE WHEN (o.customer_id IS NULL OR o.customer_id = 0 ) THEN 0 ELSE o.customer_id END) as customer_id
																				,o.amount, ");

	IF dayCondition = 1 THEN
		SET query = CONCAT(query," "," o.ETD_date as date");
	ELSEIF dayCondition = 2 THEN
		SET query = CONCAT(query," "," CASE WHEN o.ETD_date_reality IS NOT NULL THEN o.ETD_date_reality ELSE o.ETD_date END as date");
	ELSEIF dayCondition = 3 THEN
		SET query = CONCAT(query," "," o.ETA_date as date");
	ELSE
		SET query = CONCAT(query," "," CASE WHEN o.ETA_date_reality IS NOT NULL THEN o.ETA_date_reality ELSE o.ETA_date END as date");
	END IF;

	SET query = CONCAT(query," "," FROM orders o WHERE o.del_flag = 0 ");

	IF dayCondition = 1 THEN
		SET query = CONCAT(query," "," AND o.status IN(2,3,4,5,7) AND o.ETD_date BETWEEN '",from_date,"' AND '",to_date,"' ");
	ELSEIF dayCondition = 2 THEN
		SET query = CONCAT(query," "," AND o.status IN (4,5) AND o.ETD_date_reality BETWEEN '",from_date,"' AND '",to_date,"' ");
	ELSEIF dayCondition = 3 THEN
		SET query = CONCAT(query," "," AND o.status IN(2,3,4,5,7) AND o.ETA_date BETWEEN '",from_date,"' AND '",to_date,"' ");
	ELSE
		SET query = CONCAT(query," "," AND o.status = 5 AND o.ETA_date_reality BETWEEN '",from_date,"' AND '",to_date,"' ");
	END IF;

	IF partner_ids IS NOT NULL AND partner_ids != "" THEN
		SET query = CONCAT(query," ","AND o.partner_id IN (",partner_ids,")");
	END IF;

	SET query = CONCAT(query," ",") tmp GROUP BY tmp.customer_id,tmp.date ) tmp2 ON tmp1.id = tmp2.customer_id AND tmp1.date = tmp2.date");

	 IF summary = 0 THEN
			SET @SQLText = CONCAT(query,' ','ORDER BY tmp1.full_name,tmp1.date');
			 -- SELECT @SQLText;
			 PREPARE stmt FROM @SQLText;
	 ELSE
			SET summaryQuery = "SELECT tmp3.date as date,SUM(tmp3.status_complete) as total";

			SET @SQLText = CONCAT(summaryQuery," FROM(", query,") tmp3 GROUP BY tmp3.date ORDER BY STR_TO_DATE(tmp3.date,'%d-%m-%Y')");
			-- SELECT @SQLText;
			PREPARE stmt FROM @SQLText;
	 END IF;

   EXECUTE stmt;
   DEALLOCATE PREPARE stmt;
END
;


-- ----------------------------
-- Procedure structure for proc_report_by_turn
-- ----------------------------
DROP PROCEDURE IF EXISTS `proc_report_by_turn`;

CREATE PROCEDURE `proc_report_by_turn`(IN summary BOOLEAN,IN from_date TEXT,IN to_date TEXT,IN partner_ids TEXT)
BEGIN

    DECLARE query TEXT;
	DECLARE summaryQuery TEXT;

	SET query = "SELECT tmp1.id as entity_id, tmp1.full_name as entity_name,DATE_FORMAT(tmp1.date,'%d-%m-%Y') as date,tmp2.total";

	SET query = CONCAT(query," ","FROM (SELECT c.id,c.full_name,cal.date FROM ((SELECT cu.id,CONCAT(cu.customer_code,'|||',cu.full_name) full_name FROM customer cu) UNION (SELECT 0 as id ,'*Chưa gán khách hàng' as full_name)) c");

	SET query = CONCAT(query," ","CROSS JOIN (
										SELECT selected_date as date FROM (
													SELECT adddate('1970-01-01',t4.i*10000 + t3.i*1000 + t2.i*100 + t1.i*10 + t0.i) selected_date
													FROM
														 (SELECT 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t0,
														 (SELECT 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t1,
														 (SELECT 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t2,
														 (SELECT 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t3,
														 (SELECT 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t4
										) d
										WHERE selected_date BETWEEN '",from_date,"' AND '",to_date,"'
										) cal");
	SET query = CONCAT(query," ",") tmp1 LEFT JOIN (SELECT tmp.date,tmp.customer_id, COUNT(*) as total");

	SET query = CONCAT(query," "," FROM (SELECT (CASE WHEN o.status = 5 AND o.ETA_date_reality IS NOT NULL THEN o.ETA_date_reality ELSE o.ETA_date END) date
																				,(CASE WHEN (o.customer_id IS NULL OR o.customer_id = 0 ) THEN 0 ELSE o.customer_id END) as customer_id
																				,o.id,o.partner_id FROM orders o ");


	SET query = CONCAT(query," ","WHERE o.del_flag = 0 AND o.status != 1 AND CASE WHEN o.ETA_date_reality IS NOT NULL AND o.status = 5
									THEN o.ETA_date_reality BETWEEN '",from_date,"' AND '",to_date,"'
									ELSE o.ETA_date BETWEEN '",from_date,"' AND '",to_date,"' END");

	IF partner_ids IS NOT NULL AND partner_ids != "" THEN
		SET query = CONCAT(query," ","AND o.partner_id IN (",partner_ids,")");
	END IF;

	SET query = CONCAT(query," ",") tmp GROUP BY tmp.customer_id,tmp.date ) tmp2 ON tmp1.date = tmp2.date AND tmp1.id = tmp2.customer_id");

	 IF summary = 0 THEN
			SET @SQLText = CONCAT(query,' ','ORDER BY tmp1.full_name,tmp1.date');
		 -- SELECT @SQLText;
			PREPARE stmt FROM @SQLText;
	 ELSE
			SET summaryQuery = "SELECT tmp3.date as date, SUM(tmp3.total) as total";

			SET @SQLText = CONCAT(summaryQuery," FROM(", query,") tmp3 GROUP BY tmp3.date ORDER BY STR_TO_DATE(tmp3.date,'%d-%m-%Y')");
		 -- SELECT @SQLText;
			PREPARE stmt FROM @SQLText;
	 END IF;

   EXECUTE stmt;
   DEALLOCATE PREPARE stmt;
END
;


-- ----------------------------
-- Procedure structure for proc_report_customer_by_cost
-- ----------------------------
DROP PROCEDURE IF EXISTS `proc_report_customer_by_cost`;

CREATE PROCEDURE `proc_report_customer_by_cost`(IN summary BOOLEAN,
	IN vehicle_team_ids TEXT,IN vehicle_ids TEXT,IN driver_ids TEXT,
	IN customer_ids TEXT,IN from_date TEXT,IN to_date TEXT,IN dayCondition TEXT,IN partner_ids TEXT)
BEGIN

	DECLARE query TEXT;
	DECLARE summaryQuery TEXT;

	SET query = "SELECT tmp1.id as entity_id, tmp1.full_name as entity_name, DATE_FORMAT(tmp1.date, '%d-%m-%Y') as date, tmp2.cost as status_complete";
	SET query = CONCAT(query," ","FROM (SELECT c.id,c.full_name,cal.date,c.del_flag FROM ((SELECT cu.id,cu.full_name,cu.del_flag FROM customer cu) UNION (SELECT 0 as id ,'*Chưa gán khách hàng' as full_name,0 as del_flag)) c");

	SET query = CONCAT(query," ","CROSS JOIN (
										SELECT selected_date as date FROM (
													SELECT adddate('1970-01-01',t4.i*10000 + t3.i*1000 + t2.i*100 + t1.i*10 + t0.i) selected_date
													FROM
														 (SELECT 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t0,
														 (SELECT 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t1,
														 (SELECT 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t2,
														 (SELECT 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t3,
														 (SELECT 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t4
										) d
										WHERE selected_date BETWEEN '",from_date,"' AND '",to_date,"'
										) cal WHERE 1=1");

	IF customer_ids IS NOT NULL AND customer_ids != "" THEN
		SET query = CONCAT(query," ","AND c.id IN (",customer_ids,")");
	END IF;

	SET query = CONCAT(query," ",") tmp1 LEFT JOIN (SELECT tmp.date,tmp.customer_id, SUM(tmp.final_cost) as cost");

	SET query = CONCAT(query," ","FROM (SELECT DISTINCT r.id,
																(CASE WHEN (a.customer_id IS NULL OR a.customer_id = 0 ) THEN 0 ELSE a.customer_id END) as customer_id,
																r.final_cost");
	IF dayCondition = 1 THEN
		SET query = CONCAT(query," ",",r.ETD_date as date");
	ELSEIF dayCondition = 2 THEN
		SET query = CONCAT(query," ",",r.ETD_date_reality as date");
	ELSEIF dayCondition = 3 THEN
		SET query = CONCAT(query," ",",r.ETA_date as date");
	ELSE
		SET query = CONCAT(query," ",",r.ETA_date_reality as date");
	END IF;

	SET query = CONCAT(query," ","FROM routes r
																LEFT JOIN ( SELECT DISTINCT ro.route_id, o.customer_id FROM route_order ro
																						INNER JOIN orders o ON o.id = ro.order_id WHERE ro.del_flag = 0 AND o.del_flag = 0
																					) a ON a.route_id = r.id
																LEFT JOIN driver_vehicle_team dvt ON dvt.driver_id = r.driver_id
																WHERE r.del_flag = 0 AND r.is_approved = 1 AND r.route_status IN (1,0)");
	IF dayCondition = 1 THEN
		SET query = CONCAT(query," "," AND r.ETD_date IS NOT NULL AND r.ETD_date BETWEEN '",from_date,"' AND '",to_date,"' ");
	ELSEIF dayCondition = 2 THEN
		SET query = CONCAT(query," "," AND r.ETD_date_reality IS NOT NULL AND r.ETD_date_reality BETWEEN '",from_date,"' AND '",to_date,"' ");
	ELSEIF dayCondition = 3 THEN
		SET query = CONCAT(query," "," AND r.ETA_date IS NOT NULL AND r.ETA_date BETWEEN '",from_date,"' AND '",to_date,"' ");
	ELSE
		SET query = CONCAT(query," "," AND r.ETA_date_reality IS NOT NULL AND r.ETA_date_reality BETWEEN '",from_date,"' AND '",to_date,"' ");
	END IF;

	IF vehicle_team_ids IS NOT NULL AND vehicle_team_ids != "" THEN
		SET query = CONCAT(query," ","AND dvt.vehicle_team_id IN (",vehicle_team_ids,")");
	END IF;
	IF driver_ids IS NOT NULL AND driver_ids != "" THEN
		SET query = CONCAT(query," ","AND r.driver_id IN (",driver_ids,")");
	END IF;
	IF vehicle_ids IS NOT NULL AND vehicle_ids != "" THEN
		SET query = CONCAT(query," ","AND r.vehicle_id IN (",vehicle_ids,")");
	END IF;
	IF customer_ids IS NOT NULL AND customer_ids != "" THEN
		SET query = CONCAT(query," ","AND a.customer_id IN (",customer_ids,")");
	END IF;
		IF partner_ids IS NOT NULL AND partner_ids != "" THEN
		SET query = CONCAT(query," ","AND r.partner_id IN (",partner_ids,")");
	END IF;

	SET query = CONCAT(query," ",") tmp GROUP BY tmp.customer_id, DATE_FORMAT(tmp.date,'%Y-%m-%d')
																) tmp2 ON tmp1.id = tmp2.customer_id AND tmp1.date = tmp2.date ");

	SET query = CONCAT(query," ","WHERE tmp1.del_flag = 0 OR (tmp2.cost IS NOT NULL AND tmp2.cost != 0 )");

	 IF summary = 0 THEN
			SET @SQLText = CONCAT(query,' ','ORDER BY tmp1.full_name,tmp1.date');
			-- SELECT @SQLText;
			PREPARE stmt FROM @SQLText;
	 ELSE
			SET summaryQuery = "SELECT tmp3.date as date, SUM(tmp3.status_complete) as total";
			SET @SQLText = CONCAT(summaryQuery," FROM(", query,") tmp3 GROUP BY tmp3.date ORDER BY STR_TO_DATE(tmp3.date,'%d-%m-%Y')");
			PREPARE stmt FROM @SQLText;
	 END IF;

   EXECUTE stmt;
   DEALLOCATE PREPARE stmt;
END
;


-- ----------------------------
-- Procedure structure for proc_report_customer_by_cost_monthly
-- ----------------------------
DROP PROCEDURE IF EXISTS `proc_report_customer_by_cost_monthly`;

CREATE PROCEDURE `proc_report_customer_by_cost_monthly`(IN summary BOOLEAN,
	IN vehicle_team_ids TEXT,IN vehicle_ids TEXT,IN driver_ids TEXT,
	IN customer_ids TEXT,IN from_date TEXT,IN to_date TEXT,IN dayCondition TEXT,IN partner_ids TEXT)
BEGIN

DECLARE query TEXT;
	DECLARE summaryQuery TEXT;

	SET query = "SELECT tmp1.id as entity_id, tmp1.full_name as entity_name, DATE_FORMAT(STR_TO_DATE(tmp1.date,'%Y-%m'),'%m-%Y') as date , tmp2.cost as status_complete";
	SET query = CONCAT(query," ","FROM (SELECT c.id,c.full_name,cal.date,c.del_flag FROM ((SELECT cu.id,cu.full_name,cu.del_flag FROM customer cu) UNION (SELECT 0 as id ,'*Chưa gán khách hàng' as full_name,0 as del_flag)) c");

	SET query = CONCAT(query," ","CROSS JOIN (select DATE_FORMAT(aDate,'%Y-%m') as date from (select @maxDate - interval (a.a + (10 * b.a) + (100 * c.a)) month as aDate from
																						(select 0 as a union all select 1 union all select 2 union all select 3 union all select 4 union all select 5 union all select 6 union all select 7
																						union all select 8 union all select 9) a,
																						(select 0 as a union all select 1 union all select 2 union all select 3 union all select 4 union all select 5 union all select 6 union all select 7
																						union all select 8 union all select 9) b,
																						(select 0 as a union all select 1 union all select 2 union all select 3 union all select 4 union all select 5 union all select 6 union all select 7
																						union all select 8 union all select 9) c,
																						(select @minDate := '",from_date,"', @maxDate := '",to_date,"') d) e
																						where DATE_FORMAT(aDate,'%Y-%m') BETWEEN DATE_FORMAT(@minDate, '%Y-%m') AND DATE_FORMAT(@maxDate, '%Y-%m')) cal WHERE 1=1");

	IF customer_ids IS NOT NULL AND customer_ids != "" THEN
		SET query = CONCAT(query," ","AND c.id IN (",customer_ids,")");
	END IF;

		SET query = CONCAT(query," ",") tmp1 LEFT JOIN (SELECT DATE_FORMAT(tmp.date,'%Y-%m') as date,tmp.customer_id, SUM(tmp.final_cost) as cost");

		SET query = CONCAT(query," ","FROM (SELECT DISTINCT r.id,
																	(CASE WHEN (a.customer_id IS NULL OR a.customer_id = 0 ) THEN 0 ELSE a.customer_id END) as customer_id,
																	r.final_cost");
		IF dayCondition = 1 THEN
			SET query = CONCAT(query," ",",r.ETD_date as date");
		ELSEIF dayCondition = 2 THEN
			SET query = CONCAT(query," ",",r.ETD_date_reality as date");
		ELSEIF dayCondition = 3 THEN
			SET query = CONCAT(query," ",",r.ETA_date as date");
		ELSE
			SET query = CONCAT(query," ",",r.ETA_date_reality as date");
		END IF;

		SET query = CONCAT(query," ","FROM routes r
																	LEFT JOIN ( SELECT DISTINCT ro.route_id, o.customer_id FROM route_order ro
																							INNER JOIN orders o ON o.id = ro.order_id WHERE ro.del_flag = 0 AND o.del_flag = 0
																						) a ON a.route_id = r.id
																	LEFT JOIN driver_vehicle_team dvt ON dvt.driver_id = r.driver_id
																	WHERE r.del_flag = 0 AND r.is_approved = 1 AND r.route_status IN (1,0)");
		IF dayCondition = 1 THEN
			SET query = CONCAT(query," "," AND r.ETD_date IS NOT NULL AND DATE_FORMAT(r.ETD_date,'%Y-%m') BETWEEN DATE_FORMAT('",from_date,"', '%Y-%m') AND DATE_FORMAT('",to_date,"', '%Y-%m')");
		ELSEIF dayCondition = 2 THEN
			SET query = CONCAT(query," "," AND r.ETD_date_reality IS NOT NULL AND DATE_FORMAT(r.ETD_date_reality,'%Y-%m') BETWEEN DATE_FORMAT('",from_date,"', '%Y-%m') AND DATE_FORMAT('",to_date,"', '%Y-%m')");
		ELSEIF dayCondition = 3 THEN
			SET query = CONCAT(query," "," AND r.ETA_date IS NOT NULL AND DATE_FORMAT(r.ETA_date,'%Y-%m') BETWEEN DATE_FORMAT('",from_date,"', '%Y-%m') AND DATE_FORMAT('",to_date,"', '%Y-%m')");
		ELSE
			SET query = CONCAT(query," "," AND r.ETA_date_reality IS NOT NULL AND DATE_FORMAT(r.ETA_date_reality,'%Y-%m') BETWEEN DATE_FORMAT('",from_date,"', '%Y-%m') AND DATE_FORMAT('",to_date,"', '%Y-%m')");
		END IF;

		IF vehicle_team_ids IS NOT NULL AND vehicle_team_ids != "" THEN
			SET query = CONCAT(query," ","AND dvt.vehicle_team_id IN (",vehicle_team_ids,")");
		END IF;
		IF driver_ids IS NOT NULL AND driver_ids != "" THEN
			SET query = CONCAT(query," ","AND r.driver_id IN (",driver_ids,")");
		END IF;
		IF vehicle_ids IS NOT NULL AND vehicle_ids != "" THEN
			SET query = CONCAT(query," ","AND r.vehicle_id IN (",vehicle_ids,")");
		END IF;
		IF customer_ids IS NOT NULL AND customer_ids != "" THEN
			SET query = CONCAT(query," ","AND a.customer_id IN (",customer_ids,")");
		END IF;
		IF partner_ids IS NOT NULL AND partner_ids != "" THEN
		SET query = CONCAT(query," ","AND r.partner_id IN (",partner_ids,")");
		END IF;

		SET query = CONCAT(query," ",") tmp GROUP BY tmp.customer_id, DATE_FORMAT(tmp.date,'%Y-%m')
																	) tmp2 ON tmp1.id = tmp2.customer_id AND tmp1.date = tmp2.date ");

		SET query = CONCAT(query," ","WHERE tmp1.del_flag = 0 OR (tmp2.cost IS NOT NULL AND tmp2.cost != 0 )");

	 IF summary = 0 THEN
			SET @SQLText = CONCAT(query,' ','ORDER BY tmp1.full_name,tmp1.date');
			PREPARE stmt FROM @SQLText;
	 ELSE
			SET summaryQuery = "SELECT tmp3.date as date, SUM(tmp3.status_complete) as total";
			SET @SQLText = CONCAT(summaryQuery," FROM(", query,") tmp3 GROUP BY tmp3.date ORDER BY STR_TO_DATE(tmp3.date,'%m-%Y')");
			PREPARE stmt FROM @SQLText;
	 END IF;

   EXECUTE stmt;
   DEALLOCATE PREPARE stmt;
END
;


-- ----------------------------
-- Procedure structure for proc_report_customer_by_income
-- ----------------------------
DROP PROCEDURE IF EXISTS `proc_report_customer_by_income`;

CREATE PROCEDURE `proc_report_customer_by_income`(IN summary BOOLEAN,IN vehicle_team_ids TEXT,
	IN vehicle_ids TEXT,IN driver_ids TEXT,IN customer_ids TEXT,IN from_date TEXT,IN to_date TEXT,IN dayCondition TEXT,IN partner_ids TEXT)
BEGIN

	DECLARE query TEXT;
	DECLARE summaryQuery TEXT;

	SET query = "SELECT tmp1.id as entity_id, tmp1.full_name as entity_name,DATE_FORMAT(tmp1.date,'%d-%m-%Y') as date,tmp2.status_complete";

	SET query = CONCAT(query," ","FROM (SELECT c.id,c.full_name,cal.date,c.del_flag FROM ((SELECT cu.id,cu.full_name,cu.del_flag FROM customer cu) UNION (SELECT 0 as id ,'*Chưa gán khách hàng' as full_name,0 as del_flag)) c");
	SET query = CONCAT(query," ","CROSS JOIN (
										SELECT selected_date as date FROM (
													SELECT adddate('1970-01-01',t4.i*10000 + t3.i*1000 + t2.i*100 + t1.i*10 + t0.i) selected_date
													FROM
														 (SELECT 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t0,
														 (SELECT 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t1,
														 (SELECT 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t2,
														 (SELECT 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t3,
														 (SELECT 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t4
										) d
										WHERE selected_date BETWEEN '",from_date,"' AND '",to_date,"'
										) cal
										WHERE 1=1 ");

	IF customer_ids IS NOT NULL AND customer_ids != "" THEN
		SET query = CONCAT(query," ","AND c.id IN (",customer_ids,")");
	END IF;
	SET query = CONCAT(query," ",") tmp1 LEFT JOIN (SELECT tmp.date,tmp.customer_id,SUM(status_complete) as status_complete");

	SET query = CONCAT(query," ","FROM (SELECT DISTINCT o.id,
																(CASE WHEN (o.customer_id IS NULL OR o.customer_id = 0 ) THEN 0 ELSE o.customer_id END) as customer_id,
																o.amount as status_complete ,");

	IF dayCondition = 1 THEN
		SET query = CONCAT(query," "," o.ETD_date as date");
	ELSEIF dayCondition = 2 THEN
		SET query = CONCAT(query," "," o.ETD_date_reality as date");
	ELSEIF dayCondition = 3 THEN
		SET query = CONCAT(query," "," o.ETA_date as date");
	ELSE
		SET query = CONCAT(query," "," o.ETA_date_reality as date");
	END IF;

	SET query = CONCAT(query," ","FROM orders o ");

	IF vehicle_team_ids IS NOT NULL AND vehicle_team_ids != "" THEN
		SET query = CONCAT(query," ","INNER JOIN driver_vehicle_team dvt ON dvt.driver_id = o.primary_driver_id AND dvt.vehicle_team_id IN (",vehicle_team_ids,")");
	END IF;

	SET query = CONCAT(query," ","WHERE o.del_flag = 0");

	IF dayCondition = 1 THEN
		SET query = CONCAT(query," "," AND o.status IN(2,3,4,5,7) AND o.ETD_date BETWEEN '",from_date,"' AND '",to_date,"' ");
	ELSEIF dayCondition = 2 THEN
		SET query = CONCAT(query," "," AND o.status IN (4,5) AND o.ETD_date_reality BETWEEN '",from_date,"' AND '",to_date,"' ");
	ELSEIF dayCondition = 3 THEN
		SET query = CONCAT(query," "," AND o.status IN(2,3,4,5,7) AND o.ETA_date BETWEEN '",from_date,"' AND '",to_date,"' ");
	ELSE
		SET query = CONCAT(query," "," AND o.status = 5 AND o.ETA_date_reality BETWEEN '",from_date,"' AND '",to_date,"' ");
	END IF;

	IF driver_ids IS NOT NULL AND driver_ids != "" THEN
		SET query = CONCAT(query," ","AND o.primary_driver_id IN (",driver_ids,")");
	END IF;
	IF vehicle_ids IS NOT NULL AND vehicle_ids != "" THEN
		SET query = CONCAT(query," ","AND o.vehicle_id IN (",vehicle_ids,")");
	END IF;
	IF customer_ids IS NOT NULL AND customer_ids != "" THEN
		SET query = CONCAT(query," ","AND o.customer_id IN (",customer_ids,")");
	END IF;
	IF partner_ids IS NOT NULL AND partner_ids != "" THEN
		SET query = CONCAT(query," ","AND o.partner_id IN (",partner_ids,")");
	END IF;

	SET query = CONCAT(query," ",") tmp GROUP BY tmp.customer_id,tmp.date ) tmp2 ON tmp1.id = tmp2.customer_id AND tmp1.date = tmp2.date");

	SET query = CONCAT(query," ","WHERE tmp1.del_flag = 0 OR (tmp2.status_complete IS NOT NULL AND tmp2.status_complete != 0 )");

	 IF summary = 0 THEN
			SET @SQLText = CONCAT(query,' ','ORDER BY tmp1.full_name,tmp1.date');
			-- SELECT @SQLText;
			PREPARE stmt FROM @SQLText;
	 ELSE
			SET summaryQuery = "SELECT tmp3.date as date,SUM(tmp3.status_complete) as total";

			SET @SQLText = CONCAT(summaryQuery," FROM(", query,") tmp3 GROUP BY tmp3.date ORDER BY STR_TO_DATE(tmp3.date,'%d-%m-%Y')");
			PREPARE stmt FROM @SQLText;
	 END IF;

   EXECUTE stmt;
   DEALLOCATE PREPARE stmt;
END
;


-- ----------------------------
-- Procedure structure for proc_report_customer_by_income_monthly
-- ----------------------------
DROP PROCEDURE IF EXISTS `proc_report_customer_by_income_monthly`;

CREATE PROCEDURE `proc_report_customer_by_income_monthly`(IN summary BOOLEAN,IN vehicle_team_ids TEXT,
	IN vehicle_ids TEXT,IN driver_ids TEXT,IN customer_ids TEXT,IN from_date TEXT,IN to_date TEXT,IN dayCondition TEXT,IN partner_ids TEXT)
BEGIN

	DECLARE query TEXT;
	DECLARE summaryQuery TEXT;

	SET query = "SELECT tmp1.id as entity_id, tmp1.full_name as entity_name,DATE_FORMAT(STR_TO_DATE(tmp1.date,'%Y-%m'),'%m-%Y') as date,tmp2.status_complete";

	SET query = CONCAT(query," ","FROM (SELECT c.id,c.full_name,cal.date,c.del_flag FROM ((SELECT cu.id,cu.full_name,cu.del_flag FROM customer cu) UNION (SELECT 0 as id ,'*Chưa gán khách hàng' as full_name,0 as del_flag)) c");

	SET query = CONCAT(query," ","CROSS JOIN (select DATE_FORMAT(aDate,'%Y-%m') as date from (select @maxDate - interval (a.a + (10 * b.a) + (100 * c.a)) month as aDate from
																						(select 0 as a union all select 1 union all select 2 union all select 3 union all select 4 union all select 5 union all select 6 union all select 7
																						union all select 8 union all select 9) a,
																						(select 0 as a union all select 1 union all select 2 union all select 3 union all select 4 union all select 5 union all select 6 union all select 7
																						union all select 8 union all select 9) b,
																						(select 0 as a union all select 1 union all select 2 union all select 3 union all select 4 union all select 5 union all select 6 union all select 7
																						union all select 8 union all select 9) c,
																						(select @minDate := '",from_date,"', @maxDate := '",to_date,"') d) e
																						where DATE_FORMAT(aDate,'%Y-%m') BETWEEN DATE_FORMAT(@minDate, '%Y-%m') AND DATE_FORMAT(@maxDate, '%Y-%m')) cal
																						WHERE 1=1");

	IF customer_ids IS NOT NULL AND customer_ids != "" THEN
		SET query = CONCAT(query," ","AND c.id IN (",customer_ids,")");
	END IF;
	SET query = CONCAT(query," ",") tmp1 LEFT JOIN (SELECT tmp.date,tmp.customer_id,SUM(status_complete) as status_complete");

	SET query = CONCAT(query," ","FROM (SELECT DISTINCT o.id,
																						(CASE WHEN (o.customer_id IS NULL OR o.customer_id = 0 ) THEN 0 ELSE o.customer_id END) as customer_id,
																						o.amount as status_complete , ");
	IF dayCondition = 1 THEN
		SET query = CONCAT(query," "," DATE_FORMAT(o.ETD_date,'%Y-%m') as date");
	ELSEIF dayCondition = 2 THEN
		SET query = CONCAT(query," "," DATE_FORMAT(o.ETD_date_reality,'%Y-%m') as date");
	ELSEIF dayCondition = 3 THEN
		SET query = CONCAT(query," "," DATE_FORMAT(o.ETA_date,'%Y-%m') as date");
	ELSE
		SET query = CONCAT(query," "," DATE_FORMAT(o.ETA_date_reality,'%Y-%m') as date");
	END IF;

	SET query = CONCAT(query," ","FROM orders o ");

	IF vehicle_team_ids IS NOT NULL AND vehicle_team_ids != "" THEN
		SET query = CONCAT(query," ","INNER JOIN driver_vehicle_team dvt ON dvt.driver_id = o.primary_driver_id AND dvt.vehicle_team_id IN (",vehicle_team_ids,")");
	END IF;

	SET query = CONCAT(query," ","WHERE o.del_flag = 0 ");

	IF dayCondition = 1 THEN
		SET query = CONCAT(query," "," AND o.status IN(2,3,4,5,7) AND DATE_FORMAT(o.ETD_date,'%Y-%m') BETWEEN DATE_FORMAT('",from_date,"','%Y-%m') AND DATE_FORMAT('",to_date,"','%Y-%m') ");
	ELSEIF dayCondition = 2 THEN
		SET query = CONCAT(query," "," AND o.status IN (4,5) AND DATE_FORMAT(o.ETD_date_reality,'%Y-%m') BETWEEN DATE_FORMAT('",from_date,"','%Y-%m') AND DATE_FORMAT('",to_date,"','%Y-%m') ");
	ELSEIF dayCondition = 3 THEN
		SET query = CONCAT(query," "," AND o.status IN(2,3,4,5,7) AND DATE_FORMAT(o.ETA_date,'%Y-%m') BETWEEN DATE_FORMAT('",from_date,"','%Y-%m') AND DATE_FORMAT('",to_date,"','%Y-%m') ");
	ELSE
		SET query = CONCAT(query," "," AND o.status = 5 AND DATE_FORMAT(o.ETA_date_reality,'%Y-%m') BETWEEN DATE_FORMAT('",from_date,"','%Y-%m') AND DATE_FORMAT('",to_date,"','%Y-%m') ");
	END IF;

	IF driver_ids IS NOT NULL AND driver_ids != "" THEN
		SET query = CONCAT(query," ","AND o.primary_driver_id IN (",driver_ids,")");
	END IF;
	IF vehicle_ids IS NOT NULL AND vehicle_ids != "" THEN
		SET query = CONCAT(query," ","AND o.vehicle_id IN (",vehicle_ids,")");
	END IF;
	IF customer_ids IS NOT NULL AND customer_ids != "" THEN
		SET query = CONCAT(query," ","AND o.customer_id IN (",customer_ids,")");
	END IF;
	IF partner_ids IS NOT NULL AND partner_ids != "" THEN
		SET query = CONCAT(query," ","AND o.partner_id IN (",partner_ids,")");
	END IF;

	SET query = CONCAT(query," ",") tmp GROUP BY tmp.customer_id,tmp.date ) tmp2 ON tmp1.id = tmp2.customer_id AND tmp1.date = tmp2.date");

	SET query = CONCAT(query," ","WHERE tmp1.del_flag = 0 OR (tmp2.status_complete IS NOT NULL AND tmp2.status_complete != 0 )");

	 IF summary = 0 THEN
			SET @SQLText = CONCAT(query,' ','ORDER BY tmp1.full_name,tmp1.date');
			-- SELECT @SQLText;
		  PREPARE stmt FROM @SQLText;
	 ELSE
			SET summaryQuery = "SELECT tmp3.date as date,SUM(tmp3.status_complete) as total";

			SET @SQLText = CONCAT(summaryQuery," FROM(", query,") tmp3 GROUP BY tmp3.date ORDER BY STR_TO_DATE(tmp3.date,'%m-%Y')");
			PREPARE stmt FROM @SQLText;
	 END IF;

   EXECUTE stmt;
   DEALLOCATE PREPARE stmt;
END
;


-- ----------------------------
-- Procedure structure for proc_report_customer_by_profit
-- ----------------------------
DROP PROCEDURE IF EXISTS `proc_report_customer_by_profit`;

CREATE PROCEDURE `proc_report_customer_by_profit`(IN summary BOOLEAN,IN vehicle_team_ids TEXT,
	IN vehicle_ids TEXT,IN driver_ids TEXT,IN customer_ids TEXT,IN from_date TEXT,IN to_date TEXT,IN dayCondition TEXT,IN partner_ids TEXT)
BEGIN

	DECLARE query TEXT;
	DECLARE summaryQuery TEXT;

	SET query = "SELECT tmp1.id as entity_id, tmp1.full_name as entity_name,DATE_FORMAT(tmp1.date,'%d-%m-%Y') as date,
								((CASE WHEN tmp2.income IS NULL THEN 0 ELSE tmp2.income END) - (CASE WHEN tmp3.cost IS NULL THEN 0 ELSE tmp3.cost END)) as status_complete";

SET query = CONCAT(query," ","FROM (SELECT c.id,c.full_name,cal.date,c.del_flag FROM ((SELECT cu.id,cu.full_name,cu.del_flag FROM customer cu WHERE cu.del_flag = 0) UNION (SELECT 0 as id ,'*Chưa gán khách hàng' as full_name,0 as del_flag)) c");
	SET query = CONCAT(query," ","CROSS JOIN (
										SELECT selected_date as date FROM (
													SELECT adddate('1970-01-01',t4.i*10000 + t3.i*1000 + t2.i*100 + t1.i*10 + t0.i) selected_date
													FROM
														 (SELECT 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t0,
														 (SELECT 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t1,
														 (SELECT 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t2,
														 (SELECT 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t3,
														 (SELECT 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t4
										) d
										WHERE selected_date BETWEEN '",from_date,"' AND '",to_date,"'
										) cal
										WHERE 1=1 ");

	IF customer_ids IS NOT NULL AND customer_ids != "" THEN
		SET query = CONCAT(query," ","AND c.id IN (",customer_ids,")");
	END IF;
	SET query = CONCAT(query," ",") tmp1 LEFT JOIN (SELECT tmp.date,tmp.customer_id,SUM(status_complete) as income");

	SET query = CONCAT(query," ","FROM (SELECT DISTINCT o.id,
																(CASE WHEN (o.customer_id IS NULL OR o.customer_id = 0 ) THEN 0 ELSE o.customer_id END) as customer_id,
																o.amount as status_complete ,");

	IF dayCondition = 1 THEN
		SET query = CONCAT(query," "," o.ETD_date as date");
	ELSEIF dayCondition = 2 THEN
		SET query = CONCAT(query," "," o.ETD_date_reality as date");
	ELSEIF dayCondition = 3 THEN
		SET query = CONCAT(query," "," o.ETA_date as date");
	ELSE
		SET query = CONCAT(query," "," o.ETA_date_reality as date");
	END IF;

	SET query = CONCAT(query," ","FROM orders o ");

	IF vehicle_team_ids IS NOT NULL AND vehicle_team_ids != "" THEN
		SET query = CONCAT(query," ","INNER JOIN driver_vehicle_team dvt ON dvt.driver_id = o.primary_driver_id AND dvt.vehicle_team_id IN (",vehicle_team_ids,")");
	END IF;

	SET query = CONCAT(query," ","WHERE o.del_flag = 0");

	IF dayCondition = 1 THEN
		SET query = CONCAT(query," "," AND o.status IN(2,3,4,5,7) AND o.ETD_date BETWEEN '",from_date,"' AND '",to_date,"' ");
	ELSEIF dayCondition = 2 THEN
		SET query = CONCAT(query," "," AND o.status IN (4,5) AND o.ETD_date_reality BETWEEN '",from_date,"' AND '",to_date,"' ");
	ELSEIF dayCondition = 3 THEN
		SET query = CONCAT(query," "," AND o.status IN(2,3,4,5,7) AND o.ETA_date BETWEEN '",from_date,"' AND '",to_date,"' ");
	ELSE
		SET query = CONCAT(query," "," AND o.status = 5 AND o.ETA_date_reality BETWEEN '",from_date,"' AND '",to_date,"' ");
	END IF;
	IF partner_ids IS NOT NULL AND partner_ids != "" THEN
		SET query = CONCAT(query," ","AND o.partner_id IN (",partner_ids,")");
	END IF;

	IF driver_ids IS NOT NULL AND driver_ids != "" THEN
		SET query = CONCAT(query," ","AND o.primary_driver_id IN (",driver_ids,")");
	END IF;
	IF vehicle_ids IS NOT NULL AND vehicle_ids != "" THEN
		SET query = CONCAT(query," ","AND o.vehicle_id IN (",vehicle_ids,")");
	END IF;
	IF customer_ids IS NOT NULL AND customer_ids != "" THEN
		SET query = CONCAT(query," ","AND o.customer_id IN (",customer_ids,")");
	END IF;

	SET query = CONCAT(query," ",") tmp GROUP BY tmp.customer_id,tmp.date ) tmp2 ON tmp1.id = tmp2.customer_id AND tmp1.date = tmp2.date");


	SET query = CONCAT(query," ","LEFT JOIN (SELECT tmp.date, tmp.customer_id, SUM(tmp.final_cost) as cost");

	SET query = CONCAT(query," ","FROM (SELECT DISTINCT r.id,
																(CASE WHEN (a.customer_id IS NULL OR a.customer_id = 0 ) THEN 0 ELSE a.customer_id END) as customer_id,
																r.final_cost");
	IF dayCondition = 1 THEN
		SET query = CONCAT(query," ",",r.ETD_date as date");
	ELSEIF dayCondition = 2 THEN
		SET query = CONCAT(query," ",",r.ETD_date_reality as date");
	ELSEIF dayCondition = 3 THEN
		SET query = CONCAT(query," ",",r.ETA_date as date");
	ELSE
		SET query = CONCAT(query," ",",r.ETA_date_reality as date");
	END IF;

	SET query = CONCAT(query," ","FROM routes r
																LEFT JOIN ( SELECT DISTINCT ro.route_id, o.customer_id FROM route_order ro
																						INNER JOIN orders o ON o.id = ro.order_id WHERE ro.del_flag = 0 AND o.del_flag = 0
																					) a ON a.route_id = r.id
																LEFT JOIN driver_vehicle_team dvt ON dvt.driver_id = r.driver_id
																WHERE r.del_flag = 0 AND r.is_approved = 1 AND r.route_status IN (1,0)");
	IF dayCondition = 1 THEN
		SET query = CONCAT(query," "," AND r.ETD_date IS NOT NULL AND r.ETD_date BETWEEN '",from_date,"' AND '",to_date,"' ");
	ELSEIF dayCondition = 2 THEN
		SET query = CONCAT(query," "," AND r.ETD_date_reality IS NOT NULL AND r.ETD_date_reality BETWEEN '",from_date,"' AND '",to_date,"' ");
	ELSEIF dayCondition = 3 THEN
		SET query = CONCAT(query," "," AND r.ETA_date IS NOT NULL AND r.ETA_date BETWEEN '",from_date,"' AND '",to_date,"' ");
	ELSE
		SET query = CONCAT(query," "," AND r.ETA_date_reality IS NOT NULL AND r.ETA_date_reality BETWEEN '",from_date,"' AND '",to_date,"' ");
	END IF;

	IF vehicle_team_ids IS NOT NULL AND vehicle_team_ids != "" THEN
		SET query = CONCAT(query," ","AND dvt.vehicle_team_id IN (",vehicle_team_ids,")");
	END IF;
	IF driver_ids IS NOT NULL AND driver_ids != "" THEN
		SET query = CONCAT(query," ","AND r.driver_id IN (",driver_ids,")");
	END IF;
	IF vehicle_ids IS NOT NULL AND vehicle_ids != "" THEN
		SET query = CONCAT(query," ","AND r.vehicle_id IN (",vehicle_ids,")");
	END IF;
	IF customer_ids IS NOT NULL AND customer_ids != "" THEN
		SET query = CONCAT(query," ","AND a.customer_id IN (",customer_ids,")");
	END IF;
	IF partner_ids IS NOT NULL AND partner_ids != "" THEN
		SET query = CONCAT(query," ","AND r.partner_id IN (",partner_ids,")");
	END IF;

	SET query = CONCAT(query," ",") tmp GROUP BY tmp.customer_id, DATE_FORMAT(tmp.date,'%Y-%m-%d')
																) tmp3 ON tmp1.id = tmp3.customer_id AND tmp1.date = tmp3.date ");

	SET query = CONCAT(query," ","WHERE tmp1.del_flag = 0 OR ((CASE WHEN tmp2.income IS NULL THEN 0 ELSE tmp2.income END) - (CASE WHEN tmp3.cost IS NULL THEN 0 ELSE tmp3.cost END)) !=0 ");

	 IF summary = 0 THEN
			SET @SQLText = CONCAT(query,' ','ORDER BY tmp1.full_name,tmp1.date');
			-- SELECT @SQLText;
			PREPARE stmt FROM @SQLText;
	 ELSE
			SET summaryQuery = "SELECT tmp4.date as date,SUM(tmp4.status_complete) as total";

			SET @SQLText = CONCAT(summaryQuery," FROM(", query,") tmp4 GROUP BY tmp4.date ORDER BY STR_TO_DATE(tmp4.date,'%d-%m-%Y')");
			PREPARE stmt FROM @SQLText;
	 END IF;

   EXECUTE stmt;
   DEALLOCATE PREPARE stmt;
END
;


-- ----------------------------
-- Procedure structure for proc_report_customer_by_profit_monthly
-- ----------------------------
DROP PROCEDURE IF EXISTS `proc_report_customer_by_profit_monthly`;

CREATE PROCEDURE `proc_report_customer_by_profit_monthly`(IN summary BOOLEAN,IN vehicle_team_ids TEXT,
	IN vehicle_ids TEXT,IN driver_ids TEXT,IN customer_ids TEXT,IN from_date TEXT,IN to_date TEXT,IN dayCondition TEXT,IN partner_ids TEXT)
BEGIN

	DECLARE query TEXT;
	DECLARE summaryQuery TEXT;

	SET query = "SELECT tmp1.id as entity_id, tmp1.full_name as entity_name,DATE_FORMAT(STR_TO_DATE(tmp1.date,'%Y-%m'),'%m-%Y') as date,
								((CASE WHEN tmp2.income IS NULL THEN 0 ELSE tmp2.income END) - (CASE WHEN tmp3.cost IS NULL THEN 0 ELSE tmp3.cost END)) as status_complete";

	SET query = CONCAT(query," ","FROM (SELECT c.id,c.full_name,cal.date,c.del_flag FROM ((SELECT cu.id,cu.full_name,cu.del_flag FROM customer cu WHERE cu.del_flag = 0) UNION (SELECT 0 as id ,'*Chưa gán khách hàng' as full_name,0 as del_flag)) c");

	SET query = CONCAT(query," ","CROSS JOIN (select DATE_FORMAT(aDate,'%Y-%m') as date from (select @maxDate - interval (a.a + (10 * b.a) + (100 * c.a)) month as aDate from
																						(select 0 as a union all select 1 union all select 2 union all select 3 union all select 4 union all select 5 union all select 6 union all select 7
																						union all select 8 union all select 9) a,
																						(select 0 as a union all select 1 union all select 2 union all select 3 union all select 4 union all select 5 union all select 6 union all select 7
																						union all select 8 union all select 9) b,
																						(select 0 as a union all select 1 union all select 2 union all select 3 union all select 4 union all select 5 union all select 6 union all select 7
																						union all select 8 union all select 9) c,
																						(select @minDate := '",from_date,"', @maxDate := '",to_date,"') d) e
																						where DATE_FORMAT(aDate,'%Y-%m') BETWEEN DATE_FORMAT(@minDate, '%Y-%m') AND DATE_FORMAT(@maxDate, '%Y-%m')) cal
																						WHERE 1=1");

	IF customer_ids IS NOT NULL AND customer_ids != "" THEN
		SET query = CONCAT(query," ","AND c.id IN (",customer_ids,")");
	END IF;
	SET query = CONCAT(query," ",") tmp1 LEFT JOIN (SELECT tmp.date,tmp.customer_id,SUM(status_complete) as income");

	SET query = CONCAT(query," ","FROM (SELECT DISTINCT o.id,
																						(CASE WHEN (o.customer_id IS NULL OR o.customer_id = 0 ) THEN 0 ELSE o.customer_id END) as customer_id,
																						o.amount as status_complete , ");
	IF dayCondition = 1 THEN
		SET query = CONCAT(query," "," DATE_FORMAT(o.ETD_date,'%Y-%m') as date");
	ELSEIF dayCondition = 2 THEN
		SET query = CONCAT(query," "," DATE_FORMAT(o.ETD_date_reality,'%Y-%m') as date");
	ELSEIF dayCondition = 3 THEN
		SET query = CONCAT(query," "," DATE_FORMAT(o.ETA_date,'%Y-%m') as date");
	ELSE
		SET query = CONCAT(query," "," DATE_FORMAT(o.ETA_date_reality,'%Y-%m') as date");
	END IF;

	SET query = CONCAT(query," ","FROM orders o ");

	IF vehicle_team_ids IS NOT NULL AND vehicle_team_ids != "" THEN
		SET query = CONCAT(query," ","INNER JOIN driver_vehicle_team dvt ON dvt.driver_id = o.primary_driver_id AND dvt.vehicle_team_id IN (",vehicle_team_ids,")");
	END IF;

	SET query = CONCAT(query," ","WHERE o.del_flag = 0 ");

	IF dayCondition = 1 THEN
		SET query = CONCAT(query," "," AND o.status IN(2,3,4,5,7) AND DATE_FORMAT(o.ETD_date,'%Y-%m') BETWEEN DATE_FORMAT('",from_date,"','%Y-%m') AND DATE_FORMAT('",to_date,"','%Y-%m') ");
	ELSEIF dayCondition = 2 THEN
		SET query = CONCAT(query," "," AND o.status IN (4,5) AND DATE_FORMAT(o.ETD_date_reality,'%Y-%m') BETWEEN DATE_FORMAT('",from_date,"','%Y-%m') AND DATE_FORMAT('",to_date,"','%Y-%m') ");
	ELSEIF dayCondition = 3 THEN
		SET query = CONCAT(query," "," AND o.status IN(2,3,4,5,7) AND DATE_FORMAT(o.ETA_date,'%Y-%m') BETWEEN DATE_FORMAT('",from_date,"','%Y-%m') AND DATE_FORMAT('",to_date,"','%Y-%m') ");
	ELSE
		SET query = CONCAT(query," "," AND o.status = 5 AND DATE_FORMAT(o.ETA_date_reality,'%Y-%m') BETWEEN DATE_FORMAT('",from_date,"','%Y-%m') AND DATE_FORMAT('",to_date,"','%Y-%m') ");
	END IF;

	IF driver_ids IS NOT NULL AND driver_ids != "" THEN
		SET query = CONCAT(query," ","AND o.primary_driver_id IN (",driver_ids,")");
	END IF;
	IF vehicle_ids IS NOT NULL AND vehicle_ids != "" THEN
		SET query = CONCAT(query," ","AND o.vehicle_id IN (",vehicle_ids,")");
	END IF;
	IF customer_ids IS NOT NULL AND customer_ids != "" THEN
		SET query = CONCAT(query," ","AND o.customer_id IN (",customer_ids,")");
	END IF;
	IF partner_ids IS NOT NULL AND partner_ids != "" THEN
		SET query = CONCAT(query," ","AND o.partner_id IN (",partner_ids,")");
	END IF;

	SET query = CONCAT(query," ",") tmp GROUP BY tmp.customer_id,tmp.date ) tmp2 ON tmp1.id = tmp2.customer_id AND tmp1.date = tmp2.date");

	SET query = CONCAT(query," ","LEFT JOIN (SELECT DATE_FORMAT(tmp.date,'%Y-%m') date, tmp.customer_id, SUM(tmp.final_cost) as cost");

		SET query = CONCAT(query," ","FROM (SELECT DISTINCT r.id,
																	(CASE WHEN (a.customer_id IS NULL OR a.customer_id = 0 ) THEN 0 ELSE a.customer_id END) as customer_id,
																	r.final_cost");
		IF dayCondition = 1 THEN
			SET query = CONCAT(query," ",",r.ETD_date as date");
		ELSEIF dayCondition = 2 THEN
			SET query = CONCAT(query," ",",r.ETD_date_reality as date");
		ELSEIF dayCondition = 3 THEN
			SET query = CONCAT(query," ",",r.ETA_date as date");
		ELSE
			SET query = CONCAT(query," ",",r.ETA_date_reality as date");
		END IF;

		SET query = CONCAT(query," ","FROM routes r
																	LEFT JOIN ( SELECT DISTINCT ro.route_id, o.customer_id FROM route_order ro
																							INNER JOIN orders o ON o.id = ro.order_id WHERE ro.del_flag = 0 AND o.del_flag = 0
																						) a ON a.route_id = r.id
																	LEFT JOIN driver_vehicle_team dvt ON dvt.driver_id = r.driver_id
																	WHERE r.del_flag = 0 AND r.is_approved = 1 AND r.route_status IN (1,0)");
		IF dayCondition = 1 THEN
			SET query = CONCAT(query," "," AND r.ETD_date IS NOT NULL AND DATE_FORMAT(r.ETD_date,'%Y-%m') BETWEEN DATE_FORMAT('",from_date,"', '%Y-%m') AND DATE_FORMAT('",to_date,"', '%Y-%m')");
		ELSEIF dayCondition = 2 THEN
			SET query = CONCAT(query," "," AND r.ETD_date_reality IS NOT NULL AND DATE_FORMAT(r.ETD_date_reality,'%Y-%m') BETWEEN DATE_FORMAT('",from_date,"', '%Y-%m') AND DATE_FORMAT('",to_date,"', '%Y-%m')");
		ELSEIF dayCondition = 3 THEN
			SET query = CONCAT(query," "," AND r.ETA_date IS NOT NULL AND DATE_FORMAT(r.ETA_date,'%Y-%m') BETWEEN DATE_FORMAT('",from_date,"', '%Y-%m') AND DATE_FORMAT('",to_date,"', '%Y-%m')");
		ELSE
			SET query = CONCAT(query," "," AND r.ETA_date_reality IS NOT NULL AND DATE_FORMAT(r.ETA_date_reality,'%Y-%m') BETWEEN DATE_FORMAT('",from_date,"', '%Y-%m') AND DATE_FORMAT('",to_date,"', '%Y-%m')");
		END IF;

		IF vehicle_team_ids IS NOT NULL AND vehicle_team_ids != "" THEN
			SET query = CONCAT(query," ","AND dvt.vehicle_team_id IN (",vehicle_team_ids,")");
		END IF;
		IF driver_ids IS NOT NULL AND driver_ids != "" THEN
			SET query = CONCAT(query," ","AND r.driver_id IN (",driver_ids,")");
		END IF;
		IF vehicle_ids IS NOT NULL AND vehicle_ids != "" THEN
			SET query = CONCAT(query," ","AND r.vehicle_id IN (",vehicle_ids,")");
		END IF;
		IF customer_ids IS NOT NULL AND customer_ids != "" THEN
			SET query = CONCAT(query," ","AND a.customer_id IN (",customer_ids,")");
		END IF;
		IF partner_ids IS NOT NULL AND partner_ids != "" THEN
			SET query = CONCAT(query," ","AND r.partner_id IN (",partner_ids,")");
		END IF;

		SET query = CONCAT(query," ",") tmp GROUP BY tmp.customer_id, DATE_FORMAT(tmp.date,'%Y-%m')
																	) tmp3 ON tmp1.id = tmp3.customer_id AND tmp1.date = tmp3.date ");

			SET query = CONCAT(query," ","WHERE tmp1.del_flag = 0 OR ((CASE WHEN tmp2.income IS NULL THEN 0 ELSE tmp2.income END) - (CASE WHEN tmp3.cost IS NULL THEN 0 ELSE tmp3.cost END)) !=0 ");

	 IF summary = 0 THEN
			SET @SQLText = CONCAT(query,' ','ORDER BY tmp1.full_name,tmp1.date');
			-- SELECT @SQLText;
			PREPARE stmt FROM @SQLText;
	 ELSE
			SET summaryQuery = "SELECT tmp4.date as date,SUM(tmp4.status_complete) as total";

			SET @SQLText = CONCAT(summaryQuery," FROM(", query,") tmp4 GROUP BY tmp4.date ORDER BY STR_TO_DATE(tmp4.date,'%m-%Y')");
			PREPARE stmt FROM @SQLText;
	 END IF;

   EXECUTE stmt;
   DEALLOCATE PREPARE stmt;
END
;


-- ----------------------------
-- Procedure structure for proc_report_customer_by_turn
-- ----------------------------
DROP PROCEDURE IF EXISTS `proc_report_customer_by_turn`;

CREATE PROCEDURE `proc_report_customer_by_turn`(IN summary BOOLEAN,IN vehicle_team_ids TEXT,
	IN vehicle_ids TEXT,IN driver_ids TEXT,
	IN customer_ids TEXT,IN status_all BOOLEAN,IN status_incomplete BOOLEAN,
	IN status_complete BOOLEAN,IN status_cancel BOOLEAN,IN from_date TEXT,IN to_date TEXT,IN partner_ids TEXT)
BEGIN

	DECLARE query TEXT;
	DECLARE summaryQuery TEXT;

	SET query = "SELECT tmp1.id as entity_id, tmp1.full_name as entity_name,DATE_FORMAT(tmp1.date,'%d-%m-%Y') as date";
		IF status_all = 1 THEN SET query = CONCAT(query,",","tmp2.status_all"); END IF;
		IF status_complete = 1 THEN
			SET query = CONCAT(query,",","tmp2.status_complete");
			SET query = CONCAT(query,",","tmp2.status_on_time");
			SET query = CONCAT(query,",","tmp2.status_late");
		END IF;
		IF status_incomplete = 1 THEN SET query = CONCAT(query,",","tmp2.status_incomplete"); END IF;
		IF status_cancel  = 1 THEN SET query = CONCAT(query,",","tmp2.status_cancel "); END IF;

	SET query = CONCAT(query," ","FROM (SELECT c.id,c.full_name,cal.date,c.del_flag FROM ((SELECT cu.id,cu.full_name,cu.del_flag FROM customer cu) UNION (SELECT 0 as id ,'*Chưa gán khách hàng' as full_name,0 as del_flag)) c");

	SET query = CONCAT(query," ","CROSS JOIN (
										SELECT selected_date as date FROM (
													SELECT adddate('1970-01-01',t4.i*10000 + t3.i*1000 + t2.i*100 + t1.i*10 + t0.i) selected_date
													FROM
														 (SELECT 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t0,
														 (SELECT 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t1,
														 (SELECT 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t2,
														 (SELECT 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t3,
														 (SELECT 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t4
										) d
										WHERE selected_date BETWEEN '",from_date,"' AND '",to_date,"'
										) cal
										WHERE 1=1 ");

	IF customer_ids IS NOT NULL AND customer_ids != "" THEN
		SET query = CONCAT(query," ","AND c.id IN (",customer_ids,")");
	END IF;
	SET query = CONCAT(query," ",") tmp1 LEFT JOIN (SELECT tmp.date,tmp.customer_id");

		IF status_all = 1 THEN SET query = CONCAT(query,",","(SUM(status_complete) + SUM(status_incomplete) + SUM(status_cancel))  as status_all"); END IF;
		IF status_complete = 1 THEN
			SET query = CONCAT(query,",","SUM(status_complete) as status_complete");
			SET query = CONCAT(query,",","SUM(status_on_time) as status_on_time");
			SET query = CONCAT(query,",","SUM(status_late) as status_late");
		END IF;
		IF status_incomplete = 1 THEN SET query = CONCAT(query,",","SUM(status_incomplete) as status_incomplete"); END IF;
		IF status_cancel = 1 THEN SET query = CONCAT(query,",","SUM(status_cancel) as status_cancel"); END IF;

	SET query = CONCAT(query," ","FROM (SELECT DISTINCT o.id, (CASE WHEN (o.ETA_date_reality IS NOT NULL AND o.status = 5) THEN o.ETA_date_reality ELSE o.ETA_date END) date,
																							(CASE WHEN (o.customer_id IS NULL OR o.customer_id = 0 ) THEN 0 ELSE o.customer_id END) as customer_id");

		IF status_complete = 1 THEN
			SET query = CONCAT(query,",","(CASE WHEN (o.ETA_date_reality IS NOT NULL AND o.status = 5) THEN 1 ELSE 0 END) as status_complete");
			SET query = CONCAT(query,",","(CASE WHEN (o.ETA_date_reality IS NOT NULL AND o.status = 5
																			AND DATE_FORMAT(CONCAT(o.ETA_date,' ',o.ETA_time), '%Y-%m-%d %H:%i') >= DATE_FORMAT(CONCAT(o.ETA_date_reality,' ',o.ETA_time_reality)
																			, '%Y-%m-%d %H:%i')) THEN 1 ELSE 0 END) as status_on_time");
			SET query = CONCAT(query,",","(CASE WHEN (o.ETA_date_reality IS NOT NULL AND o.status = 5
																			AND DATE_FORMAT(CONCAT(o.ETA_date,' ',o.ETA_time), '%Y-%m-%d %H:%i') < DATE_FORMAT(CONCAT(o.ETA_date_reality,' ',o.ETA_time_reality)
																			, '%Y-%m-%d %H:%i')) THEN 1 ELSE 0 END) as status_late");
		ELSE
			SET query = CONCAT(query,",","0 as status_complete");
		END IF;
		IF status_incomplete = 1 THEN SET query = CONCAT(query,",","(CASE WHEN o.status IN (2,3,4,7) THEN 1 ELSE 0 END) as status_incomplete");
		ELSE
			SET query = CONCAT(query,",","0 as status_incomplete");
		END IF;

		IF status_cancel = 1 THEN SET query = CONCAT(query,",","(CASE WHEN o.status = 6 THEN 1 ELSE 0 END) as status_cancel");
		ELSE
			SET query = CONCAT(query,",","0 as status_cancel");
		END IF;

	SET query = CONCAT(query," ","FROM orders o ");

	IF vehicle_team_ids IS NOT NULL AND vehicle_team_ids != "" THEN
		SET query = CONCAT(query," ","INNER JOIN driver_vehicle_team dvt ON dvt.driver_id = o.primary_driver_id AND dvt.vehicle_team_id IN (",vehicle_team_ids,")");
	END IF;

	SET query = CONCAT(query," ","WHERE o.del_flag = 0 AND o.status != 1 AND CASE WHEN o.ETA_date_reality IS NOT NULL AND o.status = 5
																																								THEN o.ETA_date_reality BETWEEN '",from_date,"' AND '",to_date,"'
																																								ELSE o.ETA_date BETWEEN '",from_date,"' AND '",to_date,"' END");

	IF driver_ids IS NOT NULL AND driver_ids != "" THEN
		SET query = CONCAT(query," ","AND o.primary_driver_id IN (",driver_ids,")");
	END IF;
	IF vehicle_ids IS NOT NULL AND vehicle_ids != "" THEN
		SET query = CONCAT(query," ","AND o.vehicle_id IN (",vehicle_ids,")");
	END IF;
	IF customer_ids IS NOT NULL AND customer_ids != "" THEN
		SET query = CONCAT(query," ","AND o.customer_id IN (",customer_ids,")");
	END IF;
	IF partner_ids IS NOT NULL AND partner_ids != "" THEN
		SET query = CONCAT(query," ","AND o.partner_id IN (",partner_ids,")");
	END IF;

	SET query = CONCAT(query," ",") tmp GROUP BY tmp.customer_id,tmp.date ) tmp2 ON tmp1.id = tmp2.customer_id AND tmp1.date = tmp2.date");

	SET query = CONCAT(query," ","WHERE tmp1.del_flag = 0 OR (tmp2.status_all IS NOT NULL AND tmp2.status_all != 0 )");

	 IF summary = 0 THEN
			SET @SQLText = CONCAT(query,' ','ORDER BY tmp1.full_name,tmp1.date');
		  -- SELECT @SQLText;
			PREPARE stmt FROM @SQLText;
	 ELSE
			SET summaryQuery = "SELECT tmp3.date as date,";

			IF status_all = 1 THEN SET summaryQuery = CONCAT(summaryQuery,"SUM(tmp3.status_all) as total");
			ELSEIF status_incomplete = 1 THEN SET summaryQuery = CONCAT(summaryQuery,"SUM(tmp3.status_incomplete) as total");
			ELSEIF status_complete = 1 THEN SET summaryQuery = CONCAT(summaryQuery,"SUM(tmp3.status_complete) as total");
			ELSEIF status_cancel = 1 THEN SET summaryQuery = CONCAT(summaryQuery,"SUM(tmp3.status_cancel) as total");
			END IF;

			SET @SQLText = CONCAT(summaryQuery," FROM(", query,") tmp3 GROUP BY tmp3.date ORDER BY STR_TO_DATE(tmp3.date,'%d-%m-%Y')");
			PREPARE stmt FROM @SQLText;
	 END IF;

   EXECUTE stmt;
   DEALLOCATE PREPARE stmt;
END
;


-- ----------------------------
-- Procedure structure for proc_report_customer_by_turn_monthly
-- ----------------------------
DROP PROCEDURE IF EXISTS `proc_report_customer_by_turn_monthly`;

CREATE PROCEDURE `proc_report_customer_by_turn_monthly`(IN summary BOOLEAN,IN vehicle_team_ids TEXT,
	IN vehicle_ids TEXT,IN driver_ids TEXT,
	IN customer_ids TEXT,IN status_all BOOLEAN,IN status_incomplete BOOLEAN,
	IN status_complete BOOLEAN,IN status_cancel BOOLEAN,IN from_date TEXT,IN to_date TEXT,IN partner_ids TEXT)
BEGIN

	DECLARE query TEXT;
	DECLARE summaryQuery TEXT;

	SET query = "SELECT tmp1.id as entity_id, tmp1.full_name as entity_name,DATE_FORMAT(STR_TO_DATE(tmp1.date,'%Y-%m'),'%m-%Y') as date";
		IF status_all = 1 THEN SET query = CONCAT(query,",","tmp2.status_all"); END IF;
		IF status_complete = 1 THEN
			SET query = CONCAT(query,",","tmp2.status_complete");
			SET query = CONCAT(query,",","tmp2.status_on_time");
			SET query = CONCAT(query,",","tmp2.status_late");
		END IF;
		IF status_incomplete = 1 THEN SET query = CONCAT(query,",","tmp2.status_incomplete"); END IF;
		IF status_cancel  = 1 THEN SET query = CONCAT(query,",","tmp2.status_cancel"); END IF;

	SET query = CONCAT(query," ","FROM (SELECT c.id,c.full_name,cal.date,c.del_flag FROM ((SELECT cu.id,cu.full_name,cu.del_flag FROM customer cu) UNION (SELECT 0 as id ,'*Chưa gán khách hàng' as full_name,0 as del_flag)) c");

	SET query = CONCAT(query," ","CROSS JOIN (select DATE_FORMAT(aDate,'%Y-%m') as date from (select @maxDate - interval (a.a + (10 * b.a) + (100 * c.a)) month as aDate from
																						(select 0 as a union all select 1 union all select 2 union all select 3 union all select 4 union all select 5 union all select 6 union all select 7
																						union all select 8 union all select 9) a,
																						(select 0 as a union all select 1 union all select 2 union all select 3 union all select 4 union all select 5 union all select 6 union all select 7
																						union all select 8 union all select 9) b,
																						(select 0 as a union all select 1 union all select 2 union all select 3 union all select 4 union all select 5 union all select 6 union all select 7
																						union all select 8 union all select 9) c,
																						(select @minDate := '",from_date,"', @maxDate := '",to_date,"') d) e
																						where DATE_FORMAT(aDate,'%Y-%m') BETWEEN DATE_FORMAT(@minDate, '%Y-%m') AND DATE_FORMAT(@maxDate, '%Y-%m')) cal
																						WHERE 1=1");

	IF customer_ids IS NOT NULL AND customer_ids != "" THEN
		SET query = CONCAT(query," ","AND c.id IN (",customer_ids,")");
	END IF;
	SET query = CONCAT(query," ",") tmp1 LEFT JOIN (SELECT tmp.date,tmp.customer_id");

		IF status_all = 1 THEN SET query = CONCAT(query,",","(SUM(status_complete) + SUM(status_incomplete) + SUM(status_cancel))  as status_all"); END IF;
		IF status_complete = 1 THEN
			SET query = CONCAT(query,",","SUM(status_complete) as status_complete");
			SET query = CONCAT(query,",","SUM(status_on_time) as status_on_time");
			SET query = CONCAT(query,",","SUM(status_late) as status_late");
		END IF;
		IF status_incomplete = 1 THEN SET query = CONCAT(query,",","SUM(status_incomplete) as status_incomplete"); END IF;
		IF status_cancel = 1 THEN SET query = CONCAT(query,",","SUM(status_cancel) as status_cancel"); END IF;

	SET query = CONCAT(query," ","FROM (SELECT DISTINCT o.id, (CASE WHEN (o.ETA_date_reality IS NOT NULL AND o.status = 5) THEN DATE_FORMAT(o.ETA_date_reality,'%Y-%m') ELSE DATE_FORMAT(o.ETA_date,'%Y-%m') END) date,
																						(CASE WHEN (o.customer_id IS NULL OR o.customer_id = 0 ) THEN 0 ELSE o.customer_id END) as customer_id");

		IF status_complete = 1 THEN
			SET query = CONCAT(query,",","(CASE WHEN (o.ETA_date_reality IS NOT NULL AND o.status = 5) THEN 1 ELSE 0 END) as status_complete");
			SET query = CONCAT(query,",","(CASE WHEN (o.ETA_date_reality IS NOT NULL AND o.status = 5
																	AND DATE_FORMAT(CONCAT(o.ETA_date,' ',o.ETA_time), '%Y-%m-%d %H:%i') >= DATE_FORMAT(CONCAT(o.ETA_date_reality,' ',o.ETA_time_reality)
																	, '%Y-%m-%d %H:%i')) THEN 1 ELSE 0 END) as status_on_time");
			SET query = CONCAT(query,",","(CASE WHEN (o.ETA_date_reality IS NOT NULL AND o.status = 5
																			AND DATE_FORMAT(CONCAT(o.ETA_date,' ',o.ETA_time), '%Y-%m-%d %H:%i')< DATE_FORMAT(CONCAT(o.ETA_date_reality,' ',o.ETA_time_reality)
																			, '%Y-%m-%d %H:%i')) THEN 1 ELSE 0 END) as status_late");
		ELSE
			SET query = CONCAT(query,",","0 as status_complete");
		END IF;
		IF status_incomplete = 1 THEN SET query = CONCAT(query,",","(CASE WHEN o.status IN (2,3,4,7) THEN 1 ELSE 0 END) as status_incomplete");
		ELSE
			SET query = CONCAT(query,",","0 as status_incomplete");
		END IF;

		IF status_cancel = 1 THEN SET query = CONCAT(query,",","(CASE WHEN o.status = 6 THEN 1 ELSE 0 END) as status_cancel");
		ELSE
			SET query = CONCAT(query,",","0 as status_cancel");
		END IF;

	SET query = CONCAT(query," ","FROM orders o ");

	IF vehicle_team_ids IS NOT NULL AND vehicle_team_ids != "" THEN
		SET query = CONCAT(query," ","INNER JOIN driver_vehicle_team dvt ON dvt.driver_id = o.primary_driver_id AND dvt.vehicle_team_id IN (",vehicle_team_ids,")");
	END IF;

	SET query = CONCAT(query," ","WHERE o.del_flag = 0 AND o.status != 1 AND CASE WHEN o.ETA_date_reality IS NOT NULL AND o.status = 5
																																								THEN o.ETA_date_reality BETWEEN '",from_date,"' AND '",to_date,"'
																																								ELSE o.ETA_date BETWEEN '",from_date,"' AND '",to_date,"' END");

	IF driver_ids IS NOT NULL AND driver_ids != "" THEN
		SET query = CONCAT(query," ","AND o.primary_driver_id IN (",driver_ids,")");
	END IF;
	IF vehicle_ids IS NOT NULL AND vehicle_ids != "" THEN
		SET query = CONCAT(query," ","AND o.vehicle_id IN (",vehicle_ids,")");
	END IF;
	IF customer_ids IS NOT NULL AND customer_ids != "" THEN
		SET query = CONCAT(query," ","AND o.customer_id IN (",customer_ids,")");
	END IF;
	IF partner_ids IS NOT NULL AND partner_ids != "" THEN
		SET query = CONCAT(query," ","AND o.partner_id IN (",partner_ids,")");
	END IF;

	SET query = CONCAT(query," ",") tmp GROUP BY tmp.customer_id,tmp.date ) tmp2 ON tmp1.id = tmp2.customer_id AND tmp1.date = tmp2.date");

	SET query = CONCAT(query," ","WHERE tmp1.del_flag = 0 OR (tmp2.status_all IS NOT NULL AND tmp2.status_all != 0 )");

	 IF summary = 0 THEN
			SET @SQLText = CONCAT(query,' ','ORDER BY tmp1.full_name,tmp1.date');
			 -- SELECT @SQLText;
			 PREPARE stmt FROM @SQLText;
	 ELSE
			SET summaryQuery = "SELECT tmp3.date as date,";

			IF status_all = 1 THEN SET summaryQuery = CONCAT(summaryQuery,"SUM(tmp3.status_all) as total");
			ELSEIF status_incomplete = 1 THEN SET summaryQuery = CONCAT(summaryQuery,"SUM(tmp3.status_incomplete) as total");
			ELSEIF status_complete = 1 THEN SET summaryQuery = CONCAT(summaryQuery,"SUM(tmp3.status_complete) as total");
			ELSEIF status_cancel = 1 THEN SET summaryQuery = CONCAT(summaryQuery,"SUM(tmp3.status_cancel) as total");
			END IF;

			SET @SQLText = CONCAT(summaryQuery," FROM(", query,") tmp3 GROUP BY tmp3.date ORDER BY STR_TO_DATE(tmp3.date,'%m-%Y')");
			PREPARE stmt FROM @SQLText;
	 END IF;

   EXECUTE stmt;
   DEALLOCATE PREPARE stmt;
END
;


-- ----------------------------
-- Procedure structure for proc_report_driver_by_cost
-- ----------------------------
DROP PROCEDURE IF EXISTS `proc_report_driver_by_cost`;

CREATE PROCEDURE `proc_report_driver_by_cost`(IN summary BOOLEAN,
	IN vehicle_team_ids TEXT,IN vehicle_ids TEXT,IN driver_ids TEXT,
	IN customer_ids TEXT,IN from_date TEXT,IN to_date TEXT,IN dayCondition TEXT,IN partner_ids TEXT)
BEGIN

	DECLARE query TEXT;
	DECLARE summaryQuery TEXT;

	SET query = "SELECT tmp1.id as entity_id, tmp1.full_name as entity_name, DATE_FORMAT(tmp1.date, '%d-%m-%Y') as date, tmp2.cost as status_complete";
	SET query = CONCAT(query," ","FROM (SELECT d.id,d.full_name,cal.selected_date as date,d.del_flag FROM ((SELECT dv.id,dv.full_name,dv.del_flag FROM drivers dv) UNION (SELECT 0 as id ,'*Chưa gán tài xế' as full_name,0 as del_flag)) dr");

	IF vehicle_team_ids IS NOT NULL AND vehicle_team_ids != "" THEN
		SET query = CONCAT(query," ","LEFT JOIN driver_vehicle_team dvt ON dvt.driver_id = dr.id");
	END IF;

	SET query = CONCAT(query," ","CROSS JOIN (
										SELECT selected_date FROM (
													SELECT adddate('1970-01-01',t4.i*10000 + t3.i*1000 + t2.i*100 + t1.i*10 + t0.i) selected_date
													FROM
														 (SELECT 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t0,
														 (SELECT 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t1,
														 (SELECT 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t2,
														 (SELECT 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t3,
														 (SELECT 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t4
										) d
										WHERE selected_date BETWEEN '",from_date,"' AND '",to_date,"'
										) cal WHERE 1=1");

	IF driver_ids IS NOT NULL AND driver_ids != "" THEN
		SET query = CONCAT(query," ","AND dr.id IN (",driver_ids,")");
	END IF;

	IF partner_ids IS NOT NULL AND partner_ids != "" THEN
		SET query = CONCAT(query," ","AND dr.partner_id IN (",partner_ids,")");
	END IF;

	IF vehicle_team_ids IS NOT NULL AND vehicle_team_ids != "" THEN
		SET query = CONCAT(query," ","AND dvt.vehicle_team_id IN (",vehicle_team_ids,")");
	END IF;

	SET query = CONCAT(query," ",") tmp1 LEFT JOIN (SELECT tmp.date,tmp.driver_id, SUM(tmp.final_cost) as cost");

	SET query = CONCAT(query," ","FROM (SELECT DISTINCT r.id,
																(CASE WHEN (r.driver_id IS NULL OR r.driver_id = 0 ) THEN 0 ELSE r.driver_id END) as driver_id,
																r.final_cost");
	IF dayCondition = 1 THEN
		SET query = CONCAT(query," ",",r.ETD_date as date");
	ELSEIF dayCondition = 2 THEN
		SET query = CONCAT(query," ",",r.ETD_date_reality as date");
	ELSEIF dayCondition = 3 THEN
		SET query = CONCAT(query," ",",r.ETA_date as date");
	ELSE
		SET query = CONCAT(query," ",",r.ETA_date_reality as date");
	END IF;

	SET query = CONCAT(query," ","FROM routes r
																LEFT JOIN ( SELECT DISTINCT ro.route_id, o.customer_id FROM route_order ro
																						INNER JOIN orders o ON o.id = ro.order_id WHERE ro.del_flag = 0 AND o.del_flag = 0
																					) a ON a.route_id = r.id
																LEFT JOIN driver_vehicle_team dvt ON dvt.driver_id = r.driver_id
																WHERE r.del_flag = 0 AND r.is_approved = 1 AND r.route_status IN (1,0)");
	IF dayCondition = 1 THEN
		SET query = CONCAT(query," "," AND r.ETD_date IS NOT NULL AND r.ETD_date BETWEEN '",from_date,"' AND '",to_date,"' ");
	ELSEIF dayCondition = 2 THEN
		SET query = CONCAT(query," "," AND r.ETD_date_reality IS NOT NULL AND r.ETD_date_reality BETWEEN '",from_date,"' AND '",to_date,"' ");
	ELSEIF dayCondition = 3 THEN
		SET query = CONCAT(query," "," AND r.ETA_date IS NOT NULL AND r.ETA_date BETWEEN '",from_date,"' AND '",to_date,"' ");
	ELSE
		SET query = CONCAT(query," "," AND r.ETA_date_reality IS NOT NULL AND r.ETA_date_reality BETWEEN '",from_date,"' AND '",to_date,"' ");
	END IF;

	IF vehicle_team_ids IS NOT NULL AND vehicle_team_ids != "" THEN
		SET query = CONCAT(query," ","AND dvt.vehicle_team_id IN (",vehicle_team_ids,")");
	END IF;
	IF driver_ids IS NOT NULL AND driver_ids != "" THEN
		SET query = CONCAT(query," ","AND r.driver_id IN (",driver_ids,")");
	END IF;
	IF vehicle_ids IS NOT NULL AND vehicle_ids != "" THEN
		SET query = CONCAT(query," ","AND r.vehicle_id IN (",vehicle_ids,")");
	END IF;
	IF customer_ids IS NOT NULL AND customer_ids != "" THEN
		SET query = CONCAT(query," ","AND a.customer_id IN (",customer_ids,")");
	END IF;
	IF partner_ids IS NOT NULL AND partner_ids != "" THEN
		SET query = CONCAT(query," ","AND r.partner_id IN (",partner_ids,")");
	END IF;

	SET query = CONCAT(query," ",") tmp GROUP BY tmp.driver_id, DATE_FORMAT(tmp.date,'%Y-%m-%d')
																) tmp2 ON tmp1.id = tmp2.driver_id AND tmp1.date = tmp2.date ");

	SET query = CONCAT(query," ","WHERE tmp1.del_flag = 0 OR (tmp2.cost IS NOT NULL AND tmp2.cost != 0 )");

	 IF summary = 0 THEN
			SET @SQLText = CONCAT(query,' ','ORDER BY tmp1.full_name,tmp1.date');
			PREPARE stmt FROM @SQLText;
	 ELSE
			SET summaryQuery = "SELECT tmp3.date as date, SUM(tmp3.status_complete) as total";
			SET @SQLText = CONCAT(summaryQuery," FROM(", query,") tmp3 GROUP BY tmp3.date ORDER BY STR_TO_DATE(tmp3.date,'%d-%m-%Y')");
			PREPARE stmt FROM @SQLText;
	 END IF;

   EXECUTE stmt;
   DEALLOCATE PREPARE stmt;
END
;


-- ----------------------------
-- Procedure structure for proc_report_driver_by_cost_monthly
-- ----------------------------
DROP PROCEDURE IF EXISTS `proc_report_driver_by_cost_monthly`;

CREATE PROCEDURE `proc_report_driver_by_cost_monthly`(IN summary BOOLEAN,
	IN vehicle_team_ids TEXT,IN vehicle_ids TEXT,IN driver_ids TEXT,
	IN customer_ids TEXT,IN from_date TEXT,IN to_date TEXT,IN dayCondition TEXT,IN partner_ids TEXT)
BEGIN

DECLARE query TEXT;
	DECLARE summaryQuery TEXT;

	SET query = "SELECT tmp1.id as entity_id, tmp1.full_name as entity_name, DATE_FORMAT(STR_TO_DATE(tmp1.date,'%Y-%m'),'%m-%Y') as date, tmp2.cost as status_complete";
	SET query = CONCAT(query," ","FROM (SELECT d.id,d.full_name,cal.date,d.del_flag FROM ((SELECT dv.id,dv.full_name,dv.del_flag FROM drivers dv) UNION (SELECT 0 as id ,'*Chưa gán tài xế' as full_name,0 as del_flag)) dr");

	IF vehicle_team_ids IS NOT NULL AND vehicle_team_ids != "" THEN
		SET query = CONCAT(query," ","LEFT JOIN driver_vehicle_team dvt ON dvt.driver_id = dr.id");
	END IF;

	SET query = CONCAT(query," ","CROSS JOIN (select DATE_FORMAT(aDate,'%Y-%m') as date from (select @maxDate - interval (a.a + (10 * b.a) + (100 * c.a)) month as aDate from
																						(select 0 as a union all select 1 union all select 2 union all select 3 union all select 4 union all select 5 union all select 6 union all select 7
																						union all select 8 union all select 9) a,
																						(select 0 as a union all select 1 union all select 2 union all select 3 union all select 4 union all select 5 union all select 6 union all select 7
																						union all select 8 union all select 9) b,
																						(select 0 as a union all select 1 union all select 2 union all select 3 union all select 4 union all select 5 union all select 6 union all select 7
																						union all select 8 union all select 9) c,
																						(select @minDate := '",from_date,"', @maxDate := '",to_date,"') d) e
																						where DATE_FORMAT(aDate,'%Y-%m') BETWEEN DATE_FORMAT(@minDate, '%Y-%m') AND DATE_FORMAT(@maxDate, '%Y-%m')) cal WHERE 1=1");

	IF driver_ids IS NOT NULL AND driver_ids != "" THEN
		SET query = CONCAT(query," ","AND dr.id IN (",driver_ids,")");
	END IF;

	IF vehicle_team_ids IS NOT NULL AND vehicle_team_ids != "" THEN
		SET query = CONCAT(query," ","AND dvt.vehicle_team_id IN (",vehicle_team_ids,")");
	END IF;

	IF partner_ids IS NOT NULL AND partner_ids != "" THEN
		SET query = CONCAT(query," ","AND dr.partner_id IN (",partner_ids,")");
	END IF;

	SET query = CONCAT(query," ",") tmp1 LEFT JOIN (SELECT DATE_FORMAT(tmp.date,'%Y-%m') as date,tmp.driver_id, SUM(tmp.final_cost) as cost");

		SET query = CONCAT(query," ","FROM (SELECT DISTINCT r.id,
																	(CASE WHEN (r.driver_id IS NULL OR r.driver_id = 0 ) THEN 0 ELSE r.driver_id END) as driver_id,
																	r.final_cost");
		IF dayCondition = 1 THEN
			SET query = CONCAT(query," ",",r.ETD_date as date");
		ELSEIF dayCondition = 2 THEN
			SET query = CONCAT(query," ",",r.ETD_date_reality as date");
		ELSEIF dayCondition = 3 THEN
			SET query = CONCAT(query," ",",r.ETA_date as date");
		ELSE
			SET query = CONCAT(query," ",",r.ETA_date_reality as date");
		END IF;

		SET query = CONCAT(query," ","FROM routes r
																	LEFT JOIN ( SELECT DISTINCT ro.route_id, o.customer_id FROM route_order ro
																							INNER JOIN orders o ON o.id = ro.order_id WHERE ro.del_flag = 0 AND o.del_flag = 0
																						) a ON a.route_id = r.id
																	LEFT JOIN driver_vehicle_team dvt ON dvt.driver_id = r.driver_id
																	WHERE r.del_flag = 0 AND r.is_approved = 1 AND r.route_status IN (1,0)");
		IF dayCondition = 1 THEN
			SET query = CONCAT(query," "," AND r.ETD_date IS NOT NULL AND DATE_FORMAT(r.ETD_date,'%Y-%m') BETWEEN DATE_FORMAT('",from_date,"', '%Y-%m') AND DATE_FORMAT('",to_date,"', '%Y-%m')");
		ELSEIF dayCondition = 2 THEN
			SET query = CONCAT(query," "," AND r.ETD_date_reality IS NOT NULL AND DATE_FORMAT(r.ETD_date_reality,'%Y-%m') BETWEEN DATE_FORMAT('",from_date,"', '%Y-%m') AND DATE_FORMAT('",to_date,"', '%Y-%m')");
		ELSEIF dayCondition = 3 THEN
			SET query = CONCAT(query," "," AND r.ETA_date IS NOT NULL AND DATE_FORMAT(r.ETA_date,'%Y-%m') BETWEEN DATE_FORMAT('",from_date,"', '%Y-%m') AND DATE_FORMAT('",to_date,"', '%Y-%m')");
		ELSE
			SET query = CONCAT(query," "," AND r.ETA_date_reality IS NOT NULL AND DATE_FORMAT(r.ETA_date_reality,'%Y-%m') BETWEEN DATE_FORMAT('",from_date,"', '%Y-%m') AND DATE_FORMAT('",to_date,"', '%Y-%m')");
		END IF;

		IF vehicle_team_ids IS NOT NULL AND vehicle_team_ids != "" THEN
			SET query = CONCAT(query," ","AND dvt.vehicle_team_id IN (",vehicle_team_ids,")");
		END IF;
		IF driver_ids IS NOT NULL AND driver_ids != "" THEN
			SET query = CONCAT(query," ","AND r.driver_id IN (",driver_ids,")");
		END IF;
		IF vehicle_ids IS NOT NULL AND vehicle_ids != "" THEN
			SET query = CONCAT(query," ","AND r.vehicle_id IN (",vehicle_ids,")");
		END IF;
		IF customer_ids IS NOT NULL AND customer_ids != "" THEN
			SET query = CONCAT(query," ","AND a.customer_id IN (",customer_ids,")");
		END IF;
		IF partner_ids IS NOT NULL AND partner_ids != "" THEN
			SET query = CONCAT(query," ","AND r.partner_id IN (",partner_ids,")");
		END IF;

		SET query = CONCAT(query," ",") tmp GROUP BY tmp.driver_id, DATE_FORMAT(tmp.date,'%Y-%m')
																	) tmp2 ON tmp1.id = tmp2.driver_id AND tmp1.date = tmp2.date ");

		SET query = CONCAT(query," ","WHERE tmp1.del_flag = 0 OR (tmp2.cost IS NOT NULL AND tmp2.cost != 0 )");

	 IF summary = 0 THEN
			SET @SQLText = CONCAT(query,' ','ORDER BY tmp1.full_name,tmp1.date');
			PREPARE stmt FROM @SQLText;
	 ELSE
			SET summaryQuery = "SELECT tmp3.date as date, SUM(tmp3.status_complete) as total";
			SET @SQLText = CONCAT(summaryQuery," FROM(", query,") tmp3 GROUP BY tmp3.date ORDER BY STR_TO_DATE(tmp3.date,'%m-%Y')");
			PREPARE stmt FROM @SQLText;
	 END IF;

   EXECUTE stmt;
   DEALLOCATE PREPARE stmt;
END
;


-- ----------------------------
-- Procedure structure for proc_report_driver_by_income
-- ----------------------------
DROP PROCEDURE IF EXISTS `proc_report_driver_by_income`;

CREATE PROCEDURE `proc_report_driver_by_income`(IN summary BOOLEAN,IN vehicle_team_ids TEXT,
	IN vehicle_ids TEXT,IN driver_ids TEXT,IN customer_ids TEXT,IN from_date TEXT,IN to_date TEXT,IN dayCondition TEXT,IN partner_ids TEXT)
BEGIN

	DECLARE query TEXT;
	DECLARE summaryQuery TEXT;

	SET query = "SELECT tmp1.id as entity_id, tmp1.full_name as entity_name,DATE_FORMAT(tmp1.date,'%d-%m-%Y') as date,tmp2.status_complete";

	SET query = CONCAT(query," ","FROM (SELECT d.id,d.full_name,cal.date,d.del_flag FROM ((SELECT dv.id,dv.full_name,dv.del_flag FROM drivers dv) UNION (SELECT 0 as id ,'*Chưa gán tài xế' as full_name,0 as del_flag)) d");

	IF vehicle_team_ids IS NOT NULL AND vehicle_team_ids != "" THEN
		SET query = CONCAT(query," ","LEFT JOIN driver_vehicle_team dvt ON dvt.driver_id = d.id");
	END IF;

	SET query = CONCAT(query," ","CROSS JOIN (
										SELECT selected_date as date FROM (
													SELECT adddate('1970-01-01',t4.i*10000 + t3.i*1000 + t2.i*100 + t1.i*10 + t0.i) selected_date
													FROM
														 (SELECT 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t0,
														 (SELECT 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t1,
														 (SELECT 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t2,
														 (SELECT 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t3,
														 (SELECT 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t4
										) d
										WHERE selected_date BETWEEN '",from_date,"' AND '",to_date,"'
										) cal WHERE 1=1");

	IF driver_ids IS NOT NULL AND driver_ids != "" THEN
		SET query = CONCAT(query," ","AND d.id IN (",driver_ids,")");
	END IF;

	IF partner_ids IS NOT NULL AND partner_ids != "" THEN
		SET query = CONCAT(query," ","AND d.partner_id IN (",partner_ids,")");
	END IF;

	IF vehicle_team_ids IS NOT NULL AND vehicle_team_ids != "" THEN
		SET query = CONCAT(query," ","AND dvt.vehicle_team_id IN (",vehicle_team_ids,")");
	END IF;

	SET query = CONCAT(query," ",") tmp1 LEFT JOIN (SELECT tmp.date,tmp.driver_id,SUM(status_complete) as status_complete");

	SET query = CONCAT(query," ","FROM (SELECT DISTINCT o.id,
																(CASE WHEN (o.primary_driver_id IS NULL OR o.primary_driver_id = 0 ) THEN 0 ELSE o.primary_driver_id END) as driver_id,
																 o.amount as status_complete, ");

	IF dayCondition = 1 THEN
		SET query = CONCAT(query," "," o.ETD_date as date");
	ELSEIF dayCondition = 2 THEN
		SET query = CONCAT(query," "," o.ETD_date_reality as date");
	ELSEIF dayCondition = 3 THEN
		SET query = CONCAT(query," "," o.ETA_date as date");
	ELSE
		SET query = CONCAT(query," "," o.ETA_date_reality as date");
	END IF;

	SET query = CONCAT(query," ","FROM orders o");

	IF vehicle_team_ids IS NOT NULL AND vehicle_team_ids != "" THEN
		SET query = CONCAT(query," ","INNER JOIN driver_vehicle_team dvt ON dvt.driver_id = o.primary_driver_id AND dvt.vehicle_team_id IN (",vehicle_team_ids,")");
	END IF;

	SET query = CONCAT(query," ","WHERE o.del_flag = 0 ");

	IF dayCondition = 1 THEN
		SET query = CONCAT(query," "," AND o.status IN(2,3,4,5,7) AND o.ETD_date BETWEEN '",from_date,"' AND '",to_date,"' ");
	ELSEIF dayCondition = 2 THEN
		SET query = CONCAT(query," "," AND o.status IN (4,5) AND o.ETD_date_reality BETWEEN '",from_date,"' AND '",to_date,"' ");
	ELSEIF dayCondition = 3 THEN
		SET query = CONCAT(query," "," AND o.status IN(2,3,4,5,7) AND o.ETA_date BETWEEN '",from_date,"' AND '",to_date,"' ");
	ELSE
		SET query = CONCAT(query," "," AND o.status = 5 AND o.ETA_date_reality BETWEEN '",from_date,"' AND '",to_date,"' ");
	END IF;

	IF driver_ids IS NOT NULL AND driver_ids != "" THEN
		SET query = CONCAT(query," ","AND o.primary_driver_id IN (",driver_ids,")");
	END IF;
	IF vehicle_ids IS NOT NULL AND vehicle_ids != "" THEN
		SET query = CONCAT(query," ","AND o.vehicle_id IN (",vehicle_ids,")");
	END IF;
	IF customer_ids IS NOT NULL AND customer_ids != "" THEN
		SET query = CONCAT(query," ","AND o.customer_id IN (",customer_ids,")");
	END IF;
	IF partner_ids IS NOT NULL AND partner_ids != "" THEN
		SET query = CONCAT(query," ","AND o.partner_id IN (",partner_ids,")");
	END IF;

	SET query = CONCAT(query," ",") tmp GROUP BY tmp.driver_id,tmp.date ) tmp2 ON tmp1.id = tmp2.driver_id AND tmp1.date = tmp2.date");

	SET query = CONCAT(query," ","WHERE tmp1.del_flag = 0 OR (tmp2.status_complete IS NOT NULL AND tmp2.status_complete != 0 )");

	 IF summary = 0 THEN
			SET @SQLText = CONCAT(query,' ','ORDER BY tmp1.full_name,tmp1.date');
			-- SELECT @SQLText;
			PREPARE stmt FROM @SQLText;
	 ELSE
			SET summaryQuery = "SELECT tmp3.date as date,SUM(tmp3.status_complete) as total";

			SET @SQLText = CONCAT(summaryQuery," FROM(", query,") tmp3 GROUP BY tmp3.date ORDER BY STR_TO_DATE(tmp3.date,'%d-%m-%Y')");
			PREPARE stmt FROM @SQLText;
	 END IF;

   EXECUTE stmt;
   DEALLOCATE PREPARE stmt;
END
;


-- ----------------------------
-- Procedure structure for proc_report_driver_by_income_monthly
-- ----------------------------
DROP PROCEDURE IF EXISTS `proc_report_driver_by_income_monthly`;

CREATE PROCEDURE `proc_report_driver_by_income_monthly`(IN summary BOOLEAN,IN vehicle_team_ids TEXT,
	IN vehicle_ids TEXT,IN driver_ids TEXT,IN customer_ids TEXT,IN from_date TEXT,IN to_date TEXT,IN dayCondition TEXT,In partner_ids TEXT)
BEGIN

	DECLARE query TEXT;
	DECLARE summaryQuery TEXT;

	SET query = "SELECT tmp1.id as entity_id, tmp1.full_name as entity_name,DATE_FORMAT(STR_TO_DATE(tmp1.date,'%Y-%m'),'%m-%Y') as date,tmp2.status_complete";

	SET query = CONCAT(query," ","FROM (SELECT d.id,d.full_name,cal.date,d.del_flag FROM ((SELECT dv.id,dv.full_name,dv.del_flag FROM drivers dv) UNION (SELECT 0 as id ,'*Chưa gán tài xế' as full_name,0 as del_flag)) d");

	IF vehicle_team_ids IS NOT NULL AND vehicle_team_ids != "" THEN
		SET query = CONCAT(query," ","LEFT JOIN driver_vehicle_team dvt ON dvt.driver_id = d.id");
	END IF;

	SET query = CONCAT(query," ","CROSS JOIN (select DATE_FORMAT(aDate,'%Y-%m') as date from (select @maxDate - interval (a.a + (10 * b.a) + (100 * c.a)) month as aDate from
																						(select 0 as a union all select 1 union all select 2 union all select 3 union all select 4 union all select 5 union all select 6 union all select 7
																						union all select 8 union all select 9) a,
																						(select 0 as a union all select 1 union all select 2 union all select 3 union all select 4 union all select 5 union all select 6 union all select 7
																						union all select 8 union all select 9) b,
																						(select 0 as a union all select 1 union all select 2 union all select 3 union all select 4 union all select 5 union all select 6 union all select 7
																						union all select 8 union all select 9) c,
																						(select @minDate := '",from_date,"', @maxDate := '",to_date,"') d) e
																						where DATE_FORMAT(aDate,'%Y-%m') BETWEEN DATE_FORMAT(@minDate, '%Y-%m') AND DATE_FORMAT(@maxDate, '%Y-%m')) cal WHERE 1=1");

	IF driver_ids IS NOT NULL AND driver_ids != "" THEN
		SET query = CONCAT(query," ","AND dv.id IN (",driver_ids,")");
	END IF;
	IF partner_ids IS NOT NULL AND partner_ids != "" THEN
		SET query = CONCAT(query," ","AND d.partner_id IN (",partner_ids,")");
	END IF;

	IF vehicle_team_ids IS NOT NULL AND vehicle_team_ids != "" THEN
		SET query = CONCAT(query," ","AND dvt.vehicle_team_id IN (",vehicle_team_ids,")");
	END IF;

	SET query = CONCAT(query," ",") tmp1 LEFT JOIN (SELECT tmp.date,tmp.driver_id,SUM(status_complete) as status_complete");

	SET query = CONCAT(query," ","FROM (SELECT DISTINCT o.id,
																(CASE WHEN (o.primary_driver_id IS NULL OR o.primary_driver_id = 0 ) THEN 0 ELSE o.primary_driver_id END) as driver_id,
																o.amount as status_complete , ");


	IF dayCondition = 1 THEN
		SET query = CONCAT(query," "," DATE_FORMAT(o.ETD_date,'%Y-%m') as date");
	ELSEIF dayCondition = 2 THEN
		SET query = CONCAT(query," "," DATE_FORMAT(o.ETD_date_reality,'%Y-%m') as date");
	ELSEIF dayCondition = 3 THEN
		SET query = CONCAT(query," "," DATE_FORMAT(o.ETA_date,'%Y-%m') as date");
	ELSE
		SET query = CONCAT(query," "," DATE_FORMAT(o.ETA_date_reality,'%Y-%m') as date");
	END IF;

	SET query = CONCAT(query," ","FROM orders o ");

	IF vehicle_team_ids IS NOT NULL AND vehicle_team_ids != "" THEN
		SET query = CONCAT(query," ","INNER JOIN driver_vehicle_team dvt ON dvt.driver_id = o.primary_driver_id AND dvt.vehicle_team_id IN (",vehicle_team_ids,")");
	END IF;

	SET query = CONCAT(query," ","WHERE o.del_flag = 0 ");

	IF dayCondition = 1 THEN
		SET query = CONCAT(query," "," AND o.status IN(2,3,4,5,7) AND DATE_FORMAT(o.ETD_date,'%Y-%m') BETWEEN DATE_FORMAT('",from_date,"','%Y-%m') AND DATE_FORMAT('",to_date,"','%Y-%m') ");
	ELSEIF dayCondition = 2 THEN
		SET query = CONCAT(query," "," AND o.status IN (4,5) AND DATE_FORMAT(o.ETD_date_reality,'%Y-%m') BETWEEN DATE_FORMAT('",from_date,"','%Y-%m') AND DATE_FORMAT('",to_date,"','%Y-%m') ");
	ELSEIF dayCondition = 3 THEN
		SET query = CONCAT(query," "," AND o.status IN(2,3,4,5,7) AND DATE_FORMAT(o.ETA_date,'%Y-%m') BETWEEN DATE_FORMAT('",from_date,"','%Y-%m') AND DATE_FORMAT('",to_date,"','%Y-%m') ");
	ELSE
		SET query = CONCAT(query," "," AND o.status = 5 AND DATE_FORMAT(o.ETA_date_reality,'%Y-%m') BETWEEN DATE_FORMAT('",from_date,"','%Y-%m') AND DATE_FORMAT('",to_date,"','%Y-%m') ");
	END IF;

	IF driver_ids IS NOT NULL AND driver_ids != "" THEN
		SET query = CONCAT(query," ","AND o.primary_driver_id IN (",driver_ids,")");
	END IF;
	IF vehicle_ids IS NOT NULL AND vehicle_ids != "" THEN
		SET query = CONCAT(query," ","AND o.vehicle_id IN (",vehicle_ids,")");
	END IF;
	IF customer_ids IS NOT NULL AND customer_ids != "" THEN
		SET query = CONCAT(query," ","AND o.customer_id IN (",customer_ids,")");
	END IF;
	IF partner_ids IS NOT NULL AND partner_ids != "" THEN
		SET query = CONCAT(query," ","AND o.partner_id IN (",partner_ids,")");
	END IF;

	SET query = CONCAT(query," ",") tmp GROUP BY tmp.driver_id,tmp.date ) tmp2 ON tmp1.id = tmp2.driver_id AND tmp1.date = tmp2.date");

	SET query = CONCAT(query," ","WHERE tmp1.del_flag = 0 OR (tmp2.status_complete IS NOT NULL AND tmp2.status_complete != 0 )");

	 IF summary = 0 THEN
			SET @SQLText = CONCAT(query,' ','ORDER BY tmp1.full_name,tmp1.date');
			-- SELECT @SQLText;
			PREPARE stmt FROM @SQLText;
	 ELSE
			SET summaryQuery = "SELECT tmp3.date as date,SUM(tmp3.status_complete) as total";

			SET @SQLText = CONCAT(summaryQuery," FROM(", query,") tmp3 GROUP BY tmp3.date ORDER BY STR_TO_DATE(tmp3.date,'%m-%Y')");
			PREPARE stmt FROM @SQLText;
	 END IF;

   EXECUTE stmt;
   DEALLOCATE PREPARE stmt;
END
;


-- ----------------------------
-- Procedure structure for proc_report_driver_by_profit
-- ----------------------------
DROP PROCEDURE IF EXISTS `proc_report_driver_by_profit`;

CREATE PROCEDURE `proc_report_driver_by_profit`(IN summary BOOLEAN,IN vehicle_team_ids TEXT,
	IN vehicle_ids TEXT,IN driver_ids TEXT,IN customer_ids TEXT,IN from_date TEXT,IN to_date TEXT,IN dayCondition TEXT,In partner_ids TEXT)
BEGIN

	DECLARE query TEXT;
	DECLARE summaryQuery TEXT;

	SET query = "SELECT tmp1.id as entity_id, tmp1.full_name as entity_name,DATE_FORMAT(tmp1.date,'%d-%m-%Y') as date,
								((CASE WHEN tmp2.income IS NULL THEN 0 ELSE tmp2.income END) - (CASE WHEN tmp3.cost IS NULL THEN 0 ELSE tmp3.cost END)) as status_complete";

	SET query = CONCAT(query," ","FROM (SELECT d.id,d.full_name,cal.date,d.del_flag FROM ((SELECT dv.id,dv.full_name,dv.del_flag FROM drivers dv) UNION (SELECT 0 as id ,'*Chưa gán tài xế' as full_name,0 as del_flag)) d");

	IF vehicle_team_ids IS NOT NULL AND vehicle_team_ids != "" THEN
		SET query = CONCAT(query," ","LEFT JOIN driver_vehicle_team dvt ON dvt.driver_id = d.id");
	END IF;

	SET query = CONCAT(query," ","CROSS JOIN (
										SELECT selected_date as date FROM (
													SELECT adddate('1970-01-01',t4.i*10000 + t3.i*1000 + t2.i*100 + t1.i*10 + t0.i) selected_date
													FROM
														 (SELECT 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t0,
														 (SELECT 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t1,
														 (SELECT 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t2,
														 (SELECT 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t3,
														 (SELECT 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t4
										) d
										WHERE selected_date BETWEEN '",from_date,"' AND '",to_date,"'
										) cal WHERE 1=1");

	IF driver_ids IS NOT NULL AND driver_ids != "" THEN
		SET query = CONCAT(query," ","AND d.id IN (",driver_ids,")");
	END IF;

	IF vehicle_team_ids IS NOT NULL AND vehicle_team_ids != "" THEN
		SET query = CONCAT(query," ","AND dvt.vehicle_team_id IN (",vehicle_team_ids,")");
	END IF;

	IF partner_ids IS NOT NULL AND partner_ids != "" THEN
		SET query = CONCAT(query," ","AND d.partner_id IN (",partner_ids,")");
	END IF;

	SET query = CONCAT(query," ",") tmp1 LEFT JOIN (SELECT tmp.date,tmp.driver_id,SUM(status_complete) as income");

	SET query = CONCAT(query," ","FROM (SELECT DISTINCT o.id,
																(CASE WHEN (o.primary_driver_id IS NULL OR o.primary_driver_id = 0 ) THEN 0 ELSE o.primary_driver_id END) as driver_id,
																 o.amount as status_complete, ");

	IF dayCondition = 1 THEN
		SET query = CONCAT(query," "," o.ETD_date as date");
	ELSEIF dayCondition = 2 THEN
		SET query = CONCAT(query," "," o.ETD_date_reality as date");
	ELSEIF dayCondition = 3 THEN
		SET query = CONCAT(query," "," o.ETA_date as date");
	ELSE
		SET query = CONCAT(query," "," o.ETA_date_reality as date");
	END IF;

	SET query = CONCAT(query," ","FROM orders o ");

	IF vehicle_team_ids IS NOT NULL AND vehicle_team_ids != "" THEN
		SET query = CONCAT(query," ","INNER JOIN driver_vehicle_team dvt ON dvt.driver_id = o.primary_driver_id AND dvt.vehicle_team_id IN (",vehicle_team_ids,")");
	END IF;

	SET query = CONCAT(query," ","WHERE o.del_flag = 0 ");

	IF dayCondition = 1 THEN
		SET query = CONCAT(query," "," AND o.status IN(2,3,4,5,7) AND o.ETD_date BETWEEN '",from_date,"' AND '",to_date,"' ");
	ELSEIF dayCondition = 2 THEN
		SET query = CONCAT(query," "," AND o.status IN (4,5) AND o.ETD_date_reality BETWEEN '",from_date,"' AND '",to_date,"' ");
	ELSEIF dayCondition = 3 THEN
		SET query = CONCAT(query," "," AND o.status IN(2,3,4,5,7) AND o.ETA_date BETWEEN '",from_date,"' AND '",to_date,"' ");
	ELSE
		SET query = CONCAT(query," "," AND o.status = 5 AND o.ETA_date_reality BETWEEN '",from_date,"' AND '",to_date,"' ");
	END IF;

	IF driver_ids IS NOT NULL AND driver_ids != "" THEN
		SET query = CONCAT(query," ","AND o.primary_driver_id IN (",driver_ids,")");
	END IF;
	IF vehicle_ids IS NOT NULL AND vehicle_ids != "" THEN
		SET query = CONCAT(query," ","AND o.vehicle_id IN (",vehicle_ids,")");
	END IF;
	IF customer_ids IS NOT NULL AND customer_ids != "" THEN
		SET query = CONCAT(query," ","AND o.customer_id IN (",customer_ids,")");
	END IF;
	IF partner_ids IS NOT NULL AND partner_ids != "" THEN
		SET query = CONCAT(query," ","AND o.partner_id IN (",partner_ids,")");
	END IF;

	SET query = CONCAT(query," ",") tmp GROUP BY tmp.driver_id,tmp.date ) tmp2 ON tmp1.id = tmp2.driver_id AND tmp1.date = tmp2.date");

	SET query = CONCAT(query," ","LEFT JOIN (SELECT tmp.date, tmp.driver_id, SUM(tmp.final_cost) as cost");

	SET query = CONCAT(query," ","FROM (SELECT DISTINCT r.id,
																(CASE WHEN (r.driver_id IS NULL OR r.driver_id = 0 ) THEN 0 ELSE r.driver_id END) as driver_id,
																r.final_cost");
	IF dayCondition = 1 THEN
		SET query = CONCAT(query," ",",r.ETD_date as date");
	ELSEIF dayCondition = 2 THEN
		SET query = CONCAT(query," ",",r.ETD_date_reality as date");
	ELSEIF dayCondition = 3 THEN
		SET query = CONCAT(query," ",",r.ETA_date as date");
	ELSE
		SET query = CONCAT(query," ",",r.ETA_date_reality as date");
	END IF;

	SET query = CONCAT(query," ","FROM routes r
																LEFT JOIN ( SELECT DISTINCT ro.route_id, o.customer_id FROM route_order ro
																						INNER JOIN orders o ON o.id = ro.order_id WHERE ro.del_flag = 0 AND o.del_flag = 0
																					) a ON a.route_id = r.id
																LEFT JOIN driver_vehicle_team dvt ON dvt.driver_id = r.driver_id
																WHERE r.del_flag = 0 AND r.is_approved = 1 AND r.route_status IN (1,0)");
	IF dayCondition = 1 THEN
		SET query = CONCAT(query," "," AND r.ETD_date IS NOT NULL AND r.ETD_date BETWEEN '",from_date,"' AND '",to_date,"' ");
	ELSEIF dayCondition = 2 THEN
		SET query = CONCAT(query," "," AND r.ETD_date_reality IS NOT NULL AND r.ETD_date_reality BETWEEN '",from_date,"' AND '",to_date,"' ");
	ELSEIF dayCondition = 3 THEN
		SET query = CONCAT(query," "," AND r.ETA_date IS NOT NULL AND r.ETA_date BETWEEN '",from_date,"' AND '",to_date,"' ");
	ELSE
		SET query = CONCAT(query," "," AND r.ETA_date_reality IS NOT NULL AND r.ETA_date_reality BETWEEN '",from_date,"' AND '",to_date,"' ");
	END IF;

	IF vehicle_team_ids IS NOT NULL AND vehicle_team_ids != "" THEN
		SET query = CONCAT(query," ","AND dvt.vehicle_team_id IN (",vehicle_team_ids,")");
	END IF;
	IF driver_ids IS NOT NULL AND driver_ids != "" THEN
		SET query = CONCAT(query," ","AND r.driver_id IN (",driver_ids,")");
	END IF;
	IF vehicle_ids IS NOT NULL AND vehicle_ids != "" THEN
		SET query = CONCAT(query," ","AND r.vehicle_id IN (",vehicle_ids,")");
	END IF;
	IF customer_ids IS NOT NULL AND customer_ids != "" THEN
		SET query = CONCAT(query," ","AND a.customer_id IN (",customer_ids,")");
	END IF;
	IF partner_ids IS NOT NULL AND partner_ids != "" THEN
		SET query = CONCAT(query," ","AND r.partner_id IN (",partner_ids,")");
	END IF;

	SET query = CONCAT(query," ",") tmp GROUP BY tmp.driver_id, DATE_FORMAT(tmp.date,'%Y-%m-%d')
																) tmp3 ON tmp1.id = tmp3.driver_id AND tmp1.date = tmp3.date ");

	SET query = CONCAT(query," ","WHERE tmp1.del_flag = 0 OR ((CASE WHEN tmp2.income IS NULL THEN 0 ELSE tmp2.income END) - (CASE WHEN tmp3.cost IS NULL THEN 0 ELSE tmp3.cost END)) != 0 ");

	 IF summary = 0 THEN
			SET @SQLText = CONCAT(query,' ','ORDER BY tmp1.full_name,tmp1.date');
			-- SELECT @SQLText;
			PREPARE stmt FROM @SQLText;
	 ELSE
			SET summaryQuery = "SELECT tmp4.date as date,SUM(tmp4.status_complete) as total";

			SET @SQLText = CONCAT(summaryQuery," FROM(", query,") tmp4 GROUP BY tmp4.date ORDER BY STR_TO_DATE(tmp4.date,'%d-%m-%Y')");
			PREPARE stmt FROM @SQLText;
	 END IF;

   EXECUTE stmt;
   DEALLOCATE PREPARE stmt;
END
;


-- ----------------------------
-- Procedure structure for proc_report_driver_by_profit_monthly
-- ----------------------------
DROP PROCEDURE IF EXISTS `proc_report_driver_by_profit_monthly`;

CREATE PROCEDURE `proc_report_driver_by_profit_monthly`(IN summary BOOLEAN,IN vehicle_team_ids TEXT,
	IN vehicle_ids TEXT,IN driver_ids TEXT,IN customer_ids TEXT,IN from_date TEXT,IN to_date TEXT,IN dayCondition TEXT,IN partner_ids TEXT)
BEGIN

	DECLARE query TEXT;
	DECLARE summaryQuery TEXT;

	SET query = "SELECT tmp1.id as entity_id, tmp1.full_name as entity_name,DATE_FORMAT(STR_TO_DATE(tmp1.date,'%Y-%m'),'%m-%Y') as date,
								((CASE WHEN tmp2.income IS NULL THEN 0 ELSE tmp2.income END) - (CASE WHEN tmp3.cost IS NULL THEN 0 ELSE tmp3.cost END)) as status_complete";

	SET query = CONCAT(query," ","FROM (SELECT d.id,d.full_name,cal.date,d.del_flag FROM ((SELECT dv.id,dv.full_name,dv.del_flag FROM drivers dv) UNION (SELECT 0 as id ,'*Chưa gán tài xế' as full_name,0 as del_flag)) d");

	IF vehicle_team_ids IS NOT NULL AND vehicle_team_ids != "" THEN
		SET query = CONCAT(query," ","LEFT JOIN driver_vehicle_team dvt ON dvt.driver_id = d.id");
	END IF;

	SET query = CONCAT(query," ","CROSS JOIN (select DATE_FORMAT(aDate,'%Y-%m') as date from (select @maxDate - interval (a.a + (10 * b.a) + (100 * c.a)) month as aDate from
																						(select 0 as a union all select 1 union all select 2 union all select 3 union all select 4 union all select 5 union all select 6 union all select 7
																						union all select 8 union all select 9) a,
																						(select 0 as a union all select 1 union all select 2 union all select 3 union all select 4 union all select 5 union all select 6 union all select 7
																						union all select 8 union all select 9) b,
																						(select 0 as a union all select 1 union all select 2 union all select 3 union all select 4 union all select 5 union all select 6 union all select 7
																						union all select 8 union all select 9) c,
																						(select @minDate := '",from_date,"', @maxDate := '",to_date,"') d) e
																						where DATE_FORMAT(aDate,'%Y-%m') BETWEEN DATE_FORMAT(@minDate, '%Y-%m') AND DATE_FORMAT(@maxDate, '%Y-%m')) cal WHERE 1=1");

	IF driver_ids IS NOT NULL AND driver_ids != "" THEN
		SET query = CONCAT(query," ","AND dv.id IN (",driver_ids,")");
	END IF;

	IF vehicle_team_ids IS NOT NULL AND vehicle_team_ids != "" THEN
		SET query = CONCAT(query," ","AND dvt.vehicle_team_id IN (",vehicle_team_ids,")");
	END IF;

	IF partner_ids IS NOT NULL AND partner_ids != "" THEN
		SET query = CONCAT(query," ","AND d.partner_id IN (",partner_ids,")");
	END IF;

	SET query = CONCAT(query," ",") tmp1 LEFT JOIN (SELECT tmp.date,tmp.driver_id,SUM(status_complete) as income");

SET query = CONCAT(query," ","FROM (SELECT DISTINCT o.id,
																(CASE WHEN (o.primary_driver_id IS NULL OR o.primary_driver_id = 0 ) THEN 0 ELSE o.primary_driver_id END) as driver_id,
																o.amount as status_complete , ");


	IF dayCondition = 1 THEN
		SET query = CONCAT(query," "," DATE_FORMAT(o.ETD_date,'%Y-%m') as date");
	ELSEIF dayCondition = 2 THEN
		SET query = CONCAT(query," "," DATE_FORMAT(o.ETD_date_reality,'%Y-%m') as date");
	ELSEIF dayCondition = 3 THEN
		SET query = CONCAT(query," "," DATE_FORMAT(o.ETA_date,'%Y-%m') as date");
	ELSE
		SET query = CONCAT(query," "," DATE_FORMAT(o.ETA_date_reality,'%Y-%m') as date");
	END IF;

	SET query = CONCAT(query," ","FROM orders o ");

	IF vehicle_team_ids IS NOT NULL AND vehicle_team_ids != "" THEN
		SET query = CONCAT(query," ","INNER JOIN driver_vehicle_team dvt ON dvt.driver_id = o.primary_driver_id AND dvt.vehicle_team_id IN (",vehicle_team_ids,")");
	END IF;

	SET query = CONCAT(query," ","WHERE o.del_flag = 0 ");

	IF dayCondition = 1 THEN
		SET query = CONCAT(query," "," AND o.status IN(2,3,4,5,7) AND DATE_FORMAT(o.ETD_date,'%Y-%m') BETWEEN DATE_FORMAT('",from_date,"','%Y-%m') AND DATE_FORMAT('",to_date,"','%Y-%m') ");
	ELSEIF dayCondition = 2 THEN
		SET query = CONCAT(query," "," AND o.status IN (4,5) AND DATE_FORMAT(o.ETD_date_reality,'%Y-%m') BETWEEN DATE_FORMAT('",from_date,"','%Y-%m') AND DATE_FORMAT('",to_date,"','%Y-%m') ");
	ELSEIF dayCondition = 3 THEN
		SET query = CONCAT(query," "," AND o.status IN(2,3,4,5,7) AND DATE_FORMAT(o.ETA_date,'%Y-%m') BETWEEN DATE_FORMAT('",from_date,"','%Y-%m') AND DATE_FORMAT('",to_date,"','%Y-%m') ");
	ELSE
		SET query = CONCAT(query," "," AND o.status = 5 AND DATE_FORMAT(o.ETA_date_reality,'%Y-%m') BETWEEN DATE_FORMAT('",from_date,"','%Y-%m') AND DATE_FORMAT('",to_date,"','%Y-%m') ");
	END IF;

	IF driver_ids IS NOT NULL AND driver_ids != "" THEN
		SET query = CONCAT(query," ","AND o.primary_driver_id IN (",driver_ids,")");
	END IF;
	IF vehicle_ids IS NOT NULL AND vehicle_ids != "" THEN
		SET query = CONCAT(query," ","AND o.vehicle_id IN (",vehicle_ids,")");
	END IF;
	IF customer_ids IS NOT NULL AND customer_ids != "" THEN
		SET query = CONCAT(query," ","AND o.customer_id IN (",customer_ids,")");
	END IF;
	IF partner_ids IS NOT NULL AND partner_ids != "" THEN
		SET query = CONCAT(query," ","AND o.partner_id IN (",partner_ids,")");
	END IF;

	SET query = CONCAT(query," ",") tmp GROUP BY tmp.driver_id,tmp.date ) tmp2 ON tmp1.id = tmp2.driver_id AND tmp1.date = tmp2.date");

	SET query = CONCAT(query," ","LEFT JOIN (SELECT DATE_FORMAT(tmp.date,'%Y-%m') date, tmp.driver_id, SUM(tmp.final_cost) as cost");

		SET query = CONCAT(query," ","FROM (SELECT DISTINCT r.id,
																	(CASE WHEN (r.driver_id IS NULL OR r.driver_id = 0 ) THEN 0 ELSE r.driver_id END) as driver_id,
																	r.final_cost");
		IF dayCondition = 1 THEN
			SET query = CONCAT(query," ",",r.ETD_date as date");
		ELSEIF dayCondition = 2 THEN
			SET query = CONCAT(query," ",",r.ETD_date_reality as date");
		ELSEIF dayCondition = 3 THEN
			SET query = CONCAT(query," ",",r.ETA_date as date");
		ELSE
			SET query = CONCAT(query," ",",r.ETA_date_reality as date");
		END IF;

		SET query = CONCAT(query," ","FROM routes r
																	LEFT JOIN ( SELECT DISTINCT ro.route_id, o.customer_id FROM route_order ro
																							INNER JOIN orders o ON o.id = ro.order_id WHERE ro.del_flag = 0 AND o.del_flag = 0
																						) a ON a.route_id = r.id
																	LEFT JOIN driver_vehicle_team dvt ON dvt.driver_id = r.driver_id
																	WHERE r.del_flag = 0 AND r.is_approved = 1 AND r.route_status IN (1,0)");
		IF dayCondition = 1 THEN
			SET query = CONCAT(query," "," AND r.ETD_date IS NOT NULL AND DATE_FORMAT(r.ETD_date,'%Y-%m') BETWEEN DATE_FORMAT('",from_date,"', '%Y-%m') AND DATE_FORMAT('",to_date,"', '%Y-%m')");
		ELSEIF dayCondition = 2 THEN
			SET query = CONCAT(query," "," AND r.ETD_date_reality IS NOT NULL AND DATE_FORMAT(r.ETD_date_reality,'%Y-%m') BETWEEN DATE_FORMAT('",from_date,"', '%Y-%m') AND DATE_FORMAT('",to_date,"', '%Y-%m')");
		ELSEIF dayCondition = 3 THEN
			SET query = CONCAT(query," "," AND r.ETA_date IS NOT NULL AND DATE_FORMAT(r.ETA_date,'%Y-%m') BETWEEN DATE_FORMAT('",from_date,"', '%Y-%m') AND DATE_FORMAT('",to_date,"', '%Y-%m')");
		ELSE
			SET query = CONCAT(query," "," AND r.ETA_date_reality IS NOT NULL AND DATE_FORMAT(r.ETA_date_reality,'%Y-%m') BETWEEN DATE_FORMAT('",from_date,"', '%Y-%m') AND DATE_FORMAT('",to_date,"', '%Y-%m')");
		END IF;

		IF vehicle_team_ids IS NOT NULL AND vehicle_team_ids != "" THEN
			SET query = CONCAT(query," ","AND dvt.vehicle_team_id IN (",vehicle_team_ids,")");
		END IF;
		IF driver_ids IS NOT NULL AND driver_ids != "" THEN
			SET query = CONCAT(query," ","AND r.driver_id IN (",driver_ids,")");
		END IF;
		IF vehicle_ids IS NOT NULL AND vehicle_ids != "" THEN
			SET query = CONCAT(query," ","AND r.vehicle_id IN (",vehicle_ids,")");
		END IF;
		IF customer_ids IS NOT NULL AND customer_ids != "" THEN
			SET query = CONCAT(query," ","AND a.customer_id IN (",customer_ids,")");
		END IF;
		IF partner_ids IS NOT NULL AND partner_ids != "" THEN
			SET query = CONCAT(query," ","AND r.partner_id IN (",partner_ids,")");
		END IF;

		SET query = CONCAT(query," ",") tmp GROUP BY tmp.driver_id, DATE_FORMAT(tmp.date,'%Y-%m')
																	) tmp3 ON tmp1.id = tmp3.driver_id AND tmp1.date = tmp3.date ");

		SET query = CONCAT(query," ","WHERE tmp1.del_flag = 0 OR ((CASE WHEN tmp2.income IS NULL THEN 0 ELSE tmp2.income END) - (CASE WHEN tmp3.cost IS NULL THEN 0 ELSE tmp3.cost END)) != 0 ");

	 IF summary = 0 THEN
			SET @SQLText = CONCAT(query,' ','ORDER BY tmp1.full_name,tmp1.date');
			-- SELECT @SQLText;
			PREPARE stmt FROM @SQLText;
	 ELSE
			SET summaryQuery = "SELECT tmp4.date as date,SUM(tmp4.status_complete) as total";

			SET @SQLText = CONCAT(summaryQuery," FROM(", query,") tmp4 GROUP BY tmp4.date ORDER BY STR_TO_DATE(tmp4.date,'%m-%Y')");
			PREPARE stmt FROM @SQLText;
	 END IF;

   EXECUTE stmt;
   DEALLOCATE PREPARE stmt;
END
;


-- ----------------------------
-- Procedure structure for proc_report_driver_by_turn
-- ----------------------------
DROP PROCEDURE IF EXISTS `proc_report_driver_by_turn`;

CREATE PROCEDURE `proc_report_driver_by_turn`(IN summary BOOLEAN,IN vehicle_team_ids TEXT,
	IN vehicle_ids TEXT,IN driver_ids TEXT,
	IN customer_ids TEXT,IN status_all BOOLEAN,IN status_incomplete BOOLEAN,
	IN status_complete BOOLEAN,IN status_cancel BOOLEAN,IN from_date TEXT,IN to_date TEXT, IN partner_ids TEXT)
BEGIN

	DECLARE query TEXT;
	DECLARE summaryQuery TEXT;

	SET query = "SELECT tmp1.id as entity_id, tmp1.full_name as entity_name,DATE_FORMAT(tmp1.date,'%d-%m-%Y') as date";
		IF status_all = 1 THEN SET query = CONCAT(query,",","tmp2.status_all"); END IF;
		IF status_complete = 1 THEN
			SET query = CONCAT(query,",","tmp2.status_complete");
			SET query = CONCAT(query,",","tmp2.status_on_time");
			SET query = CONCAT(query,",","tmp2.status_late");
		END IF;
		IF status_incomplete = 1 THEN SET query = CONCAT(query,",","tmp2.status_incomplete"); END IF;
		IF status_cancel  = 1 THEN SET query = CONCAT(query,",","tmp2.status_cancel "); END IF;

	SET query = CONCAT(query," ","FROM (SELECT d.id,d.full_name,cal.date,d.del_flag FROM ((SELECT dv.id,dv.full_name,dv.del_flag,dv.partner_id FROM drivers dv) UNION (SELECT 0 as id ,'*Chưa gán tài xế' as full_name,0 as del_flag, 0 as partner_id)) d");

	IF vehicle_team_ids IS NOT NULL AND vehicle_team_ids != "" THEN
		SET query = CONCAT(query," ","LEFT JOIN driver_vehicle_team dvt ON dvt.driver_id = d.id");
	END IF;

	SET query = CONCAT(query," ","CROSS JOIN (
										SELECT selected_date as date FROM (
													SELECT adddate('1970-01-01',t4.i*10000 + t3.i*1000 + t2.i*100 + t1.i*10 + t0.i) selected_date
													FROM
														 (SELECT 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t0,
														 (SELECT 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t1,
														 (SELECT 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t2,
														 (SELECT 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t3,
														 (SELECT 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t4
										) d
										WHERE selected_date BETWEEN '",from_date,"' AND '",to_date,"'
										) cal
										WHERE 1=1 ");

	IF driver_ids IS NOT NULL AND driver_ids != "" THEN
		SET query = CONCAT(query," ","AND d.id IN (",driver_ids,")");
	END IF;

	IF vehicle_team_ids IS NOT NULL AND vehicle_team_ids != "" THEN
		SET query = CONCAT(query," ","AND dvt.vehicle_team_id IN (",vehicle_team_ids,")");
	END IF;

	IF partner_ids IS NOT NULL AND partner_ids != "" THEN
		SET query = CONCAT(query," ","AND d.partner_id IN (",partner_ids,")");
	END IF;

	SET query = CONCAT(query," ",") tmp1 LEFT JOIN (SELECT tmp.date,tmp.driver_id");

		IF status_all = 1 THEN SET query = CONCAT(query,",","(SUM(status_complete) + SUM(status_incomplete) + SUM(status_cancel))  as status_all"); END IF;
		IF status_complete = 1 THEN
			SET query = CONCAT(query,",","SUM(status_complete) as status_complete");
			SET query = CONCAT(query,",","SUM(status_on_time) as status_on_time");
			SET query = CONCAT(query,",","SUM(status_late) as status_late");
		END IF;
		IF status_incomplete = 1 THEN SET query = CONCAT(query,",","SUM(status_incomplete) as status_incomplete"); END IF;
		IF status_cancel = 1 THEN SET query = CONCAT(query,",","SUM(status_cancel) as status_cancel"); END IF;

	SET query = CONCAT(query," ","FROM (SELECT DISTINCT o.id, (CASE WHEN (o.ETA_date_reality IS NOT NULL AND o.status = 5) THEN o.ETA_date_reality ELSE o.ETA_date END) date,
																						(CASE WHEN (o.primary_driver_id IS NULL OR o.primary_driver_id = 0 ) THEN 0 ELSE o.primary_driver_id END) as driver_id");
				IF status_complete = 1 THEN
			SET query = CONCAT(query,",","(CASE WHEN (o.ETA_date_reality IS NOT NULL AND o.status = 5) THEN 1 ELSE 0 END) as status_complete");
			SET query = CONCAT(query,",","(CASE WHEN (o.ETA_date_reality IS NOT NULL AND o.status = 5
																	AND DATE_FORMAT(CONCAT(o.ETA_date,' ',o.ETA_time), '%Y-%m-%d %H:%i') >= DATE_FORMAT(CONCAT(o.ETA_date_reality,' ',o.ETA_time_reality)
																	, '%Y-%m-%d %H:%i')) THEN 1 ELSE 0 END) as status_on_time");
			SET query = CONCAT(query,",","(CASE WHEN (o.ETA_date_reality IS NOT NULL AND o.status = 5
																			AND DATE_FORMAT(CONCAT(o.ETA_date,' ',o.ETA_time), '%Y-%m-%d %H:%i') < DATE_FORMAT(CONCAT(o.ETA_date_reality,' ',o.ETA_time_reality)
																			, '%Y-%m-%d %H:%i')) THEN 1 ELSE 0 END) as status_late");
		ELSE
			SET query = CONCAT(query,",","0 as status_complete");
		END IF;
		IF status_incomplete = 1 THEN SET query = CONCAT(query,",","(CASE WHEN o.status IN (2,3,4,7) THEN 1 ELSE 0 END) as status_incomplete");
		ELSE
			SET query = CONCAT(query,",","0 as status_incomplete");
		END IF;

		IF status_cancel = 1 THEN SET query = CONCAT(query,",","(CASE WHEN o.status = 6 THEN 1 ELSE 0 END) as status_cancel");
		ELSE
			SET query = CONCAT(query,",","0 as status_cancel");
		END IF;

	SET query = CONCAT(query," ","FROM orders o ");

	IF vehicle_team_ids IS NOT NULL AND vehicle_team_ids != "" THEN
		SET query = CONCAT(query," ","INNER JOIN driver_vehicle_team dvt ON dvt.driver_id = o.primary_driver_id AND dvt.vehicle_team_id IN (",vehicle_team_ids,")");
	END IF;

	SET query = CONCAT(query," ","WHERE o.del_flag = 0 AND o.status != 1 AND CASE WHEN o.ETA_date_reality IS NOT NULL AND o.status = 5
																																								THEN o.ETA_date_reality BETWEEN '",from_date,"' AND '",to_date,"'
																																								ELSE o.ETA_date BETWEEN '",from_date,"' AND '",to_date,"' END");

	IF driver_ids IS NOT NULL AND driver_ids != "" THEN
		SET query = CONCAT(query," ","AND o.primary_driver_id IN (",driver_ids,")");
	END IF;
	IF vehicle_ids IS NOT NULL AND vehicle_ids != "" THEN
		SET query = CONCAT(query," ","AND o.vehicle_id IN (",vehicle_ids,")");
	END IF;
	IF customer_ids IS NOT NULL AND customer_ids != "" THEN
		SET query = CONCAT(query," ","AND o.customer_id IN (",customer_ids,")");
	END IF;
	IF partner_ids IS NOT NULL AND partner_ids != "" THEN
		SET query = CONCAT(query," ","AND o.partner_id IN (",partner_ids,")");
	END IF;

	SET query = CONCAT(query," ",") tmp GROUP BY tmp.driver_id,tmp.date ) tmp2 ON tmp1.id = tmp2.driver_id AND tmp1.date = tmp2.date");

	SET query = CONCAT(query," ","WHERE tmp1.del_flag = 0 OR (tmp2.status_all IS NOT NULL AND tmp2.status_all != 0 )");

	 IF summary = 0 THEN
			SET @SQLText = CONCAT(query,' ','ORDER BY tmp1.full_name,tmp1.date');
			-- SELECT @SQLText;
			PREPARE stmt FROM @SQLText;
	 ELSE
			SET summaryQuery = "SELECT tmp3.date as date,";

			IF status_all = 1 THEN SET summaryQuery = CONCAT(summaryQuery,"SUM(tmp3.status_all) as total");
			ELSEIF status_incomplete = 1 THEN SET summaryQuery = CONCAT(summaryQuery,"SUM(tmp3.status_incomplete) as total");
			ELSEIF status_complete = 1 THEN SET summaryQuery = CONCAT(summaryQuery,"SUM(tmp3.status_complete) as total");
			ELSEIF status_cancel = 1 THEN SET summaryQuery = CONCAT(summaryQuery,"SUM(tmp3.status_cancel) as total");
			END IF;

			SET @SQLText = CONCAT(summaryQuery," FROM(", query,") tmp3 GROUP BY tmp3.date ORDER BY STR_TO_DATE(tmp3.date,'%d-%m-%Y')");
			PREPARE stmt FROM @SQLText;
	 END IF;

   EXECUTE stmt;
   DEALLOCATE PREPARE stmt;
END
;


-- ----------------------------
-- Procedure structure for proc_report_driver_by_turn_monthly
-- ----------------------------
DROP PROCEDURE IF EXISTS `proc_report_driver_by_turn_monthly`;

CREATE PROCEDURE `proc_report_driver_by_turn_monthly`(IN summary BOOLEAN,IN vehicle_team_ids TEXT,
	IN vehicle_ids TEXT,IN driver_ids TEXT,
	IN customer_ids TEXT,IN status_all BOOLEAN,IN status_incomplete BOOLEAN,
	IN status_complete BOOLEAN,IN status_cancel BOOLEAN,IN from_date TEXT,IN to_date TEXT, IN partner_ids TEXT)
BEGIN

	DECLARE query TEXT;
	DECLARE summaryQuery TEXT;

	SET query = "SELECT tmp1.id as entity_id, tmp1.full_name as entity_name,DATE_FORMAT(STR_TO_DATE(tmp1.date,'%Y-%m'),'%m-%Y') as date";
		IF status_all = 1 THEN SET query = CONCAT(query,",","tmp2.status_all"); END IF;
		IF status_complete = 1 THEN
			SET query = CONCAT(query,",","tmp2.status_complete");
			SET query = CONCAT(query,",","tmp2.status_on_time");
			SET query = CONCAT(query,",","tmp2.status_late");
		END IF;
		IF status_incomplete = 1 THEN SET query = CONCAT(query,",","tmp2.status_incomplete"); END IF;
		IF status_cancel  = 1 THEN SET query = CONCAT(query,",","tmp2.status_cancel"); END IF;

	SET query = CONCAT(query," ","FROM (SELECT d.id,d.full_name,cal.date,d.del_flag,d.partner_id FROM ((SELECT dv.id,dv.full_name,dv.del_flag,dv.partner_id FROM drivers dv WHERE dv.del_flag = 0) UNION (SELECT 0 as id ,'*Chưa gán tài xế' as full_name,0 as del_flag,0 as partner_id)) d");

	IF vehicle_team_ids IS NOT NULL AND vehicle_team_ids != "" THEN
		SET query = CONCAT(query," ","LEFT JOIN driver_vehicle_team dvt ON dvt.driver_id = d.id");
	END IF;

	SET query = CONCAT(query," ","CROSS JOIN (select DATE_FORMAT(aDate,'%Y-%m') as date from (select @maxDate - interval (a.a + (10 * b.a) + (100 * c.a)) month as aDate from
																						(select 0 as a union all select 1 union all select 2 union all select 3 union all select 4 union all select 5 union all select 6 union all select 7
																						union all select 8 union all select 9) a,
																						(select 0 as a union all select 1 union all select 2 union all select 3 union all select 4 union all select 5 union all select 6 union all select 7
																						union all select 8 union all select 9) b,
																						(select 0 as a union all select 1 union all select 2 union all select 3 union all select 4 union all select 5 union all select 6 union all select 7
																						union all select 8 union all select 9) c,
																						(select @minDate := '",from_date,"', @maxDate := '",to_date,"') d) e
																						where DATE_FORMAT(aDate,'%Y-%m') BETWEEN DATE_FORMAT(@minDate, '%Y-%m') AND DATE_FORMAT(@maxDate, '%Y-%m')) cal
																						WHERE 1=1");

	IF driver_ids IS NOT NULL AND driver_ids != "" THEN
		SET query = CONCAT(query," ","AND d.id IN (",driver_ids,")");
	END IF;

	IF vehicle_team_ids IS NOT NULL AND vehicle_team_ids != "" THEN
		SET query = CONCAT(query," ","AND dvt.vehicle_team_id IN (",vehicle_team_ids,")");
	END IF;

	IF partner_ids IS NOT NULL AND partner_ids != "" THEN
		SET query = CONCAT(query," ","AND d.partner_id IN (",partner_ids,")");
	END IF;

	SET query = CONCAT(query," ",") tmp1 LEFT JOIN (SELECT tmp.date,tmp.driver_id");

		IF status_all = 1 THEN SET query = CONCAT(query,",","(SUM(status_complete) + SUM(status_incomplete) + SUM(status_cancel))  as status_all"); END IF;
		IF status_complete = 1 THEN
			SET query = CONCAT(query,",","SUM(status_complete) as status_complete");
			SET query = CONCAT(query,",","SUM(status_on_time) as status_on_time");
			SET query = CONCAT(query,",","SUM(status_late) as status_late");
		END IF;
		IF status_incomplete = 1 THEN SET query = CONCAT(query,",","SUM(status_incomplete) as status_incomplete"); END IF;
		IF status_cancel = 1 THEN SET query = CONCAT(query,",","SUM(status_cancel) as status_cancel"); END IF;

	SET query = CONCAT(query," ","FROM (SELECT DISTINCT o.id, (CASE WHEN (o.ETA_date_reality IS NOT NULL AND o.status = 5) THEN DATE_FORMAT(o.ETA_date_reality,'%Y-%m') ELSE DATE_FORMAT(o.ETA_date,'%Y-%m') END) date,
																						(CASE WHEN (o.primary_driver_id IS NULL OR o.primary_driver_id = 0 ) THEN 0 ELSE o.primary_driver_id END) as driver_id");
		IF status_complete = 1 THEN
			SET query = CONCAT(query,",","(CASE WHEN (o.ETA_date_reality IS NOT NULL AND o.status = 5) THEN 1 ELSE 0 END) as status_complete");
			SET query = CONCAT(query,",","(CASE WHEN (o.ETA_date_reality IS NOT NULL AND o.status = 5
																	AND DATE_FORMAT(CONCAT(o.ETA_date,' ',o.ETA_time), '%Y-%m-%d %H:%i') >= DATE_FORMAT(CONCAT(o.ETA_date_reality,' ',o.ETA_time_reality)
																	, '%Y-%m-%d %H:%i')) THEN 1 ELSE 0 END) as status_on_time");
			SET query = CONCAT(query,",","(CASE WHEN (o.ETA_date_reality IS NOT NULL AND o.status = 5
																			AND DATE_FORMAT(CONCAT(o.ETA_date,' ',o.ETA_time), '%Y-%m-%d %H:%i') < DATE_FORMAT(CONCAT(o.ETA_date_reality,' ',o.ETA_time_reality)
																			, '%Y-%m-%d %H:%i')) THEN 1 ELSE 0 END) as status_late");
		ELSE
			SET query = CONCAT(query,",","0 as status_complete");
		END IF;
		IF status_incomplete = 1 THEN SET query = CONCAT(query,",","(CASE WHEN o.status IN (2,3,4,7) THEN 1 ELSE 0 END) as status_incomplete");
		ELSE
			SET query = CONCAT(query,",","0 as status_incomplete");
		END IF;

		IF status_cancel = 1 THEN SET query = CONCAT(query,",","(CASE WHEN o.status = 6 THEN 1 ELSE 0 END) as status_cancel");
		ELSE
			SET query = CONCAT(query,",","0 as status_cancel");
		END IF;

	SET query = CONCAT(query," ","FROM orders o ");

	IF vehicle_team_ids IS NOT NULL AND vehicle_team_ids != "" THEN
		SET query = CONCAT(query," ","INNER JOIN driver_vehicle_team dvt ON dvt.driver_id = o.primary_driver_id AND dvt.vehicle_team_id IN (",vehicle_team_ids,")");
	END IF;

	SET query = CONCAT(query," ","WHERE o.del_flag = 0 AND o.status != 1 AND CASE WHEN o.ETA_date_reality IS NOT NULL AND o.status = 5
																																								THEN o.ETA_date_reality BETWEEN '",from_date,"' AND '",to_date,"'
																																								ELSE o.ETA_date BETWEEN '",from_date,"' AND '",to_date,"' END");

	IF driver_ids IS NOT NULL AND driver_ids != "" THEN
		SET query = CONCAT(query," ","AND o.primary_driver_id IN (",driver_ids,")");
	END IF;
	IF vehicle_ids IS NOT NULL AND vehicle_ids != "" THEN
		SET query = CONCAT(query," ","AND o.vehicle_id IN (",vehicle_ids,")");
	END IF;
	IF customer_ids IS NOT NULL AND customer_ids != "" THEN
		SET query = CONCAT(query," ","AND o.customer_id IN (",customer_ids,")");
	END IF;
	IF partner_ids IS NOT NULL AND partner_ids != "" THEN
		SET query = CONCAT(query," ","AND o.partner_id IN (",partner_ids,")");
	END IF;

	SET query = CONCAT(query," ",") tmp GROUP BY tmp.driver_id,tmp.date ) tmp2 ON tmp1.id = tmp2.driver_id AND tmp1.date = tmp2.date");

	SET query = CONCAT(query," ","WHERE tmp1.del_flag = 0 OR (tmp2.status_all IS NOT NULL AND tmp2.status_all != 0 )");

	 IF summary = 0 THEN
			SET @SQLText = CONCAT(query,' ','ORDER BY tmp1.full_name,tmp1.date');
			-- SELECT @SQLText;
			PREPARE stmt FROM @SQLText;
	 ELSE
			SET summaryQuery = "SELECT tmp3.date as date,";

			IF status_all = 1 THEN SET summaryQuery = CONCAT(summaryQuery,"SUM(tmp3.status_all) as total");
			ELSEIF status_incomplete = 1 THEN SET summaryQuery = CONCAT(summaryQuery,"SUM(tmp3.status_incomplete) as total");
			ELSEIF status_complete = 1 THEN SET summaryQuery = CONCAT(summaryQuery,"SUM(tmp3.status_complete) as total");
			ELSEIF status_cancel = 1 THEN SET summaryQuery = CONCAT(summaryQuery,"SUM(tmp3.status_cancel) as total");
			END IF;

			SET @SQLText = CONCAT(summaryQuery," FROM(", query,") tmp3 GROUP BY tmp3.date ORDER BY STR_TO_DATE(tmp3.date,'%m-%Y')");
			PREPARE stmt FROM @SQLText;
	 END IF;

   EXECUTE stmt;
   DEALLOCATE PREPARE stmt;
END
;


-- ----------------------------
-- Procedure structure for proc_report_goods_by_time
-- ----------------------------
DROP PROCEDURE IF EXISTS `proc_report_goods_by_time`;

CREATE PROCEDURE `proc_report_goods_by_time`(IN from_date TEXT,IN to_date TEXT,IN dayCondition TEXT,IN partner_ids TEXT)
BEGIN

	DECLARE query TEXT;

	SET query = "SELECT DATE_FORMAT(tmp1.date,'%d-%m-%Y') as date, tmp2.volume, tmp2.weight";

	SET query = CONCAT(query," ","FROM (
										SELECT selected_date as date FROM (
													SELECT adddate('1970-01-01',t4.i*10000 + t3.i*1000 + t2.i*100 + t1.i*10 + t0.i) selected_date
													FROM
														 (SELECT 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t0,
														 (SELECT 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t1,
														 (SELECT 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t2,
														 (SELECT 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t3,
														 (SELECT 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t4
										) d
										WHERE selected_date BETWEEN '",from_date,"' AND '",to_date,"'
										) tmp1 ");


	SET query = CONCAT(query," ","LEFT JOIN (SELECT tmp.date, SUM(volume) as volume, SUM(weight) as weight");

	SET query = CONCAT(query," ","FROM (SELECT o.id, o.volume, o.weight, ");

	IF dayCondition = 1 THEN
		SET query = CONCAT(query," "," o.ETD_date as date");
	ELSEIF dayCondition = 2 THEN
		SET query = CONCAT(query," "," o.ETD_date_reality as date");
	ELSEIF dayCondition = 3 THEN
		SET query = CONCAT(query," "," o.ETA_date as date");
	ELSE
		SET query = CONCAT(query," "," o.ETA_date_reality as date");
	END IF;

	SET query = CONCAT(query," ","FROM order_customer as o");

	SET query = CONCAT(query," ","WHERE o.del_flag = 0 ");

	IF dayCondition = 1 THEN
		SET query = CONCAT(query," "," AND o.ETD_date BETWEEN '",from_date,"' AND '",to_date,"' ");
	ELSEIF dayCondition = 2 THEN
		SET query = CONCAT(query," "," AND o.ETD_date_reality BETWEEN '",from_date,"' AND '",to_date,"' ");
	ELSEIF dayCondition = 3 THEN
		SET query = CONCAT(query," "," AND o.ETA_date BETWEEN '",from_date,"' AND '",to_date,"' ");
	ELSE
		SET query = CONCAT(query," "," AND o.ETA_date_reality BETWEEN '",from_date,"' AND '",to_date,"' ");
	END IF;
	IF partner_ids IS NOT NULL AND partner_ids != "" THEN
		SET query = CONCAT(query," ","AND o.partner_id IN (",partner_ids,")");
	END IF;

	SET query = CONCAT(query," ",") tmp GROUP BY tmp.date ) tmp2 ON tmp1.date = tmp2.date");

	SET @SQLText = CONCAT(query,' ','ORDER BY tmp1.date');
	PREPARE stmt FROM @SQLText;
   	EXECUTE stmt;
   	DEALLOCATE PREPARE stmt;
END
;


-- ----------------------------
-- Procedure structure for proc_report_income_cost_profit_by_time
-- ----------------------------
DROP PROCEDURE IF EXISTS `proc_report_income_cost_profit_by_time`;

CREATE PROCEDURE `proc_report_income_cost_profit_by_time`(IN from_date TEXT,IN to_date TEXT,IN dayCondition TEXT, IN partner_ids TEXT)
BEGIN

	DECLARE query TEXT;

	SET query = "SELECT DATE_FORMAT(tmp1.date,'%d-%m-%Y') as date, tmp2.income, tmp3.cost,
							((CASE WHEN tmp2.income IS NULL THEN 0 ELSE tmp2.income END) - (CASE WHEN tmp3.cost IS NULL THEN 0 ELSE tmp3.cost END)) as profit";

	SET query = CONCAT(query," ","FROM (
										SELECT selected_date as date FROM (
													SELECT adddate('1970-01-01',t4.i*10000 + t3.i*1000 + t2.i*100 + t1.i*10 + t0.i) selected_date
													FROM
														 (SELECT 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t0,
														 (SELECT 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t1,
														 (SELECT 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t2,
														 (SELECT 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t3,
														 (SELECT 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t4
										) d
										WHERE selected_date BETWEEN '",from_date,"' AND '",to_date,"'
										) tmp1 ");


	SET query = CONCAT(query," ","LEFT JOIN (SELECT tmp.date, SUM(status_complete) as income");

	SET query = CONCAT(query," ","FROM (SELECT DISTINCT o.id, o.amount as status_complete , o.partner_id, ");

	IF dayCondition = 1 THEN
		SET query = CONCAT(query," "," o.ETD_date as date");
	ELSEIF dayCondition = 2 THEN
		SET query = CONCAT(query," "," o.ETD_date_reality as date");
	ELSEIF dayCondition = 3 THEN
		SET query = CONCAT(query," "," o.ETA_date as date");
	ELSE
		SET query = CONCAT(query," "," o.ETA_date_reality as date");
	END IF;

	SET query = CONCAT(query," ","FROM orders o ");

	SET query = CONCAT(query," ","WHERE o.del_flag = 0 ");

	IF dayCondition = 1 THEN
		SET query = CONCAT(query," "," AND o.status IN(2,3,4,5,7) AND o.ETD_date BETWEEN '",from_date,"' AND '",to_date,"' ");
	ELSEIF dayCondition = 2 THEN
		SET query = CONCAT(query," "," AND o.status IN (4,5) AND o.ETD_date_reality BETWEEN '",from_date,"' AND '",to_date,"' ");
	ELSEIF dayCondition = 3 THEN
		SET query = CONCAT(query," "," AND o.status IN(2,3,4,5,7) AND o.ETA_date BETWEEN '",from_date,"' AND '",to_date,"' ");
	ELSE
		SET query = CONCAT(query," "," AND o.status = 5 AND o.ETA_date_reality BETWEEN '",from_date,"' AND '",to_date,"' ");
	END IF;

	IF partner_ids IS NOT NULL AND partner_ids != "" THEN
		SET query = CONCAT(query," ","AND o.partner_id IN (",partner_ids,")");
	END IF;

	SET query = CONCAT(query," ",") tmp GROUP BY tmp.date ) tmp2 ON tmp1.date = tmp2.date");

	SET query = CONCAT(query," ","LEFT JOIN (SELECT tmp.date , SUM(tmp.final_cost) as cost");

		SET query = CONCAT(query," ","FROM (SELECT DISTINCT r.id, r.final_cost");

		IF dayCondition = 1 THEN
			SET query = CONCAT(query," ",",r.ETD_date as date");
		ELSEIF dayCondition = 2 THEN
			SET query = CONCAT(query," ",",r.ETD_date_reality as date");
		ELSEIF dayCondition = 3 THEN
			SET query = CONCAT(query," ",",r.ETA_date as date");
		ELSE
			SET query = CONCAT(query," ",",r.ETA_date_reality as date");
		END IF;

		SET query = CONCAT(query," ","FROM routes r WHERE r.del_flag = 0 AND r.is_approved = 1 AND r.route_status IN (1,0)");

		IF dayCondition = 1 THEN
			SET query = CONCAT(query," "," AND r.ETD_date IS NOT NULL AND r.ETD_date BETWEEN '",from_date,"' AND '",to_date,"' ");
		ELSEIF dayCondition = 2 THEN
			SET query = CONCAT(query," "," AND r.ETD_date_reality IS NOT NULL AND r.ETD_date_reality BETWEEN '",from_date,"' AND '",to_date,"' ");
		ELSEIF dayCondition = 3 THEN
			SET query = CONCAT(query," "," AND r.ETA_date IS NOT NULL AND r.ETA_date BETWEEN '",from_date,"' AND '",to_date,"' ");
		ELSE
			SET query = CONCAT(query," "," AND r.ETA_date_reality IS NOT NULL AND r.ETA_date_reality BETWEEN '",from_date,"' AND '",to_date,"' ");
		END IF;
		IF partner_ids IS NOT NULL AND partner_ids != "" THEN
			SET query = CONCAT(query," ","AND r.partner_id IN (",partner_ids,")");
		END IF;

		SET query = CONCAT(query," ",") tmp GROUP BY DATE_FORMAT(tmp.date,'%Y-%m-%d')
																	) tmp3 ON tmp1.date = tmp3.date ");


		SET @SQLText = CONCAT(query,' ','ORDER BY tmp1.date');
		-- SELECT @SQLText;
		PREPARE stmt FROM @SQLText;

   EXECUTE stmt;
   DEALLOCATE PREPARE stmt;
END
;


-- ----------------------------
-- Procedure structure for proc_report_operating_daily
-- ----------------------------
DROP PROCEDURE IF EXISTS `proc_report_operating_daily`;

CREATE PROCEDURE `proc_report_operating_daily`()
BEGIN

	DELETE FROM report_operating_daily;

	-- Thống kê theo số đơn hàng ngày hiện tại
	INSERT INTO report_operating_daily (id, label, partner_id, value, date, type, ins_id)
	SELECT tmp1.customer_id as id, tmp1.full_name as label, tmp1.partner_id, tmp1.total as value, 1, 1, 0
	FROM ( SELECT tmp.date, tmp.customer_id, c.full_name, tmp.partner_id, COUNT(DISTINCT tmp.id) as total
					FROM (SELECT (CASE WHEN o.status = 5 AND o.ETA_date_reality IS NOT NULL THEN o.ETA_date_reality ELSE o.ETA_date END) date
											,(CASE WHEN (o.customer_id IS NULL OR o.customer_id = 0 ) THEN 0 ELSE o.customer_id END) as customer_id
											,o.id
											,o.partner_id
								FROM orders o
								WHERE o.del_flag = 0 AND o.status != 1
											AND CASE WHEN o.ETA_date_reality IS NOT NULL AND o.status = 5
													THEN o.ETA_date_reality BETWEEN CURDATE() AND CURDATE()
													ELSE o.ETA_date BETWEEN CURDATE() AND CURDATE() END
								GROUP BY o.partner_id ) tmp
				LEFT JOIN customer c ON c.id = tmp.customer_id
				GROUP BY tmp.customer_id,tmp.date,tmp.partner_id ) tmp1;

 	-- Thống kê theo số đơn hàng ngày trước
 INSERT INTO report_operating_daily (id, label, partner_id, value, date, type, ins_id)
	SELECT tmp1.customer_id as id, tmp1.full_name as label, tmp1.partner_id, tmp1.total as value, 2, 1, 0
	FROM (SELECT tmp.date, tmp.customer_id, c.full_name, tmp.partner_id, COUNT(DISTINCT tmp.id) as total
				FROM (SELECT (CASE WHEN o.status = 5 AND o.ETA_date_reality IS NOT NULL THEN o.ETA_date_reality ELSE o.ETA_date END) date
										,(CASE WHEN (o.customer_id IS NULL OR o.customer_id = 0 ) THEN 0 ELSE o.customer_id END) as customer_id
										,o.id
										,o.partner_id
							FROM orders o
							WHERE o.del_flag = 0 AND o.status != 1
										AND CASE WHEN o.ETA_date_reality IS NOT NULL AND o.status = 5
												THEN o.ETA_date_reality BETWEEN SUBDATE(CURDATE(), INTERVAL 1 DAY) AND SUBDATE(CURDATE(), INTERVAL 1 DAY)
												ELSE o.ETA_date BETWEEN SUBDATE(CURDATE(), INTERVAL 1 DAY) AND SUBDATE(CURDATE(), INTERVAL 1 DAY) END
				GROUP BY o.partner_id ) tmp
				LEFT JOIN customer c ON c.id = tmp.customer_id
				GROUP BY tmp.customer_id,tmp.date,tmp.partner_id) tmp1;

  -- Thống kê theo doanh thu ngày hiện tại
	INSERT INTO report_operating_daily (partner_id, amount_ETD, amount_ETD_reality, amount_ETA, amount_ETA_reality, date, type, ins_id)
		SELECT tmp.partner_id, tmp.amount_ETD, tmp.amount_ETD_reality, tmp.amount_ETA, tmp.amount_ETA_reality, 1, 2 , 0
		FROM (SELECT CURDATE() as date
								,SUM(CASE WHEN o.ETD_date = CURDATE() THEN o.amount ELSE 0 END) as amount_ETD
								,SUM(CASE WHEN o.ETD_date_reality = CURDATE() THEN o.amount ELSE 0 END) as amount_ETD_reality
								,SUM(CASE WHEN o.ETA_date = CURDATE() THEN o.amount ELSE 0 END) as amount_ETA
								,SUM(CASE WHEN o.ETA_date_reality = CURDATE() THEN o.amount ELSE 0 END) as amount_ETA_reality
								,o.partner_id
					FROM orders o
					WHERE o.del_flag = 0 AND o.status IN (2,3,4,5,7)
								AND ( o.ETD_date = CURDATE() OR o.ETD_date_reality = CURDATE()
											OR o.ETA_date = CURDATE() OR o.ETA_date_reality = CURDATE() )
					GROUP BY o.partner_id) tmp ;

	 -- Thống kê theo doanh thu ngày trước
	INSERT INTO report_operating_daily (partner_id, amount_ETD, amount_ETD_reality, amount_ETA, amount_ETA_reality, date, type, ins_id)
		SELECT tmp.partner_id, tmp.amount_ETD, tmp.amount_ETD_reality, tmp.amount_ETA, tmp.amount_ETA_reality, 2, 2, 0
		FROM (	SELECT SUBDATE(CURDATE(), INTERVAL 1 DAY) as date
										,(CASE WHEN (o.customer_id IS NULL OR o.customer_id = 0 ) THEN 0 ELSE o.customer_id END) as customer_id
										,SUM(CASE WHEN o.ETD_date = SUBDATE(CURDATE(), INTERVAL 1 DAY) THEN o.amount ELSE 0 END) as amount_ETD
										,SUM(CASE WHEN o.ETD_date_reality = SUBDATE(CURDATE(), INTERVAL 1 DAY) THEN o.amount ELSE 0 END) as amount_ETD_reality
										,SUM(CASE WHEN o.ETA_date = SUBDATE(CURDATE(), INTERVAL 1 DAY) THEN o.amount ELSE 0 END) as amount_ETA
										,SUM(CASE WHEN o.ETA_date_reality = SUBDATE(CURDATE(), INTERVAL 1 DAY) THEN o.amount ELSE 0 END) as amount_ETA_reality
										,o.partner_id
						FROM orders o
						WHERE o.del_flag = 0 AND o.status IN (2,3,4,5,7)
									AND ( o.ETD_date = SUBDATE(CURDATE(), INTERVAL 1 DAY) OR o.ETD_date_reality = SUBDATE(CURDATE(), INTERVAL 1 DAY)
												OR o.ETA_date = SUBDATE(CURDATE(), INTERVAL 1 DAY) OR o.ETA_date_reality = SUBDATE(CURDATE(), INTERVAL 1 DAY) )
						GROUP BY o.partner_id) tmp;

	COMMIT;

END
;


-- ----------------------------
-- Procedure structure for proc_report_orders_amount_by_customer_and_time
-- ----------------------------
DROP PROCEDURE IF EXISTS `proc_report_orders_amount_by_customer_and_time`;

CREATE PROCEDURE `proc_report_orders_amount_by_customer_and_time`(IN customer_id int, IN from_date date, IN to_date date)
BEGIN
                CREATE TEMPORARY TABLE IF NOT EXISTS `temp_day`
                SELECT DATE_ADD(from_date, interval N DAY) AS `order_date`
                FROM tally_number
                WHERE DATE_ADD(from_date, interval N DAY) <= to_date;

                SELECT
                    sum(IFNULL(o.amount, 0)) AS `amount`,
                    td.`order_date`
                FROM temp_day td
                LEFT JOIN orders o on (o.`order_date` = td.`order_date` and o.customer_id = customer_id)
                WHERE o.del_flag = '0'
                OR o.id IS NULL
                GROUP BY td.`order_date`
                ORDER BY td.`order_date`;
            END
;


-- ----------------------------
-- Procedure structure for proc_report_orders_by_customer_and_time
-- ----------------------------
DROP PROCEDURE IF EXISTS `proc_report_orders_by_customer_and_time`;

CREATE PROCEDURE `proc_report_orders_by_customer_and_time`(IN customer_id INT,
	IN from_date date,
	IN to_date date)
BEGIN
	CREATE TEMPORARY TABLE
	IF
		NOT EXISTS `temp_day` SELECT
		DATE_ADD( from_date, INTERVAL N DAY ) AS `order_date`
	FROM
		tally_number
	WHERE
		DATE_ADD( from_date, INTERVAL N DAY ) <= to_date;
	SELECT
		COUNT( o.id ) AS `count`,
		td.`order_date`
	FROM
		temp_day td
		LEFT JOIN orders o ON (o.`order_date` = td.`order_date` and o.customer_id = customer_id )
	WHERE
		(
			o.del_flag = '0'
		)
		OR o.id IS NULL
	GROUP BY
		td.`order_date`
	ORDER BY
		td.`order_date`;

END
;


-- ----------------------------
-- Procedure structure for proc_report_orders_by_customer_and_time_copy1
-- ----------------------------
DROP PROCEDURE IF EXISTS `proc_report_orders_by_customer_and_time_copy1`;

CREATE PROCEDURE `proc_report_orders_by_customer_and_time_copy1`(IN customer_id INT,
	IN from_date date,
	IN to_date date)
BEGIN
	CREATE TEMPORARY TABLE
	IF
		NOT EXISTS `temp_day` select * from
  (select adddate('1970-01-01',t4*10000 + t3*1000 + t2*100 + t1*10 + t0) `order_date` from
    (select 0 t0 union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t0,
    (select 0 t1 union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t1,
    (select 0 t2 union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t2,
    (select 0 t3 union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t3,
    (select 0 t4 union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t4) v
where order_date between from_date and to_date;
	SELECT
		COUNT( o.id ) AS `count`,
		td.`order_date`
	FROM
		temp_day td
		LEFT JOIN orders o ON (o.`order_date` = td.`order_date` and o.customer_id = customer_id )
	WHERE
		(
			o.del_flag = '0'
		)
		OR o.id IS NULL
	GROUP BY
		td.`order_date`
	ORDER BY
		td.`order_date`;

END
;


-- ----------------------------
-- Procedure structure for proc_report_vehicle_by_cost
-- ----------------------------
DROP PROCEDURE IF EXISTS `proc_report_vehicle_by_cost`;

CREATE PROCEDURE `proc_report_vehicle_by_cost`(IN summary BOOLEAN,
	IN vehicle_team_ids TEXT,IN vehicle_ids TEXT,IN driver_ids TEXT,
	IN customer_ids TEXT,IN from_date TEXT,IN to_date TEXT,IN dayCondition TEXT,IN partner_ids TEXT)
BEGIN

	DECLARE query TEXT;
	DECLARE summaryQuery TEXT;

	SET query = "SELECT tmp1.id as entity_id, tmp1.reg_no as entity_name, DATE_FORMAT(tmp1.date, '%d-%m-%Y') as date, tmp2.cost as status_complete";
	SET query = CONCAT(query," ","FROM (SELECT DISTINCT v.id,v.reg_no,cal.selected_date as date ,v.del_flag FROM ((SELECT vh.id,vh.reg_no,vh.del_flag FROM vehicle vh WHERE vh.del_flag = 0) UNION (SELECT 0 as id ,'*Chưa gán xe' as reg_no, 0 as del_flag)) v");

	IF vehicle_team_ids IS NOT NULL AND vehicle_team_ids != "" THEN
		SET query = CONCAT(query," ","INNER JOIN driver_vehicle dv ON dv.vehicle_id = v.id");
		SET query = CONCAT(query," ","INNER JOIN driver_vehicle_team dvt ON dvt.driver_id = dv.driver_id AND dvt.vehicle_team_id IN (",vehicle_team_ids,")");
	END IF;

	SET query = CONCAT(query," ","CROSS JOIN (
										SELECT selected_date FROM (
													SELECT adddate('1970-01-01',t4.i*10000 + t3.i*1000 + t2.i*100 + t1.i*10 + t0.i) selected_date
													FROM
														 (SELECT 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t0,
														 (SELECT 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t1,
														 (SELECT 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t2,
														 (SELECT 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t3,
														 (SELECT 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t4
										) d
										WHERE selected_date BETWEEN '",from_date,"' AND '",to_date,"'
										) cal WHERE 1=1");

	IF vehicle_ids IS NOT NULL AND vehicle_ids != "" THEN
		SET query = CONCAT(query," ","AND v.id IN (",vehicle_ids,")");
	END IF;

	IF partner_ids IS NOT NULL AND partner_ids != "" THEN
		SET query = CONCAT(query," ","AND v.partner_id IN (",partner_ids,")");
	END IF;

		SET query = CONCAT(query," ",") tmp1 LEFT JOIN (SELECT tmp.date,tmp.vehicle_id, SUM(tmp.final_cost) as cost");

		SET query = CONCAT(query," ","FROM (SELECT DISTINCT r.id,
																	(CASE WHEN (r.vehicle_id IS NULL OR r.vehicle_id = 0 ) THEN 0 ELSE r.vehicle_id END) as vehicle_id,
																	r.final_cost");
		IF dayCondition = 1 THEN
			SET query = CONCAT(query," ",",r.ETD_date as date");
		ELSEIF dayCondition = 2 THEN
			SET query = CONCAT(query," ",",r.ETD_date_reality as date");
		ELSEIF dayCondition = 3 THEN
			SET query = CONCAT(query," ",",r.ETA_date as date");
		ELSE
			SET query = CONCAT(query," ",",r.ETA_date_reality as date");
		END IF;

		SET query = CONCAT(query," ","FROM routes r
																	LEFT JOIN ( SELECT DISTINCT ro.route_id, o.customer_id FROM route_order ro
																							INNER JOIN orders o ON o.id = ro.order_id WHERE ro.del_flag = 0 AND o.del_flag = 0
																						) a ON a.route_id = r.id
																	LEFT JOIN driver_vehicle_team dvt ON dvt.driver_id = r.driver_id
																	WHERE r.del_flag = 0 AND r.is_approved = 1 AND r.route_status IN (1,0)");
		IF dayCondition = 1 THEN
			SET query = CONCAT(query," "," AND r.ETD_date IS NOT NULL AND r.ETD_date BETWEEN '",from_date,"' AND '",to_date,"' ");
		ELSEIF dayCondition = 2 THEN
			SET query = CONCAT(query," "," AND r.ETD_date_reality IS NOT NULL AND r.ETD_date_reality BETWEEN '",from_date,"' AND '",to_date,"' ");
		ELSEIF dayCondition = 3 THEN
			SET query = CONCAT(query," "," AND r.ETA_date IS NOT NULL AND r.ETA_date BETWEEN '",from_date,"' AND '",to_date,"' ");
		ELSE
			SET query = CONCAT(query," "," AND r.ETA_date_reality IS NOT NULL AND r.ETA_date_reality BETWEEN '",from_date,"' AND '",to_date,"' ");
		END IF;

		IF vehicle_team_ids IS NOT NULL AND vehicle_team_ids != "" THEN
			SET query = CONCAT(query," ","AND dvt.vehicle_team_id IN (",vehicle_team_ids,")");
		END IF;
		IF driver_ids IS NOT NULL AND driver_ids != "" THEN
			SET query = CONCAT(query," ","AND r.driver_id IN (",driver_ids,")");
		END IF;
		IF vehicle_ids IS NOT NULL AND vehicle_ids != "" THEN
			SET query = CONCAT(query," ","AND r.vehicle_id IN (",vehicle_ids,")");
		END IF;
		IF customer_ids IS NOT NULL AND customer_ids != "" THEN
			SET query = CONCAT(query," ","AND a.customer_id IN (",customer_ids,")");
		END IF;
		IF partner_ids IS NOT NULL AND partner_ids != "" THEN
			SET query = CONCAT(query," ","AND r.partner_id IN (",partner_ids,")");
		END IF;

		SET query = CONCAT(query," ",") tmp GROUP BY tmp.vehicle_id, DATE_FORMAT(tmp.date,'%Y-%m-%d')
																	) tmp2 ON tmp1.id = tmp2.vehicle_id AND tmp1.date = tmp2.date ");

		SET query = CONCAT(query," ","WHERE tmp1.del_flag = 0 OR (tmp2.cost IS NOT NULL AND tmp2.cost != 0 )");

	 IF summary = 0 THEN
			SET @SQLText = CONCAT(query,' ','ORDER BY tmp1.reg_no,tmp1.date');
			PREPARE stmt FROM @SQLText;
	 ELSE
			SET summaryQuery = "SELECT tmp3.date as date, SUM(tmp3.status_complete) as total";
			SET @SQLText = CONCAT(summaryQuery," FROM(", query,") tmp3 GROUP BY tmp3.date ORDER BY STR_TO_DATE(tmp3.date,'%d-%m-%Y')");
			PREPARE stmt FROM @SQLText;
	 END IF;

   EXECUTE stmt;
   DEALLOCATE PREPARE stmt;
END
;


-- ----------------------------
-- Procedure structure for proc_report_vehicle_by_cost_monthly
-- ----------------------------
DROP PROCEDURE IF EXISTS `proc_report_vehicle_by_cost_monthly`;

CREATE PROCEDURE `proc_report_vehicle_by_cost_monthly`(IN summary BOOLEAN,
	IN vehicle_team_ids TEXT,IN vehicle_ids TEXT,IN driver_ids TEXT,
	IN customer_ids TEXT,IN from_date TEXT,IN to_date TEXT,IN dayCondition TEXT,IN partner_ids TEXT)
BEGIN

	DECLARE query TEXT;
	DECLARE summaryQuery TEXT;

	SET query = "SELECT tmp1.id as entity_id, tmp1.reg_no as entity_name,DATE_FORMAT(STR_TO_DATE(tmp1.date,'%Y-%m'),'%m-%Y') as date, tmp2.cost as status_complete";
	 SET query = CONCAT(query," ","FROM (SELECT DISTINCT v.id,v.reg_no,cal.date,v.del_flag FROM ((SELECT vh.id,vh.reg_no,vh.del_flag FROM vehicle vh WHERE vh.del_flag = 0) UNION (SELECT 0 as id ,'*Chưa gán xe' as reg_no, 0 as del_flag)) v");

	IF vehicle_team_ids IS NOT NULL AND vehicle_team_ids != "" THEN
		SET query = CONCAT(query," ","INNER JOIN driver_vehicle dv ON dv.vehicle_id = v.id");
		SET query = CONCAT(query," ","INNER JOIN driver_vehicle_team dvt ON dvt.driver_id = dv.driver_id AND dvt.vehicle_team_id IN (",vehicle_team_ids,")");
	END IF;

	SET query = CONCAT(query," ","CROSS JOIN (select DATE_FORMAT(aDate,'%Y-%m') as date from (select @maxDate - interval (a.a + (10 * b.a) + (100 * c.a)) month as aDate from
																						(select 0 as a union all select 1 union all select 2 union all select 3 union all select 4 union all select 5 union all select 6 union all select 7
																						union all select 8 union all select 9) a,
																						(select 0 as a union all select 1 union all select 2 union all select 3 union all select 4 union all select 5 union all select 6 union all select 7
																						union all select 8 union all select 9) b,
																						(select 0 as a union all select 1 union all select 2 union all select 3 union all select 4 union all select 5 union all select 6 union all select 7
																						union all select 8 union all select 9) c,
																						(select @minDate := '",from_date,"', @maxDate := '",to_date,"') d) e
																						where DATE_FORMAT(aDate,'%Y-%m') BETWEEN DATE_FORMAT(@minDate, '%Y-%m') AND DATE_FORMAT(@maxDate, '%Y-%m')) cal WHERE 1=1");

	IF vehicle_ids IS NOT NULL AND vehicle_ids != "" THEN
		SET query = CONCAT(query," ","AND v.id IN (",vehicle_ids,")");
	END IF;

	IF partner_ids IS NOT NULL AND partner_ids != "" THEN
		SET query = CONCAT(query," ","AND v.partner_id IN (",partner_ids,")");
	END IF;

	SET query = CONCAT(query," ",") tmp1 LEFT JOIN (SELECT DATE_FORMAT(tmp.date,'%Y-%m') as date,tmp.vehicle_id, SUM(tmp.final_cost) as cost");

		SET query = CONCAT(query," ","FROM (SELECT DISTINCT r.id,
																	(CASE WHEN (r.vehicle_id IS NULL OR r.vehicle_id = 0 ) THEN 0 ELSE r.vehicle_id END) as vehicle_id,
																	r.final_cost");
		IF dayCondition = 1 THEN
			SET query = CONCAT(query," ",",r.ETD_date as date");
		ELSEIF dayCondition = 2 THEN
			SET query = CONCAT(query," ",",r.ETD_date_reality as date");
		ELSEIF dayCondition = 3 THEN
			SET query = CONCAT(query," ",",r.ETA_date as date");
		ELSE
			SET query = CONCAT(query," ",",r.ETA_date_reality as date");
		END IF;

		SET query = CONCAT(query," ","FROM routes r
																	LEFT JOIN ( SELECT DISTINCT ro.route_id, o.customer_id FROM route_order ro
																							INNER JOIN orders o ON o.id = ro.order_id WHERE ro.del_flag = 0 AND o.del_flag = 0
																						) a ON a.route_id = r.id
																	LEFT JOIN driver_vehicle_team dvt ON dvt.driver_id = r.driver_id
																	WHERE r.del_flag = 0 AND r.is_approved = 1 AND r.route_status IN (1,0)");
		IF dayCondition = 1 THEN
			SET query = CONCAT(query," "," AND r.ETD_date IS NOT NULL AND DATE_FORMAT(r.ETD_date,'%Y-%m') BETWEEN DATE_FORMAT('",from_date,"', '%Y-%m') AND DATE_FORMAT('",to_date,"', '%Y-%m')");
		ELSEIF dayCondition = 2 THEN
			SET query = CONCAT(query," "," AND r.ETD_date_reality IS NOT NULL AND DATE_FORMAT(r.ETD_date_reality,'%Y-%m') BETWEEN DATE_FORMAT('",from_date,"', '%Y-%m') AND DATE_FORMAT('",to_date,"', '%Y-%m')");
		ELSEIF dayCondition = 3 THEN
			SET query = CONCAT(query," "," AND r.ETA_date IS NOT NULL AND DATE_FORMAT(r.ETA_date,'%Y-%m') BETWEEN DATE_FORMAT('",from_date,"', '%Y-%m') AND DATE_FORMAT('",to_date,"', '%Y-%m')");
		ELSE
			SET query = CONCAT(query," "," AND r.ETA_date_reality IS NOT NULL AND DATE_FORMAT(r.ETA_date_reality,'%Y-%m') BETWEEN DATE_FORMAT('",from_date,"', '%Y-%m') AND DATE_FORMAT('",to_date,"', '%Y-%m')");
		END IF;

		IF vehicle_team_ids IS NOT NULL AND vehicle_team_ids != "" THEN
			SET query = CONCAT(query," ","AND dvt.vehicle_team_id IN (",vehicle_team_ids,")");
		END IF;
		IF driver_ids IS NOT NULL AND driver_ids != "" THEN
			SET query = CONCAT(query," ","AND r.driver_id IN (",driver_ids,")");
		END IF;
		IF vehicle_ids IS NOT NULL AND vehicle_ids != "" THEN
			SET query = CONCAT(query," ","AND r.vehicle_id IN (",vehicle_ids,")");
		END IF;
		IF customer_ids IS NOT NULL AND customer_ids != "" THEN
			SET query = CONCAT(query," ","AND a.customer_id IN (",customer_ids,")");
		END IF;
		IF partner_ids IS NOT NULL AND partner_ids != "" THEN
			SET query = CONCAT(query," ","AND r.partner_id IN (",partner_ids,")");
		END IF;

		SET query = CONCAT(query," ",") tmp GROUP BY tmp.vehicle_id, DATE_FORMAT(tmp.date,'%Y-%m')
																	) tmp2 ON tmp1.id = tmp2.vehicle_id AND tmp1.date = tmp2.date ");

		SET query = CONCAT(query," ","WHERE tmp1.del_flag = 0 OR (tmp2.cost IS NOT NULL AND tmp2.cost != 0 )");

	 IF summary = 0 THEN
			SET @SQLText = CONCAT(query,' ','ORDER BY tmp1.reg_no,tmp1.date');
			-- SELECT @SQLText;
			PREPARE stmt FROM @SQLText;
	 ELSE
			SET summaryQuery = "SELECT tmp3.date as date,SUM(tmp3.status_complete) as total";
			SET @SQLText = CONCAT(summaryQuery," FROM(", query,") tmp3 GROUP BY tmp3.date ORDER BY STR_TO_DATE(tmp3.date,'%m-%Y')");
			PREPARE stmt FROM @SQLText;
	 END IF;

   EXECUTE stmt;
   DEALLOCATE PREPARE stmt;
END
;

-- ----------------------------
-- Procedure structure for proc_report_vehicle_by_income
-- ----------------------------
DROP PROCEDURE IF EXISTS `proc_report_vehicle_by_income`;

CREATE PROCEDURE `proc_report_vehicle_by_income`(IN summary BOOLEAN,IN vehicle_team_ids TEXT,
	IN vehicle_ids TEXT,IN driver_ids TEXT,IN customer_ids TEXT,IN from_date TEXT,IN to_date TEXT,IN dayCondition TEXT,IN partner_ids TEXT)
BEGIN

	DECLARE query TEXT;
	DECLARE summaryQuery TEXT;

	SET query = "SELECT tmp1.id as entity_id, tmp1.reg_no as entity_name,DATE_FORMAT(tmp1.date,'%d-%m-%Y') as date,tmp2.status_complete";

	SET query = CONCAT(query," ","FROM (SELECT DISTINCT v.id,v.reg_no,cal.date,v.del_flag FROM ((SELECT vh.id,vh.reg_no,vh.del_flag FROM vehicle vh WHERE vh.del_flag = 0) UNION (SELECT 0 as id ,'*Chưa gán xe' as reg_no, 0 as del_flag)) v");

	IF vehicle_team_ids IS NOT NULL AND vehicle_team_ids != "" THEN
		SET query = CONCAT(query," ","INNER JOIN driver_vehicle dv ON dv.vehicle_id = v.id");
		SET query = CONCAT(query," ","INNER JOIN driver_vehicle_team dvt ON dvt.driver_id = dv.driver_id AND dvt.vehicle_team_id IN (",vehicle_team_ids,")");
	END IF;

	SET query = CONCAT(query," ","CROSS JOIN (
										SELECT selected_date as date FROM (
													SELECT adddate('1970-01-01',t4.i*10000 + t3.i*1000 + t2.i*100 + t1.i*10 + t0.i) selected_date
													FROM
														 (SELECT 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t0,
														 (SELECT 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t1,
														 (SELECT 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t2,
														 (SELECT 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t3,
														 (SELECT 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t4
										) d
										WHERE selected_date BETWEEN '",from_date,"' AND '",to_date,"'
										) cal WHERE v.del_flag = 0");

	IF vehicle_ids IS NOT NULL AND vehicle_ids != "" THEN
		SET query = CONCAT(query," ","AND v.id IN (",vehicle_ids,")");
	END IF;

	IF partner_ids IS NOT NULL AND partner_ids != "" THEN
		SET query = CONCAT(query," ","AND v.partner_id IN (",partner_ids,")");
	END IF;

	SET query = CONCAT(query," ",") tmp1 LEFT JOIN (SELECT tmp.date,tmp.vehicle_id,SUM(status_complete) as status_complete");

	SET query = CONCAT(query," ","FROM (SELECT DISTINCT o.id,
																(CASE WHEN (o.vehicle_id IS NULL OR o.vehicle_id = 0 ) THEN 0 ELSE o.vehicle_id END) as vehicle_id,
																o.amount as status_complete , ");

	IF dayCondition = 1 THEN
		SET query = CONCAT(query," "," o.ETD_date as date");
	ELSEIF dayCondition = 2 THEN
		SET query = CONCAT(query," "," o.ETD_date_reality as date");
	ELSEIF dayCondition = 3 THEN
		SET query = CONCAT(query," "," o.ETA_date as date");
	ELSE
		SET query = CONCAT(query," "," o.ETA_date_reality as date");
	END IF;

	SET query = CONCAT(query," ","FROM orders o ");

	IF vehicle_team_ids IS NOT NULL AND vehicle_team_ids != "" THEN
		SET query = CONCAT(query," ","INNER JOIN driver_vehicle_team dvt ON dvt.driver_id = o.primary_driver_id AND dvt.vehicle_team_id IN (",vehicle_team_ids,")");
	END IF;

	SET query = CONCAT(query," ","WHERE o.del_flag = 0 ");

	IF dayCondition = 1 THEN
		SET query = CONCAT(query," "," AND o.status IN(2,3,4,5,7) AND o.ETD_date BETWEEN '",from_date,"' AND '",to_date,"' ");
	ELSEIF dayCondition = 2 THEN
		SET query = CONCAT(query," "," AND o.status IN (4,5) AND o.ETD_date_reality BETWEEN '",from_date,"' AND '",to_date,"' ");
	ELSEIF dayCondition = 3 THEN
		SET query = CONCAT(query," "," AND o.status IN(2,3,4,5,7) AND o.ETA_date BETWEEN '",from_date,"' AND '",to_date,"' ");
	ELSE
		SET query = CONCAT(query," "," AND o.status = 5 AND o.ETA_date_reality BETWEEN '",from_date,"' AND '",to_date,"' ");
	END IF;

	IF driver_ids IS NOT NULL AND driver_ids != "" THEN
		SET query = CONCAT(query," ","AND o.primary_driver_id IN (",driver_ids,")");
	END IF;
	IF vehicle_ids IS NOT NULL AND vehicle_ids != "" THEN
		SET query = CONCAT(query," ","AND o.vehicle_id IN (",vehicle_ids,")");
	END IF;
	IF customer_ids IS NOT NULL AND customer_ids != "" THEN
		SET query = CONCAT(query," ","AND o.customer_id IN (",customer_ids,")");
	END IF;
	IF partner_ids IS NOT NULL AND partner_ids != "" THEN
		SET query = CONCAT(query," ","AND o.partner_id IN (",partner_ids,")");
	END IF;

	SET query = CONCAT(query," ",") tmp GROUP BY tmp.vehicle_id,tmp.date ) tmp2 ON tmp1.id = tmp2.vehicle_id AND tmp1.date = tmp2.date");

	 IF summary = 0 THEN
			SET @SQLText = CONCAT(query,' ','ORDER BY tmp1.reg_no,tmp1.date');
			-- SELECT @SQLText;
			PREPARE stmt FROM @SQLText;
	 ELSE
			SET summaryQuery = "SELECT tmp3.date as date,SUM(tmp3.status_complete) as total";

			SET @SQLText = CONCAT(summaryQuery," FROM(", query,") tmp3 GROUP BY tmp3.date ORDER BY STR_TO_DATE(tmp3.date,'%d-%m-%Y')");
			PREPARE stmt FROM @SQLText;
	 END IF;

   EXECUTE stmt;
   DEALLOCATE PREPARE stmt;
END
;


-- ----------------------------
-- Procedure structure for proc_report_vehicle_by_income_monthly
-- ----------------------------
DROP PROCEDURE IF EXISTS `proc_report_vehicle_by_income_monthly`;

CREATE PROCEDURE `proc_report_vehicle_by_income_monthly`(IN summary BOOLEAN,IN vehicle_team_ids TEXT,
	IN vehicle_ids TEXT,IN driver_ids TEXT,IN customer_ids TEXT,IN from_date TEXT,IN to_date TEXT,IN dayCondition TEXT,IN partner_ids TEXT)
BEGIN

	DECLARE query TEXT;
	DECLARE summaryQuery TEXT;

	SET query = "SELECT tmp1.id as entity_id, tmp1.reg_no as entity_name,DATE_FORMAT(STR_TO_DATE(tmp1.date,'%Y-%m'),'%m-%Y') as date,tmp2.status_complete";

	SET query = CONCAT(query," ","FROM (SELECT DISTINCT v.id,v.reg_no,cal.date,v.del_flag FROM ((SELECT vh.id,vh.reg_no,vh.del_flag FROM vehicle vh WHERE vh.del_flag = 0) UNION (SELECT 0 as id ,'*Chưa gán xe' as reg_no, 0 as del_flag)) v");

	IF vehicle_team_ids IS NOT NULL AND vehicle_team_ids != "" THEN
		SET query = CONCAT(query," ","INNER JOIN driver_vehicle dv ON dv.vehicle_id = v.id");
		SET query = CONCAT(query," ","INNER JOIN driver_vehicle_team dvt ON dvt.driver_id = dv.driver_id AND dvt.vehicle_team_id IN (",vehicle_team_ids,")");
	END IF;

	SET query = CONCAT(query," ","CROSS JOIN (select DATE_FORMAT(aDate,'%Y-%m') as date from (select @maxDate - interval (a.a + (10 * b.a) + (100 * c.a)) month as aDate from
																						(select 0 as a union all select 1 union all select 2 union all select 3 union all select 4 union all select 5 union all select 6 union all select 7
																						union all select 8 union all select 9) a,
																						(select 0 as a union all select 1 union all select 2 union all select 3 union all select 4 union all select 5 union all select 6 union all select 7
																						union all select 8 union all select 9) b,
																						(select 0 as a union all select 1 union all select 2 union all select 3 union all select 4 union all select 5 union all select 6 union all select 7
																						union all select 8 union all select 9) c,
																						(select @minDate := '",from_date,"', @maxDate := '",to_date,"') d) e
																						where DATE_FORMAT(aDate,'%Y-%m') BETWEEN DATE_FORMAT(@minDate, '%Y-%m') AND DATE_FORMAT(@maxDate, '%Y-%m')) cal
																						WHERE v.del_flag = 0");

	IF vehicle_ids IS NOT NULL AND vehicle_ids != "" THEN
		SET query = CONCAT(query," ","AND v.id IN (",vehicle_ids,")");
	END IF;

	IF partner_ids IS NOT NULL AND partner_ids != "" THEN
		SET query = CONCAT(query," ","AND v.partner_id IN (",partner_ids,")");
	END IF;

	SET query = CONCAT(query," ",") tmp1 LEFT JOIN (SELECT tmp.date,tmp.vehicle_id,SUM(status_complete) as status_complete");

	SET query = CONCAT(query," ","FROM (SELECT DISTINCT o.id,
																(CASE WHEN (o.vehicle_id IS NULL OR o.vehicle_id = 0 ) THEN 0 ELSE o.vehicle_id END) as vehicle_id,
																o.amount as status_complete , ");

	IF dayCondition = 1 THEN
		SET query = CONCAT(query," "," DATE_FORMAT(o.ETD_date,'%Y-%m') as date");
	ELSEIF dayCondition = 2 THEN
		SET query = CONCAT(query," "," DATE_FORMAT(o.ETD_date_reality,'%Y-%m') as date");
	ELSEIF dayCondition = 3 THEN
		SET query = CONCAT(query," "," DATE_FORMAT(o.ETA_date,'%Y-%m') as date");
	ELSE
		SET query = CONCAT(query," "," DATE_FORMAT(o.ETA_date_reality,'%Y-%m') as date");
	END IF;


	SET query = CONCAT(query," ","FROM orders o ");

	IF vehicle_team_ids IS NOT NULL AND vehicle_team_ids != "" THEN
		SET query = CONCAT(query," ","INNER JOIN driver_vehicle_team dvt ON dvt.driver_id = o.primary_driver_id AND dvt.vehicle_team_id IN (",vehicle_team_ids,")");
	END IF;

	SET query = CONCAT(query," ","WHERE o.del_flag = 0 ");

	IF dayCondition = 1 THEN
		SET query = CONCAT(query," "," AND o.status IN(2,3,4,5,7) AND DATE_FORMAT(o.ETD_date,'%Y-%m') BETWEEN DATE_FORMAT('",from_date,"','%Y-%m') AND DATE_FORMAT('",to_date,"','%Y-%m') ");
	ELSEIF dayCondition = 2 THEN
		SET query = CONCAT(query," "," AND o.status IN (4,5) AND DATE_FORMAT(o.ETD_date_reality,'%Y-%m') BETWEEN DATE_FORMAT('",from_date,"','%Y-%m') AND DATE_FORMAT('",to_date,"','%Y-%m') ");
	ELSEIF dayCondition = 3 THEN
		SET query = CONCAT(query," "," AND o.status IN(2,3,4,5,7) AND DATE_FORMAT(o.ETA_date,'%Y-%m') BETWEEN DATE_FORMAT('",from_date,"','%Y-%m') AND DATE_FORMAT('",to_date,"','%Y-%m') ");
	ELSE
		SET query = CONCAT(query," "," AND o.status = 5 AND DATE_FORMAT(o.ETA_date_reality,'%Y-%m') BETWEEN DATE_FORMAT('",from_date,"','%Y-%m') AND DATE_FORMAT('",to_date,"','%Y-%m') ");
	END IF;

	IF driver_ids IS NOT NULL AND driver_ids != "" THEN
		SET query = CONCAT(query," ","AND o.primary_driver_id IN (",driver_ids,")");
	END IF;
	IF vehicle_ids IS NOT NULL AND vehicle_ids != "" THEN
		SET query = CONCAT(query," ","AND o.vehicle_id IN (",vehicle_ids,")");
	END IF;
	IF customer_ids IS NOT NULL AND customer_ids != "" THEN
		SET query = CONCAT(query," ","AND o.customer_id IN (",customer_ids,")");
	END IF;
	IF partner_ids IS NOT NULL AND partner_ids != "" THEN
		SET query = CONCAT(query," ","AND o.partner_id IN (",partner_ids,")");
	END IF;

	SET query = CONCAT(query," ",") tmp GROUP BY tmp.vehicle_id,tmp.date ) tmp2 ON tmp1.id = tmp2.vehicle_id AND tmp1.date = tmp2.date");

	 IF summary = 0 THEN
			SET @SQLText = CONCAT(query,' ','ORDER BY tmp1.reg_no,tmp1.date');
			-- SELECT @SQLText;
			PREPARE stmt FROM @SQLText;
	 ELSE
			SET summaryQuery = "SELECT tmp3.date as date,SUM(tmp3.status_complete) as total";

			SET @SQLText = CONCAT(summaryQuery," FROM(", query,") tmp3 GROUP BY tmp3.date ORDER BY STR_TO_DATE(tmp3.date,'%m-%Y')");
			PREPARE stmt FROM @SQLText;
	 END IF;

   EXECUTE stmt;
   DEALLOCATE PREPARE stmt;
END
;


-- ----------------------------
-- Procedure structure for proc_report_vehicle_by_profit
-- ----------------------------
DROP PROCEDURE IF EXISTS `proc_report_vehicle_by_profit`;

CREATE PROCEDURE `proc_report_vehicle_by_profit`(IN summary BOOLEAN,IN vehicle_team_ids TEXT,
	IN vehicle_ids TEXT,IN driver_ids TEXT,IN customer_ids TEXT,IN from_date TEXT,IN to_date TEXT,IN dayCondition TEXT,IN partner_ids TEXT)
BEGIN

	DECLARE query TEXT;
	DECLARE summaryQuery TEXT;

	SET query = "SELECT tmp1.id as entity_id, tmp1.reg_no as entity_name,DATE_FORMAT(tmp1.date,'%d-%m-%Y') as date,
							((CASE WHEN tmp2.income IS NULL THEN 0 ELSE tmp2.income END) - (CASE WHEN tmp3.cost IS NULL THEN 0 ELSE tmp3.cost END)) as status_complete";

	 SET query = CONCAT(query," ","FROM (SELECT DISTINCT v.id,v.reg_no,cal.date,v.del_flag FROM ((SELECT vh.id,vh.reg_no,vh.del_flag FROM vehicle vh WHERE vh.del_flag = 0) UNION (SELECT 0 as id ,'*Chưa gán xe' as reg_no, 0 as del_flag)) v");

	IF vehicle_team_ids IS NOT NULL AND vehicle_team_ids != "" THEN
		SET query = CONCAT(query," ","INNER JOIN driver_vehicle dv ON dv.vehicle_id = v.id");
		SET query = CONCAT(query," ","INNER JOIN driver_vehicle_team dvt ON dvt.driver_id = dv.driver_id AND dvt.vehicle_team_id IN (",vehicle_team_ids,")");
	END IF;

	SET query = CONCAT(query," ","CROSS JOIN (
										SELECT selected_date as date FROM (
													SELECT adddate('1970-01-01',t4.i*10000 + t3.i*1000 + t2.i*100 + t1.i*10 + t0.i) selected_date
													FROM
														 (SELECT 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t0,
														 (SELECT 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t1,
														 (SELECT 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t2,
														 (SELECT 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t3,
														 (SELECT 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t4
										) d
										WHERE selected_date BETWEEN '",from_date,"' AND '",to_date,"'
										) cal
										WHERE v.del_flag = 0 ");

	IF vehicle_ids IS NOT NULL AND vehicle_ids != "" THEN
		SET query = CONCAT(query," ","AND v.id IN (",vehicle_ids,")");
	END IF;

	IF partner_ids IS NOT NULL AND partner_ids != "" THEN
		SET query = CONCAT(query," ","AND v.partner_id IN (",partner_ids,")");
	END IF;

	SET query = CONCAT(query," ",") tmp1 LEFT JOIN (SELECT tmp.date,tmp.vehicle_id, SUM(status_complete) as income");

	SET query = CONCAT(query," ","FROM (SELECT DISTINCT o.id,
																(CASE WHEN (o.vehicle_id IS NULL OR o.vehicle_id = 0 ) THEN 0 ELSE o.vehicle_id END) as vehicle_id,
																o.amount as status_complete , ");

	IF dayCondition = 1 THEN
		SET query = CONCAT(query," "," o.ETD_date as date");
	ELSEIF dayCondition = 2 THEN
		SET query = CONCAT(query," "," o.ETD_date_reality as date");
	ELSEIF dayCondition = 3 THEN
		SET query = CONCAT(query," "," o.ETA_date as date");
	ELSE
		SET query = CONCAT(query," "," o.ETA_date_reality as date");
	END IF;

	SET query = CONCAT(query," ","FROM orders o ");

	IF vehicle_team_ids IS NOT NULL AND vehicle_team_ids != "" THEN
		SET query = CONCAT(query," ","INNER JOIN driver_vehicle_team dvt ON dvt.driver_id = o.primary_driver_id AND dvt.vehicle_team_id IN (",vehicle_team_ids,")");
	END IF;

	SET query = CONCAT(query," ","WHERE o.del_flag = 0 ");

	IF dayCondition = 1 THEN
		SET query = CONCAT(query," "," AND o.status IN(2,3,4,5,7) AND o.ETD_date BETWEEN '",from_date,"' AND '",to_date,"' ");
	ELSEIF dayCondition = 2 THEN
		SET query = CONCAT(query," "," AND o.status IN (4,5) AND o.ETD_date_reality BETWEEN '",from_date,"' AND '",to_date,"' ");
	ELSEIF dayCondition = 3 THEN
		SET query = CONCAT(query," "," AND o.status IN(2,3,4,5,7) AND o.ETA_date BETWEEN '",from_date,"' AND '",to_date,"' ");
	ELSE
		SET query = CONCAT(query," "," AND o.status = 5 AND o.ETA_date_reality BETWEEN '",from_date,"' AND '",to_date,"' ");
	END IF;

	IF driver_ids IS NOT NULL AND driver_ids != "" THEN
		SET query = CONCAT(query," ","AND o.primary_driver_id IN (",driver_ids,")");
	END IF;
	IF vehicle_ids IS NOT NULL AND vehicle_ids != "" THEN
		SET query = CONCAT(query," ","AND o.vehicle_id IN (",vehicle_ids,")");
	END IF;
	IF customer_ids IS NOT NULL AND customer_ids != "" THEN
		SET query = CONCAT(query," ","AND o.customer_id IN (",customer_ids,")");
	END IF;
	IF partner_ids IS NOT NULL AND partner_ids != "" THEN
		SET query = CONCAT(query," ","AND o.partner_id IN (",partner_ids,")");
	END IF;

	SET query = CONCAT(query," ",") tmp GROUP BY tmp.vehicle_id,tmp.date ) tmp2 ON tmp1.id = tmp2.vehicle_id AND tmp1.date = tmp2.date");

	SET query = CONCAT(query," ","LEFT JOIN (SELECT tmp.date, tmp.vehicle_id, SUM(tmp.final_cost) as cost");

		SET query = CONCAT(query," ","FROM (SELECT DISTINCT r.id,
																	(CASE WHEN (r.vehicle_id IS NULL OR r.vehicle_id = 0 ) THEN 0 ELSE r.vehicle_id END) as vehicle_id,
																	r.final_cost");
		IF dayCondition = 1 THEN
			SET query = CONCAT(query," ",",r.ETD_date as date");
		ELSEIF dayCondition = 2 THEN
			SET query = CONCAT(query," ",",r.ETD_date_reality as date");
		ELSEIF dayCondition = 3 THEN
			SET query = CONCAT(query," ",",r.ETA_date as date");
		ELSE
			SET query = CONCAT(query," ",",r.ETA_date_reality as date");
		END IF;

		SET query = CONCAT(query," ","FROM routes r
																	LEFT JOIN ( SELECT DISTINCT ro.route_id, o.customer_id FROM route_order ro
																							INNER JOIN orders o ON o.id = ro.order_id WHERE ro.del_flag = 0 AND o.del_flag = 0
																						) a ON a.route_id = r.id
																	LEFT JOIN driver_vehicle_team dvt ON dvt.driver_id = r.driver_id
																	WHERE r.del_flag = 0 AND r.is_approved = 1 AND r.route_status IN (1,0)");
		IF dayCondition = 1 THEN
			SET query = CONCAT(query," "," AND r.ETD_date IS NOT NULL AND r.ETD_date BETWEEN '",from_date,"' AND '",to_date,"' ");
		ELSEIF dayCondition = 2 THEN
			SET query = CONCAT(query," "," AND r.ETD_date_reality IS NOT NULL AND r.ETD_date_reality BETWEEN '",from_date,"' AND '",to_date,"' ");
		ELSEIF dayCondition = 3 THEN
			SET query = CONCAT(query," "," AND r.ETA_date IS NOT NULL AND r.ETA_date BETWEEN '",from_date,"' AND '",to_date,"' ");
		ELSE
			SET query = CONCAT(query," "," AND r.ETA_date_reality IS NOT NULL AND r.ETA_date_reality BETWEEN '",from_date,"' AND '",to_date,"' ");
		END IF;

		IF vehicle_team_ids IS NOT NULL AND vehicle_team_ids != "" THEN
			SET query = CONCAT(query," ","AND dvt.vehicle_team_id IN (",vehicle_team_ids,")");
		END IF;
		IF driver_ids IS NOT NULL AND driver_ids != "" THEN
			SET query = CONCAT(query," ","AND r.driver_id IN (",driver_ids,")");
		END IF;
		IF vehicle_ids IS NOT NULL AND vehicle_ids != "" THEN
			SET query = CONCAT(query," ","AND r.vehicle_id IN (",vehicle_ids,")");
		END IF;
		IF customer_ids IS NOT NULL AND customer_ids != "" THEN
			SET query = CONCAT(query," ","AND a.customer_id IN (",customer_ids,")");
		END IF;
		IF partner_ids IS NOT NULL AND partner_ids != "" THEN
			SET query = CONCAT(query," ","AND r.partner_id IN (",partner_ids,")");
		END IF;

		SET query = CONCAT(query," ",") tmp GROUP BY tmp.vehicle_id, DATE_FORMAT(tmp.date,'%Y-%m-%d')
																	) tmp3 ON tmp1.id = tmp3.vehicle_id AND tmp1.date = tmp3.date ");

	 IF summary = 0 THEN
			SET @SQLText = CONCAT(query,' ','ORDER BY tmp1.reg_no,tmp1.date');
			-- SELECT @SQLText;
			PREPARE stmt FROM @SQLText;
	 ELSE
			SET summaryQuery = "SELECT tmp4.date as date,SUM(tmp4.status_complete) as total";

			SET @SQLText = CONCAT(summaryQuery," FROM(", query,") tmp4 GROUP BY tmp4.date ORDER BY STR_TO_DATE(tmp4.date,'%d-%m-%Y')");
			PREPARE stmt FROM @SQLText;
	 END IF;

   EXECUTE stmt;
   DEALLOCATE PREPARE stmt;
END
;


-- ----------------------------
-- Procedure structure for proc_report_vehicle_by_profit_monthly
-- ----------------------------
DROP PROCEDURE IF EXISTS `proc_report_vehicle_by_profit_monthly`;

CREATE PROCEDURE `proc_report_vehicle_by_profit_monthly`(IN summary BOOLEAN,IN vehicle_team_ids TEXT,
	IN vehicle_ids TEXT,IN driver_ids TEXT,IN customer_ids TEXT,IN from_date TEXT,IN to_date TEXT,IN dayCondition TEXT,IN partner_ids TEXT)
BEGIN

	DECLARE query TEXT;
	DECLARE summaryQuery TEXT;

	SET query = "SELECT tmp1.id as entity_id, tmp1.reg_no as entity_name,DATE_FORMAT(STR_TO_DATE(tmp1.date,'%Y-%m'),'%m-%Y') as date,
								((CASE WHEN tmp2.income IS NULL THEN 0 ELSE tmp2.income END) - (CASE WHEN tmp3.cost IS NULL THEN 0 ELSE tmp3.cost END)) as status_complete";

	 SET query = CONCAT(query," ","FROM (SELECT DISTINCT v.id,v.reg_no,cal.date,v.del_flag FROM ((SELECT vh.id,vh.reg_no,vh.del_flag FROM vehicle vh WHERE vh.del_flag = 0) UNION (SELECT 0 as id ,'*Chưa gán xe' as reg_no, 0 as del_flag)) v");

	IF vehicle_team_ids IS NOT NULL AND vehicle_team_ids != "" THEN
		SET query = CONCAT(query," ","INNER JOIN driver_vehicle dv ON dv.vehicle_id = v.id");
		SET query = CONCAT(query," ","INNER JOIN driver_vehicle_team dvt ON dvt.driver_id = dv.driver_id AND dvt.vehicle_team_id IN (",vehicle_team_ids,")");
	END IF;

	SET query = CONCAT(query," ","CROSS JOIN (select DATE_FORMAT(aDate,'%Y-%m') as date from (select @maxDate - interval (a.a + (10 * b.a) + (100 * c.a)) month as aDate from
																						(select 0 as a union all select 1 union all select 2 union all select 3 union all select 4 union all select 5 union all select 6 union all select 7
																						union all select 8 union all select 9) a,
																						(select 0 as a union all select 1 union all select 2 union all select 3 union all select 4 union all select 5 union all select 6 union all select 7
																						union all select 8 union all select 9) b,
																						(select 0 as a union all select 1 union all select 2 union all select 3 union all select 4 union all select 5 union all select 6 union all select 7
																						union all select 8 union all select 9) c,
																						(select @minDate := '",from_date,"', @maxDate := '",to_date,"') d) e
																						where DATE_FORMAT(aDate,'%Y-%m') BETWEEN DATE_FORMAT(@minDate, '%Y-%m') AND DATE_FORMAT(@maxDate, '%Y-%m')) cal
																						WHERE v.del_flag = 0");

	IF vehicle_ids IS NOT NULL AND vehicle_ids != "" THEN
		SET query = CONCAT(query," ","AND v.id IN (",vehicle_ids,")");
	END IF;

	IF partner_ids IS NOT NULL AND partner_ids != "" THEN
		SET query = CONCAT(query," ","AND v.partner_id IN (",partner_ids,")");
	END IF;

	SET query = CONCAT(query," ",") tmp1 LEFT JOIN (SELECT tmp.date,tmp.vehicle_id,SUM(status_complete) as income");

	SET query = CONCAT(query," ","FROM (SELECT DISTINCT o.id,
																(CASE WHEN (o.vehicle_id IS NULL OR o.vehicle_id = 0 ) THEN 0 ELSE o.vehicle_id END) as vehicle_id,
																o.amount as status_complete , ");

	IF dayCondition = 1 THEN
		SET query = CONCAT(query," "," DATE_FORMAT(o.ETD_date,'%Y-%m') as date");
	ELSEIF dayCondition = 2 THEN
		SET query = CONCAT(query," "," DATE_FORMAT(o.ETD_date_reality,'%Y-%m') as date");
	ELSEIF dayCondition = 3 THEN
		SET query = CONCAT(query," "," DATE_FORMAT(o.ETA_date,'%Y-%m') as date");
	ELSE
		SET query = CONCAT(query," "," DATE_FORMAT(o.ETA_date_reality,'%Y-%m') as date");
	END IF;


	SET query = CONCAT(query," ","FROM orders o ");

	IF vehicle_team_ids IS NOT NULL AND vehicle_team_ids != "" THEN
		SET query = CONCAT(query," ","INNER JOIN driver_vehicle_team dvt ON dvt.driver_id = o.primary_driver_id AND dvt.vehicle_team_id IN (",vehicle_team_ids,")");
	END IF;

	SET query = CONCAT(query," ","WHERE o.del_flag = 0 ");

	IF dayCondition = 1 THEN
		SET query = CONCAT(query," "," AND o.status IN(2,3,4,5,7) AND DATE_FORMAT(o.ETD_date,'%Y-%m') BETWEEN DATE_FORMAT('",from_date,"','%Y-%m') AND DATE_FORMAT('",to_date,"','%Y-%m') ");
	ELSEIF dayCondition = 2 THEN
		SET query = CONCAT(query," "," AND o.status IN (4,5) AND DATE_FORMAT(o.ETD_date_reality,'%Y-%m') BETWEEN DATE_FORMAT('",from_date,"','%Y-%m') AND DATE_FORMAT('",to_date,"','%Y-%m') ");
	ELSEIF dayCondition = 3 THEN
		SET query = CONCAT(query," "," AND o.status IN(2,3,4,5,7) AND DATE_FORMAT(o.ETA_date,'%Y-%m') BETWEEN DATE_FORMAT('",from_date,"','%Y-%m') AND DATE_FORMAT('",to_date,"','%Y-%m') ");
	ELSE
		SET query = CONCAT(query," "," AND o.status = 5 AND DATE_FORMAT(o.ETA_date_reality,'%Y-%m') BETWEEN DATE_FORMAT('",from_date,"','%Y-%m') AND DATE_FORMAT('",to_date,"','%Y-%m') ");
	END IF;

	IF driver_ids IS NOT NULL AND driver_ids != "" THEN
		SET query = CONCAT(query," ","AND o.primary_driver_id IN (",driver_ids,")");
	END IF;
	IF vehicle_ids IS NOT NULL AND vehicle_ids != "" THEN
		SET query = CONCAT(query," ","AND o.vehicle_id IN (",vehicle_ids,")");
	END IF;
	IF customer_ids IS NOT NULL AND customer_ids != "" THEN
		SET query = CONCAT(query," ","AND o.customer_id IN (",customer_ids,")");
	END IF;
	IF partner_ids IS NOT NULL AND partner_ids != "" THEN
		SET query = CONCAT(query," ","AND o.partner_id IN (",partner_ids,")");
	END IF;

	SET query = CONCAT(query," ",") tmp GROUP BY tmp.vehicle_id,tmp.date ) tmp2 ON tmp1.id = tmp2.vehicle_id AND tmp1.date = tmp2.date");

	SET query = CONCAT(query," ","LEFT JOIN (SELECT DATE_FORMAT(tmp.date,'%Y-%m') date , tmp.vehicle_id, SUM(tmp.final_cost) as cost");

		SET query = CONCAT(query," ","FROM (SELECT DISTINCT r.id,
																	(CASE WHEN (r.vehicle_id IS NULL OR r.vehicle_id = 0 ) THEN 0 ELSE r.vehicle_id END) as vehicle_id,
																	r.final_cost");
		IF dayCondition = 1 THEN
			SET query = CONCAT(query," ",",r.ETD_date as date");
		ELSEIF dayCondition = 2 THEN
			SET query = CONCAT(query," ",",r.ETD_date_reality as date");
		ELSEIF dayCondition = 3 THEN
			SET query = CONCAT(query," ",",r.ETA_date as date");
		ELSE
			SET query = CONCAT(query," ",",r.ETA_date_reality as date");
		END IF;

		SET query = CONCAT(query," ","FROM routes r
																	LEFT JOIN ( SELECT DISTINCT ro.route_id, o.customer_id FROM route_order ro
																							INNER JOIN orders o ON o.id = ro.order_id WHERE ro.del_flag = 0 AND o.del_flag = 0
																						) a ON a.route_id = r.id
																	LEFT JOIN driver_vehicle_team dvt ON dvt.driver_id = r.driver_id
																	WHERE r.del_flag = 0 AND r.is_approved = 1 AND r.route_status IN (1,0)");
		IF dayCondition = 1 THEN
			SET query = CONCAT(query," "," AND r.ETD_date IS NOT NULL AND DATE_FORMAT(r.ETD_date,'%Y-%m') BETWEEN DATE_FORMAT('",from_date,"', '%Y-%m') AND DATE_FORMAT('",to_date,"', '%Y-%m')");
		ELSEIF dayCondition = 2 THEN
			SET query = CONCAT(query," "," AND r.ETD_date_reality IS NOT NULL AND DATE_FORMAT(r.ETD_date_reality,'%Y-%m') BETWEEN DATE_FORMAT('",from_date,"', '%Y-%m') AND DATE_FORMAT('",to_date,"', '%Y-%m')");
		ELSEIF dayCondition = 3 THEN
			SET query = CONCAT(query," "," AND r.ETA_date IS NOT NULL AND DATE_FORMAT(r.ETA_date,'%Y-%m') BETWEEN DATE_FORMAT('",from_date,"', '%Y-%m') AND DATE_FORMAT('",to_date,"', '%Y-%m')");
		ELSE
			SET query = CONCAT(query," "," AND r.ETA_date_reality IS NOT NULL AND DATE_FORMAT(r.ETA_date_reality,'%Y-%m') BETWEEN DATE_FORMAT('",from_date,"', '%Y-%m') AND DATE_FORMAT('",to_date,"', '%Y-%m')");
		END IF;

		IF vehicle_team_ids IS NOT NULL AND vehicle_team_ids != "" THEN
			SET query = CONCAT(query," ","AND dvt.vehicle_team_id IN (",vehicle_team_ids,")");
		END IF;
		IF driver_ids IS NOT NULL AND driver_ids != "" THEN
			SET query = CONCAT(query," ","AND r.driver_id IN (",driver_ids,")");
		END IF;
		IF vehicle_ids IS NOT NULL AND vehicle_ids != "" THEN
			SET query = CONCAT(query," ","AND r.vehicle_id IN (",vehicle_ids,")");
		END IF;
		IF customer_ids IS NOT NULL AND customer_ids != "" THEN
			SET query = CONCAT(query," ","AND a.customer_id IN (",customer_ids,")");
		END IF;
		IF partner_ids IS NOT NULL AND partner_ids != "" THEN
			SET query = CONCAT(query," ","AND r.partner_id IN (",partner_ids,")");
		END IF;

		SET query = CONCAT(query," ",") tmp GROUP BY tmp.vehicle_id, DATE_FORMAT(tmp.date,'%Y-%m')
																	) tmp3 ON tmp1.id = tmp3.vehicle_id AND tmp1.date = tmp3.date ");

	 IF summary = 0 THEN
			SET @SQLText = CONCAT(query,' ','ORDER BY tmp1.reg_no,tmp1.date');
			-- SELECT @SQLText;
			PREPARE stmt FROM @SQLText;
	 ELSE
			SET summaryQuery = "SELECT tmp4.date as date,SUM(tmp4.status_complete) as total";

			SET @SQLText = CONCAT(summaryQuery," FROM(", query,") tmp4 GROUP BY tmp4.date ORDER BY STR_TO_DATE(tmp4.date,'%m-%Y')");
			PREPARE stmt FROM @SQLText;
	 END IF;

   EXECUTE stmt;
   DEALLOCATE PREPARE stmt;
END
;


-- ----------------------------
-- Procedure structure for proc_report_vehicle_by_turn
-- ----------------------------
DROP PROCEDURE IF EXISTS `proc_report_vehicle_by_turn`;

CREATE PROCEDURE `proc_report_vehicle_by_turn`(IN summary BOOLEAN,IN vehicle_team_ids TEXT,
	IN vehicle_ids TEXT,IN driver_ids TEXT,
	IN customer_ids TEXT,IN status_all BOOLEAN,IN status_incomplete BOOLEAN,
	IN status_complete BOOLEAN,IN status_cancel BOOLEAN,IN from_date TEXT,IN to_date TEXT,
	IN partner_ids TEXT)
BEGIN

	DECLARE query TEXT;
	DECLARE summaryQuery TEXT;

	SET query = "SELECT tmp1.id as entity_id, tmp1.reg_no as entity_name,DATE_FORMAT(tmp1.date,'%d-%m-%Y') as date";
		IF status_all = 1 THEN SET query = CONCAT(query,",","tmp2.status_all"); END IF;
		IF status_complete = 1 THEN
			SET query = CONCAT(query,",","tmp2.status_complete");
			SET query = CONCAT(query,",","tmp2.status_on_time");
			SET query = CONCAT(query,",","tmp2.status_late");
		END IF;
		IF status_incomplete = 1 THEN SET query = CONCAT(query,",","tmp2.status_incomplete"); END IF;
		IF status_cancel  = 1 THEN SET query = CONCAT(query,",","tmp2.status_cancel "); END IF;

	SET query = CONCAT(query," ","FROM (SELECT DISTINCT v.id,v.reg_no,cal.date,v.del_flag,v.partner_id FROM ((SELECT vh.id,vh.reg_no,vh.del_flag,vh.partner_id FROM vehicle vh WHERE vh.del_flag = 0) UNION (SELECT 0 as id ,'*Chưa gán xe' as reg_no,0 as del_flag,0 as partner_id)) v");

	IF vehicle_team_ids IS NOT NULL AND vehicle_team_ids != "" THEN
		SET query = CONCAT(query," ","INNER JOIN driver_vehicle dv ON dv.vehicle_id = v.id");
		SET query = CONCAT(query," ","INNER JOIN driver_vehicle_team dvt ON dvt.driver_id = dv.driver_id AND dvt.vehicle_team_id IN (",vehicle_team_ids,")");
	END IF;

	SET query = CONCAT(query," ","CROSS JOIN (
										SELECT selected_date as date FROM (
													SELECT adddate('1970-01-01',t4.i*10000 + t3.i*1000 + t2.i*100 + t1.i*10 + t0.i) selected_date
													FROM
														 (SELECT 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t0,
														 (SELECT 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t1,
														 (SELECT 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t2,
														 (SELECT 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t3,
														 (SELECT 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t4
										) d
										WHERE selected_date BETWEEN '",from_date,"' AND '",to_date,"'
										) cal
										WHERE 1=1 ");

	IF vehicle_ids IS NOT NULL AND vehicle_ids != "" THEN
		SET query = CONCAT(query," ","AND v.id IN (",vehicle_ids,")");
	END IF;

	IF partner_ids IS NOT NULL AND partner_ids != "" THEN
		SET query = CONCAT(query," ","AND v.partner_id IN (",partner_ids,")");
	END IF;

	SET query = CONCAT(query," ",") tmp1 LEFT JOIN (SELECT tmp.date,tmp.vehicle_id");

		IF status_all = 1 THEN SET query = CONCAT(query,",","(SUM(status_complete) + SUM(status_incomplete) + SUM(status_cancel))  as status_all"); END IF;
		IF status_complete = 1 THEN
			SET query = CONCAT(query,",","SUM(status_complete) as status_complete");
			SET query = CONCAT(query,",","SUM(status_on_time) as status_on_time");
			SET query = CONCAT(query,",","SUM(status_late) as status_late");
		END IF;
		IF status_incomplete = 1 THEN SET query = CONCAT(query,",","SUM(status_incomplete) as status_incomplete"); END IF;
		IF status_cancel = 1 THEN SET query = CONCAT(query,",","SUM(status_cancel) as status_cancel"); END IF;

	  SET query = CONCAT(query," ","FROM (SELECT DISTINCT o.id, (CASE WHEN (o.ETA_date_reality IS NOT NULL AND o.status = 5) THEN o.ETA_date_reality ELSE o.ETA_date END) date,
																							(CASE WHEN (o.vehicle_id IS NULL OR o.vehicle_id = 0 ) THEN 0 ELSE o.vehicle_id END) as vehicle_id");

		IF status_complete = 1 THEN
			SET query = CONCAT(query,",","(CASE WHEN (o.ETA_date_reality IS NOT NULL AND o.status = 5) THEN 1 ELSE 0 END) as status_complete");
			SET query = CONCAT(query,",","(CASE WHEN (o.ETA_date_reality IS NOT NULL AND o.status = 5
																	AND DATE_FORMAT(CONCAT(o.ETA_date,' ',o.ETA_time), '%Y-%m-%d %H:%i') >= DATE_FORMAT(CONCAT(o.ETA_date_reality,' ',o.ETA_time_reality)
																	, '%Y-%m-%d %H:%i')) THEN 1 ELSE 0 END) as status_on_time");
			SET query = CONCAT(query,",","(CASE WHEN (o.ETA_date_reality IS NOT NULL AND o.status = 5
																													AND DATE_FORMAT(CONCAT(o.ETA_date,' ',o.ETA_time), '%Y-%m-%d %H:%i')
																													< DATE_FORMAT(CONCAT(o.ETA_date_reality,' ',o.ETA_time_reality), '%Y-%m-%d %H:%i')
																													) THEN 1 ELSE 0 END) as status_late");
		ELSE
			SET query = CONCAT(query,",","0 as status_complete");
		END IF;
		IF status_incomplete = 1 THEN SET query = CONCAT(query,",","(CASE WHEN o.status IN (2,3,4,7) THEN 1 ELSE 0 END) as status_incomplete");
		ELSE
			SET query = CONCAT(query,",","0 as status_incomplete");
		END IF;

		IF status_cancel = 1 THEN SET query = CONCAT(query,",","(CASE WHEN o.status = 6 THEN 1 ELSE 0 END) as status_cancel");
		ELSE
			SET query = CONCAT(query,",","0 as status_cancel");
		END IF;

	SET query = CONCAT(query," ","FROM orders o ");

	IF vehicle_team_ids IS NOT NULL AND vehicle_team_ids != "" THEN
		SET query = CONCAT(query," ","LEFT JOIN driver_vehicle_team dvt ON dvt.driver_id = o.primary_driver_id AND dvt.vehicle_team_id IN (",vehicle_team_ids,")");
	END IF;

	SET query = CONCAT(query," ","WHERE o.del_flag = 0 AND o.status != 1 AND CASE WHEN o.ETA_date_reality IS NOT NULL AND o.status = 5
																																								THEN o.ETA_date_reality BETWEEN '",from_date,"' AND '",to_date,"'
																																								ELSE o.ETA_date BETWEEN '",from_date,"' AND '",to_date,"' END");

	IF driver_ids IS NOT NULL AND driver_ids != "" THEN
		SET query = CONCAT(query," ","AND o.primary_driver_id IN (",driver_ids,")");
	END IF;
	IF vehicle_ids IS NOT NULL AND vehicle_ids != "" THEN
		SET query = CONCAT(query," ","AND o.vehicle_id IN (",vehicle_ids,")");
	END IF;
	IF customer_ids IS NOT NULL AND customer_ids != "" THEN
		SET query = CONCAT(query," ","AND o.customer_id IN (",customer_ids,")");
	END IF;
	IF partner_ids IS NOT NULL AND partner_ids != "" THEN
		SET query = CONCAT(query," ","AND o.partner_id IN (",partner_ids,")");
	END IF;

	SET query = CONCAT(query," ",") tmp GROUP BY tmp.vehicle_id,tmp.date ) tmp2 ON tmp1.id = tmp2.vehicle_id AND tmp1.date = tmp2.date");

	SET query = CONCAT(query," ","WHERE tmp1.del_flag = 0 OR (tmp2.status_all IS NOT NULL AND tmp2.status_all != 0 )");

	 IF summary = 0 THEN
			SET @SQLText = CONCAT(query,' ','ORDER BY tmp1.reg_no,tmp1.date');
			-- SELECT @SQLText;
			PREPARE stmt FROM @SQLText;
	 ELSE
			SET summaryQuery = "SELECT tmp3.date as date,";

			IF status_all = 1 THEN SET summaryQuery = CONCAT(summaryQuery,"SUM(tmp3.status_all) as total");
			ELSEIF status_incomplete = 1 THEN SET summaryQuery = CONCAT(summaryQuery,"SUM(tmp3.status_incomplete) as total");
			ELSEIF status_complete = 1 THEN SET summaryQuery = CONCAT(summaryQuery,"SUM(tmp3.status_complete) as total");
			ELSEIF status_cancel = 1 THEN SET summaryQuery = CONCAT(summaryQuery,"SUM(tmp3.status_cancel) as total");
			END IF;

			SET @SQLText = CONCAT(summaryQuery," FROM(", query,") tmp3 GROUP BY tmp3.date ORDER BY STR_TO_DATE(tmp3.date,'%d-%m-%Y')");
			PREPARE stmt FROM @SQLText;
	 END IF;

   EXECUTE stmt;
   DEALLOCATE PREPARE stmt;
END
;


-- ----------------------------
-- Procedure structure for proc_report_vehicle_by_turn_monthly
-- ----------------------------
DROP PROCEDURE IF EXISTS `proc_report_vehicle_by_turn_monthly`;

CREATE PROCEDURE `proc_report_vehicle_by_turn_monthly`(IN summary BOOLEAN,IN vehicle_team_ids TEXT,
	IN vehicle_ids TEXT,IN driver_ids TEXT,
	IN customer_ids TEXT,IN status_all BOOLEAN,IN status_incomplete BOOLEAN,
	IN status_complete BOOLEAN,IN status_cancel BOOLEAN,IN from_date TEXT,IN to_date TEXT, IN partner_id TEXT)
BEGIN

	DECLARE query TEXT;
	DECLARE summaryQuery TEXT;

	SET query = "SELECT tmp1.id as entity_id, tmp1.reg_no as entity_name,DATE_FORMAT(STR_TO_DATE(tmp1.date,'%Y-%m'),'%m-%Y') as date";
		IF status_all = 1 THEN SET query = CONCAT(query,",","tmp2.status_all"); END IF;
		IF status_complete = 1 THEN
			SET query = CONCAT(query,",","tmp2.status_complete");
			SET query = CONCAT(query,",","tmp2.status_on_time");
			SET query = CONCAT(query,",","tmp2.status_late");
		END IF;
		IF status_incomplete = 1 THEN SET query = CONCAT(query,",","tmp2.status_incomplete"); END IF;
		IF status_cancel  = 1 THEN SET query = CONCAT(query,",","tmp2.status_cancel"); END IF;

	SET query = CONCAT(query," ","FROM (SELECT DISTINCT v.id,v.reg_no,cal.date,v.del_flag FROM ((SELECT vh.id,vh.reg_no,vh.del_flag,vh.partner_id FROM vehicle vh) UNION (SELECT 0 as id ,'*Chưa gán xe' as reg_no,0 as del_flag,0 as partner_id)) v");

	IF vehicle_team_ids IS NOT NULL AND vehicle_team_ids != "" THEN
		SET query = CONCAT(query," ","INNER JOIN driver_vehicle dv ON dv.vehicle_id = v.id");
		SET query = CONCAT(query," ","INNER JOIN driver_vehicle_team dvt ON dvt.driver_id = dv.driver_id AND dvt.vehicle_team_id IN (",vehicle_team_ids,")");
	END IF;

	SET query = CONCAT(query," ","CROSS JOIN (select DATE_FORMAT(aDate,'%Y-%m') as date from (select @maxDate - interval (a.a + (10 * b.a) + (100 * c.a)) month as aDate from
																						(select 0 as a union all select 1 union all select 2 union all select 3 union all select 4 union all select 5 union all select 6 union all select 7
																						union all select 8 union all select 9) a,
																						(select 0 as a union all select 1 union all select 2 union all select 3 union all select 4 union all select 5 union all select 6 union all select 7
																						union all select 8 union all select 9) b,
																						(select 0 as a union all select 1 union all select 2 union all select 3 union all select 4 union all select 5 union all select 6 union all select 7
																						union all select 8 union all select 9) c,
																						(select @minDate := '",from_date,"', @maxDate := '",to_date,"') d) e
																						where DATE_FORMAT(aDate,'%Y-%m') BETWEEN DATE_FORMAT(@minDate, '%Y-%m') AND DATE_FORMAT(@maxDate, '%Y-%m')) cal
																						WHERE 1=1");

	IF vehicle_ids IS NOT NULL AND vehicle_ids != "" THEN
		SET query = CONCAT(query," ","AND v.id IN (",vehicle_ids,")");
	END IF;

	IF partner_id IS NOT NULL AND partner_id != ""  THEN
		SET query = CONCAT(query," ","AND v.partner_id IN (",partner_id,")");
	END IF;

	SET query = CONCAT(query," ",") tmp1 LEFT JOIN (SELECT tmp.date,tmp.vehicle_id");

		IF status_all = 1 THEN SET query = CONCAT(query,",","(SUM(status_complete) + SUM(status_incomplete) + SUM(status_cancel))  as status_all"); END IF;
		IF status_complete = 1 THEN
			SET query = CONCAT(query,",","SUM(status_complete) as status_complete");
			SET query = CONCAT(query,",","SUM(status_on_time) as status_on_time");
			SET query = CONCAT(query,",","SUM(status_late) as status_late");
		END IF;
		IF status_incomplete = 1 THEN SET query = CONCAT(query,",","SUM(status_incomplete) as status_incomplete"); END IF;
		IF status_cancel = 1 THEN SET query = CONCAT(query,",","SUM(status_cancel) as status_cancel"); END IF;

	SET query = CONCAT(query," ","FROM (SELECT DISTINCT o.id, (CASE WHEN (o.ETA_date_reality IS NOT NULL AND o.status = 5) THEN DATE_FORMAT(o.ETA_date_reality,'%Y-%m') ELSE DATE_FORMAT(o.ETA_date,'%Y-%m') END) date,
																							(CASE WHEN (o.vehicle_id IS NULL OR o.vehicle_id = 0 ) THEN 0 ELSE o.vehicle_id END) as vehicle_id");

IF status_complete = 1 THEN
			SET query = CONCAT(query,",","(CASE WHEN (o.ETA_date_reality IS NOT NULL AND o.status = 5) THEN 1 ELSE 0 END) as status_complete");
			SET query = CONCAT(query,",","(CASE WHEN (o.ETA_date_reality IS NOT NULL AND o.status = 5
																	AND DATE_FORMAT(CONCAT(o.ETA_date,' ',o.ETA_time), '%Y-%m-%d %H:%i') >= DATE_FORMAT(CONCAT(o.ETA_date_reality,' ',o.ETA_time_reality)
																	, '%Y-%m-%d %H:%i')) THEN 1 ELSE 0 END) as status_on_time");
			SET query = CONCAT(query,",","(CASE WHEN (o.ETA_date_reality IS NOT NULL AND o.status = 5
																			AND DATE_FORMAT(CONCAT(o.ETA_date,' ',o.ETA_time), '%Y-%m-%d %H:%i') < DATE_FORMAT(CONCAT(o.ETA_date_reality,' ',o.ETA_time_reality)
																			, '%Y-%m-%d %H:%i')) THEN 1 ELSE 0 END) as status_late");
		ELSE
			SET query = CONCAT(query,",","0 as status_complete");
		END IF;
		IF status_incomplete = 1 THEN SET query = CONCAT(query,",","(CASE WHEN o.status IN (2,3,4,7) THEN 1 ELSE 0 END) as status_incomplete");
		ELSE
			SET query = CONCAT(query,",","0 as status_incomplete");
		END IF;

		IF status_cancel = 1 THEN SET query = CONCAT(query,",","(CASE WHEN o.status = 6 THEN 1 ELSE 0 END) as status_cancel");
		ELSE
			SET query = CONCAT(query,",","0 as status_cancel");
		END IF;

	SET query = CONCAT(query," ","FROM orders o ");

	IF vehicle_team_ids IS NOT NULL AND vehicle_team_ids != "" THEN
		SET query = CONCAT(query," ","LEFT JOIN driver_vehicle_team dvt ON dvt.driver_id = o.primary_driver_id AND dvt.vehicle_team_id IN (",vehicle_team_ids,")");
	END IF;

	SET query = CONCAT(query," ","WHERE o.del_flag = 0 AND o.status != 1 AND CASE WHEN o.ETA_date_reality IS NOT NULL AND o.status = 5
																																								THEN o.ETA_date_reality BETWEEN '",from_date,"' AND '",to_date,"'
																																								ELSE o.ETA_date BETWEEN '",from_date,"' AND '",to_date,"' END");

	IF driver_ids IS NOT NULL AND driver_ids != "" THEN
		SET query = CONCAT(query," ","AND o.primary_driver_id IN (",driver_ids,")");
	END IF;
	IF vehicle_ids IS NOT NULL AND vehicle_ids != "" THEN
		SET query = CONCAT(query," ","AND o.vehicle_id IN (",vehicle_ids,")");
	END IF;
	IF customer_ids IS NOT NULL AND customer_ids != "" THEN
		SET query = CONCAT(query," ","AND o.customer_id IN (",customer_ids,")");
	END IF;

	SET query = CONCAT(query," ",") tmp GROUP BY tmp.vehicle_id,tmp.date ) tmp2 ON tmp1.id = tmp2.vehicle_id AND tmp1.date = tmp2.date");

	SET query = CONCAT(query," ","WHERE tmp1.del_flag = 0 OR (tmp2.status_all IS NOT NULL AND tmp2.status_all != 0 )");

	 IF summary = 0 THEN
			SET @SQLText = CONCAT(query,' ','ORDER BY tmp1.reg_no,tmp1.date');
		 -- SELECT @SQLText;
			PREPARE stmt FROM @SQLText;
	 ELSE
			SET summaryQuery = "SELECT tmp3.date as date,";

			IF status_all = 1 THEN SET summaryQuery = CONCAT(summaryQuery,"SUM(tmp3.status_all) as total");
			ELSEIF status_incomplete = 1 THEN SET summaryQuery = CONCAT(summaryQuery,"SUM(tmp3.status_incomplete) as total");
			ELSEIF status_complete = 1 THEN SET summaryQuery = CONCAT(summaryQuery,"SUM(tmp3.status_complete) as total");
			ELSEIF status_cancel = 1 THEN SET summaryQuery = CONCAT(summaryQuery,"SUM(tmp3.status_cancel) as total");
			END IF;

			SET @SQLText = CONCAT(summaryQuery," FROM(", query,") tmp3 GROUP BY tmp3.date ORDER BY STR_TO_DATE(tmp3.date,'%m-%Y')");
			PREPARE stmt FROM @SQLText;
	 END IF;

   EXECUTE stmt;
   DEALLOCATE PREPARE stmt;
END
;


-- ----------------------------
-- Procedure structure for proc_report_vehicle_team_by_cost
-- ----------------------------
DROP PROCEDURE IF EXISTS `proc_report_vehicle_team_by_cost`;

CREATE PROCEDURE `proc_report_vehicle_team_by_cost`(IN summary BOOLEAN,
	IN vehicle_team_ids TEXT,IN vehicle_ids TEXT,IN driver_ids TEXT,
	IN customer_ids TEXT,IN from_date TEXT,IN to_date TEXT,IN dayCondition TEXT,IN partner_ids TEXT)
BEGIN

	DECLARE query TEXT;
	DECLARE summaryQuery TEXT;

	SET query = "SELECT tmp1.id as entity_id, tmp1.name as entity_name, DATE_FORMAT(tmp1.date, '%d-%m-%Y') as date, tmp2.cost as status_complete";
	SET query = CONCAT(query," ","FROM (SELECT vt.id,vt.name,cal.date,vt.del_flag,vt.partner_id FROM ((SELECT vtm.id,vtm.name,vtm.del_flag,vtm.partner_id FROM vehicle_team vtm) UNION (SELECT 0 as id ,'*Chưa gán đội tài xế' as name,0 as del_flag,0 as partner_id)) vt");

	SET query = CONCAT(query," ","CROSS JOIN (
										SELECT selected_date as date FROM (
													SELECT adddate('1970-01-01',t4.i*10000 + t3.i*1000 + t2.i*100 + t1.i*10 + t0.i) selected_date
													FROM
														 (SELECT 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t0,
														 (SELECT 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t1,
														 (SELECT 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t2,
														 (SELECT 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t3,
														 (SELECT 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t4
										) d
										WHERE selected_date BETWEEN '",from_date,"' AND '",to_date,"'
										) cal
										WHERE 1=1 ");

	IF vehicle_team_ids IS NOT NULL AND vehicle_team_ids != "" THEN
		SET query = CONCAT(query," ","AND vt.id IN (",vehicle_team_ids,")");
	END IF;

	IF partner_ids IS NOT NULL AND partner_ids != "" THEN
		SET query = CONCAT(query," ","AND vt.partner_id IN (",partner_ids,")");
	END IF;

		SET query = CONCAT(query," ",") tmp1 LEFT JOIN (SELECT tmp.date,tmp.vehicle_team_id, SUM(tmp.final_cost) as cost");

		SET query = CONCAT(query," ","FROM (SELECT DISTINCT r.id,
																	(CASE WHEN (dvt.vehicle_team_id IS NULL OR dvt.vehicle_team_id = 0 ) THEN 0 ELSE dvt.vehicle_team_id END) as vehicle_team_id,
																	r.final_cost");
		IF dayCondition = 1 THEN
			SET query = CONCAT(query," ",",r.ETD_date as date");
		ELSEIF dayCondition = 2 THEN
			SET query = CONCAT(query," ",",r.ETD_date_reality as date");
		ELSEIF dayCondition = 3 THEN
			SET query = CONCAT(query," ",",r.ETA_date as date");
		ELSE
			SET query = CONCAT(query," ",",r.ETA_date_reality as date");
		END IF;

		SET query = CONCAT(query," ","FROM routes r
																	LEFT JOIN ( SELECT DISTINCT ro.route_id, o.customer_id FROM route_order ro
																							INNER JOIN orders o ON o.id = ro.order_id WHERE ro.del_flag = 0 AND o.del_flag = 0
																						) a ON a.route_id = r.id
																	LEFT JOIN driver_vehicle_team dvt ON dvt.driver_id = r.driver_id
																	WHERE r.del_flag = 0 AND r.is_approved = 1 AND r.route_status IN (1,0)");
		IF dayCondition = 1 THEN
			SET query = CONCAT(query," "," AND r.ETD_date IS NOT NULL AND r.ETD_date BETWEEN '",from_date,"' AND '",to_date,"' ");
		ELSEIF dayCondition = 2 THEN
			SET query = CONCAT(query," "," AND r.ETD_date_reality IS NOT NULL AND r.ETD_date_reality BETWEEN '",from_date,"' AND '",to_date,"' ");
		ELSEIF dayCondition = 3 THEN
			SET query = CONCAT(query," "," AND r.ETA_date IS NOT NULL AND r.ETA_date BETWEEN '",from_date,"' AND '",to_date,"' ");
		ELSE
			SET query = CONCAT(query," "," AND r.ETA_date_reality IS NOT NULL AND r.ETA_date_reality BETWEEN '",from_date,"' AND '",to_date,"' ");
		END IF;

		IF vehicle_team_ids IS NOT NULL AND vehicle_team_ids != "" THEN
			SET query = CONCAT(query," ","AND dvt.vehicle_team_id IN (",vehicle_team_ids,")");
		END IF;
		IF driver_ids IS NOT NULL AND driver_ids != "" THEN
			SET query = CONCAT(query," ","AND r.driver_id IN (",driver_ids,")");
		END IF;
		IF vehicle_ids IS NOT NULL AND vehicle_ids != "" THEN
			SET query = CONCAT(query," ","AND r.vehicle_id IN (",vehicle_ids,")");
		END IF;
		IF customer_ids IS NOT NULL AND customer_ids != "" THEN
			SET query = CONCAT(query," ","AND a.customer_id IN (",customer_ids,")");
		END IF;
		IF partner_ids IS NOT NULL AND partner_ids != "" THEN
			SET query = CONCAT(query," ","AND r.partner_id IN (",partner_ids,")");
		END IF;

		SET query = CONCAT(query," ",") tmp GROUP BY tmp.vehicle_team_id, DATE_FORMAT(tmp.date,'%Y-%m-%d')
																	) tmp2 ON tmp1.id = tmp2.vehicle_team_id AND tmp1.date = tmp2.date ");

		SET query = CONCAT(query," ","WHERE tmp1.del_flag = 0 OR (tmp2.cost IS NOT NULL AND tmp2.cost != 0 )");

	 IF summary = 0 THEN
			SET @SQLText = CONCAT(query,' ','ORDER BY tmp1.name,tmp1.date');
			PREPARE stmt FROM @SQLText;
	 ELSE
			SET summaryQuery = "SELECT tmp3.date as date, SUM(tmp3.status_complete) as total";
			SET @SQLText = CONCAT(summaryQuery," FROM(", query,") tmp3 GROUP BY tmp3.date ORDER BY STR_TO_DATE(tmp3.date,'%d-%m-%Y')");
			PREPARE stmt FROM @SQLText;
	 END IF;

   EXECUTE stmt;
   DEALLOCATE PREPARE stmt;
END
;


-- ----------------------------
-- Procedure structure for proc_report_vehicle_team_by_cost_monthly
-- ----------------------------
DROP PROCEDURE IF EXISTS `proc_report_vehicle_team_by_cost_monthly`;

CREATE PROCEDURE `proc_report_vehicle_team_by_cost_monthly`(IN summary BOOLEAN,
	IN vehicle_team_ids TEXT,IN vehicle_ids TEXT,IN driver_ids TEXT,
	IN customer_ids TEXT,IN from_date TEXT,IN to_date TEXT,IN dayCondition TEXT,IN partner_ids TEXT)
BEGIN

	DECLARE query TEXT;
	DECLARE summaryQuery TEXT;

	SET query = "SELECT tmp1.id as entity_id, tmp1.name as entity_name, DATE_FORMAT(STR_TO_DATE(tmp1.date,'%Y-%m'),'%m-%Y') as date, tmp2.cost as status_complete";
	SET query = CONCAT(query," ","FROM (SELECT vt.id,vt.name,cal.date,vt.del_flag FROM ((SELECT vtm.id,vtm.name,vtm.del_flag,vtm.partner_id FROM vehicle_team vtm) UNION (SELECT 0 as id ,'*Chưa gán đội tài xế' as name,0 as del_flag,0 as partner_id)) vt");

	SET query = CONCAT(query," ","CROSS JOIN (select DATE_FORMAT(aDate,'%Y-%m') as date from (select @maxDate - interval (a.a + (10 * b.a) + (100 * c.a)) month as aDate from
																						(select 0 as a union all select 1 union all select 2 union all select 3 union all select 4 union all select 5 union all select 6 union all select 7
																						union all select 8 union all select 9) a,
																						(select 0 as a union all select 1 union all select 2 union all select 3 union all select 4 union all select 5 union all select 6 union all select 7
																						union all select 8 union all select 9) b,
																						(select 0 as a union all select 1 union all select 2 union all select 3 union all select 4 union all select 5 union all select 6 union all select 7
																						union all select 8 union all select 9) c,
																						(select @minDate := '",from_date,"', @maxDate := '",to_date,"') d) e
																						where DATE_FORMAT(aDate,'%Y-%m') BETWEEN DATE_FORMAT(@minDate, '%Y-%m') AND DATE_FORMAT(@maxDate, '%Y-%m')) cal
																						WHERE 1=1");

	IF vehicle_team_ids IS NOT NULL AND vehicle_team_ids != "" THEN
		SET query = CONCAT(query," ","AND vt.id IN (",vehicle_team_ids,")");
	END IF;

	IF partner_ids IS NOT NULL AND partner_ids != "" THEN
		SET query = CONCAT(query," ","AND vt.partner_id IN (",partner_ids,")");
	END IF;

	SET query = CONCAT(query," ",") tmp1 LEFT JOIN (SELECT DATE_FORMAT(tmp.date,'%Y-%m') as date,tmp.vehicle_team_id, SUM(tmp.final_cost) as cost");

		SET query = CONCAT(query," ","FROM (SELECT DISTINCT r.id,
																	(CASE WHEN (dvt.vehicle_team_id IS NULL OR dvt.vehicle_team_id = 0 ) THEN 0 ELSE dvt.vehicle_team_id END) as vehicle_team_id,
																	r.final_cost");
		IF dayCondition = 1 THEN
			SET query = CONCAT(query," ",",r.ETD_date as date");
		ELSEIF dayCondition = 2 THEN
			SET query = CONCAT(query," ",",r.ETD_date_reality as date");
		ELSEIF dayCondition = 3 THEN
			SET query = CONCAT(query," ",",r.ETA_date as date");
		ELSE
			SET query = CONCAT(query," ",",r.ETA_date_reality as date");
		END IF;

		SET query = CONCAT(query," ","FROM routes r
																	LEFT JOIN ( SELECT DISTINCT ro.route_id, o.customer_id FROM route_order ro
																							INNER JOIN orders o ON o.id = ro.order_id WHERE ro.del_flag = 0 AND o.del_flag = 0
																						) a ON a.route_id = r.id
																	LEFT JOIN driver_vehicle_team dvt ON dvt.driver_id = r.driver_id
																	WHERE r.del_flag = 0 AND r.is_approved = 1 AND r.route_status IN (1,0)");
		IF dayCondition = 1 THEN
			SET query = CONCAT(query," "," AND r.ETD_date IS NOT NULL AND DATE_FORMAT(r.ETD_date,'%Y-%m') BETWEEN DATE_FORMAT('",from_date,"', '%Y-%m') AND DATE_FORMAT('",to_date,"', '%Y-%m')");
		ELSEIF dayCondition = 2 THEN
			SET query = CONCAT(query," "," AND r.ETD_date_reality IS NOT NULL AND DATE_FORMAT(r.ETD_date_reality,'%Y-%m') BETWEEN DATE_FORMAT('",from_date,"', '%Y-%m') AND DATE_FORMAT('",to_date,"', '%Y-%m')");
		ELSEIF dayCondition = 3 THEN
			SET query = CONCAT(query," "," AND r.ETA_date IS NOT NULL AND DATE_FORMAT(r.ETA_date,'%Y-%m') BETWEEN DATE_FORMAT('",from_date,"', '%Y-%m') AND DATE_FORMAT('",to_date,"', '%Y-%m')");
		ELSE
			SET query = CONCAT(query," "," AND r.ETA_date_reality IS NOT NULL AND DATE_FORMAT(r.ETA_date_reality,'%Y-%m') BETWEEN DATE_FORMAT('",from_date,"', '%Y-%m') AND DATE_FORMAT('",to_date,"', '%Y-%m')");
		END IF;

		IF vehicle_team_ids IS NOT NULL AND vehicle_team_ids != "" THEN
			SET query = CONCAT(query," ","AND dvt.vehicle_team_id IN (",vehicle_team_ids,")");
		END IF;
		IF driver_ids IS NOT NULL AND driver_ids != "" THEN
			SET query = CONCAT(query," ","AND r.driver_id IN (",driver_ids,")");
		END IF;
		IF vehicle_ids IS NOT NULL AND vehicle_ids != "" THEN
			SET query = CONCAT(query," ","AND r.vehicle_id IN (",vehicle_ids,")");
		END IF;
		IF customer_ids IS NOT NULL AND customer_ids != "" THEN
			SET query = CONCAT(query," ","AND a.customer_id IN (",customer_ids,")");
		END IF;
		IF partner_ids IS NOT NULL AND partner_ids != "" THEN
			SET query = CONCAT(query," ","AND r.partner_id IN (",partner_ids,")");
		END IF;

		SET query = CONCAT(query," ",") tmp GROUP BY tmp.vehicle_team_id, DATE_FORMAT(tmp.date,'%Y-%m')
																	) tmp2 ON tmp1.id = tmp2.vehicle_team_id AND tmp1.date = tmp2.date ");

		SET query = CONCAT(query," ","WHERE tmp1.del_flag = 0 OR (tmp2.cost IS NOT NULL AND tmp2.cost != 0 )");

	 IF summary = 0 THEN
			SET @SQLText = CONCAT(query,' ','ORDER BY tmp1.name,tmp1.date');
			-- SELECT @SQLText;
			PREPARE stmt FROM @SQLText;
	 ELSE
			SET summaryQuery = "SELECT tmp3.date as date, SUM(tmp3.status_complete) as total";
			SET @SQLText = CONCAT(summaryQuery," FROM(", query,") tmp3 GROUP BY tmp3.date ORDER BY STR_TO_DATE(tmp3.date,'%m-%Y')");
			PREPARE stmt FROM @SQLText;
	 END IF;

   EXECUTE stmt;
   DEALLOCATE PREPARE stmt;
END
;


-- ----------------------------
-- Procedure structure for proc_report_vehicle_team_by_income
-- ----------------------------
DROP PROCEDURE IF EXISTS `proc_report_vehicle_team_by_income`;

CREATE PROCEDURE `proc_report_vehicle_team_by_income`(IN summary BOOLEAN,IN vehicle_team_ids TEXT,
	IN vehicle_ids TEXT,IN driver_ids TEXT,IN customer_ids TEXT,IN from_date TEXT,IN to_date TEXT,IN dayCondition TEXT,IN partner_ids TEXT)
BEGIN

	DECLARE query TEXT;
	DECLARE summaryQuery TEXT;

	SET query = "SELECT tmp1.id as entity_id, tmp1.name as entity_name,DATE_FORMAT(tmp1.date,'%d-%m-%Y') as date,tmp2.status_complete";

	SET query = CONCAT(query," ","FROM (SELECT vt.id,vt.name,cal.date,vt.del_flag,vt.partner_id FROM ((SELECT vtm.id,vtm.name,vtm.del_flag,vtm.partner_id FROM vehicle_team vtm) UNION (SELECT 0 as id ,'*Chưa gán đội tài xế' as name,0 as del_flag,0 as partner_id)) vt");

	SET query = CONCAT(query," ","CROSS JOIN (
										SELECT selected_date as date FROM (
													SELECT adddate('1970-01-01',t4.i*10000 + t3.i*1000 + t2.i*100 + t1.i*10 + t0.i) selected_date
													FROM
														 (SELECT 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t0,
														 (SELECT 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t1,
														 (SELECT 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t2,
														 (SELECT 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t3,
														 (SELECT 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t4
										) d
										WHERE selected_date BETWEEN '",from_date,"' AND '",to_date,"'
										) cal
										WHERE 1=1 ");

	IF vehicle_team_ids IS NOT NULL AND vehicle_team_ids != "" THEN
		SET query = CONCAT(query," ","AND vt.id IN (",vehicle_team_ids,")");
	END IF;

	IF partner_ids IS NOT NULL AND partner_ids != "" THEN
		SET query = CONCAT(query," ","AND vt.partner_id IN (",partner_ids,")");
	END IF;

	SET query = CONCAT(query," ",") tmp1 LEFT JOIN (SELECT tmp.date,tmp.vehicle_team_id,SUM(status_complete) as status_complete");

	SET query = CONCAT(query," ","FROM (SELECT DISTINCT o.id,
																(CASE WHEN (dvt.vehicle_team_id IS NULL OR dvt.vehicle_team_id = 0 ) THEN 0 ELSE dvt.vehicle_team_id END) as vehicle_team_id,
																o.amount as status_complete , ");

	IF dayCondition = 1 THEN
		SET query = CONCAT(query," "," o.ETD_date as date");
	ELSEIF dayCondition = 2 THEN
		SET query = CONCAT(query," "," o.ETD_date_reality as date");
	ELSEIF dayCondition = 3 THEN
		SET query = CONCAT(query," "," o.ETA_date as date");
	ELSE
		SET query = CONCAT(query," "," o.ETA_date_reality as date");
	END IF;

	SET query = CONCAT(query," ","FROM orders o LEFT JOIN driver_vehicle_team dvt ON dvt.driver_id = o.primary_driver_id");

	SET query = CONCAT(query," ","WHERE o.del_flag = 0 ");

	IF dayCondition = 1 THEN
		SET query = CONCAT(query," "," AND o.status IN(2,3,4,5,7) AND o.ETD_date BETWEEN '",from_date,"' AND '",to_date,"' ");
	ELSEIF dayCondition = 2 THEN
		SET query = CONCAT(query," "," AND o.status IN (4,5) AND o.ETD_date_reality BETWEEN '",from_date,"' AND '",to_date,"' ");
	ELSEIF dayCondition = 3 THEN
		SET query = CONCAT(query," "," AND o.status IN(2,3,4,5,7) AND o.ETA_date BETWEEN '",from_date,"' AND '",to_date,"' ");
	ELSE
		SET query = CONCAT(query," "," AND o.status = 5 AND o.ETA_date_reality BETWEEN '",from_date,"' AND '",to_date,"' ");
	END IF;

	IF vehicle_team_ids IS NOT NULL AND vehicle_team_ids != "" THEN
		SET query = CONCAT(query," "," AND dvt.vehicle_team_id IN (",vehicle_team_ids,")");
	END IF;
	IF driver_ids IS NOT NULL AND driver_ids != "" THEN
		SET query = CONCAT(query," ","AND o.primary_driver_id IN (",driver_ids,")");
	END IF;
	IF vehicle_ids IS NOT NULL AND vehicle_ids != "" THEN
		SET query = CONCAT(query," ","AND o.vehicle_id IN (",vehicle_ids,")");
	END IF;
	IF customer_ids IS NOT NULL AND customer_ids != "" THEN
		SET query = CONCAT(query," ","AND o.customer_id IN (",customer_ids,")");
	END IF;
	IF partner_ids IS NOT NULL AND partner_ids != "" THEN
		SET query = CONCAT(query," ","AND o.partner_id IN (",partner_ids,")");
	END IF;

	SET query = CONCAT(query," ",") tmp GROUP BY tmp.vehicle_team_id,tmp.date ) tmp2 ON tmp1.id = tmp2.vehicle_team_id AND tmp1.date = tmp2.date");

	SET query = CONCAT(query," ","WHERE tmp1.del_flag = 0 OR (tmp2.status_complete IS NOT NULL AND tmp2.status_complete != 0 )");

	 IF summary = 0 THEN
			SET @SQLText = CONCAT(query,' ','ORDER BY tmp1.name,tmp1.date');
			-- SELECT @SQLText;
			PREPARE stmt FROM @SQLText;
	 ELSE
			SET summaryQuery = "SELECT tmp3.date as date,SUM(tmp3.status_complete) as total";

			SET @SQLText = CONCAT(summaryQuery," FROM(", query,") tmp3 GROUP BY tmp3.date ORDER BY STR_TO_DATE(tmp3.date,'%d-%m-%Y')");
			PREPARE stmt FROM @SQLText;
	 END IF;

   EXECUTE stmt;
   DEALLOCATE PREPARE stmt;
END
;


-- ----------------------------
-- Procedure structure for proc_report_vehicle_team_by_income_monthly
-- ----------------------------
DROP PROCEDURE IF EXISTS `proc_report_vehicle_team_by_income_monthly`;

CREATE PROCEDURE `proc_report_vehicle_team_by_income_monthly`(IN summary BOOLEAN,IN vehicle_team_ids TEXT,
	IN vehicle_ids TEXT,IN driver_ids TEXT,IN customer_ids TEXT,IN from_date TEXT,IN to_date TEXT,IN dayCondition TEXT,IN partner_ids TEXT)
BEGIN

	DECLARE query TEXT;
	DECLARE summaryQuery TEXT;

	SET query = "SELECT tmp1.id as entity_id, tmp1.name as entity_name,DATE_FORMAT(STR_TO_DATE(tmp1.date,'%Y-%m'),'%m-%Y') as date,tmp2.status_complete";

	SET query = CONCAT(query," ","FROM (SELECT vt.id,vt.name,cal.date,vt.del_flag,vt.partner_id FROM ((SELECT vtm.id,vtm.name,vtm.del_flag,vtm.partner_id FROM vehicle_team vtm) UNION (SELECT 0 as id ,'*Chưa gán đội tài xế' as name,0 as del_flag,0 as partner_id)) vt");

	SET query = CONCAT(query," ","CROSS JOIN (select DATE_FORMAT(aDate,'%Y-%m') as date from (select @maxDate - interval (a.a + (10 * b.a) + (100 * c.a)) month as aDate from
																						(select 0 as a union all select 1 union all select 2 union all select 3 union all select 4 union all select 5 union all select 6 union all select 7
																						union all select 8 union all select 9) a,
																						(select 0 as a union all select 1 union all select 2 union all select 3 union all select 4 union all select 5 union all select 6 union all select 7
																						union all select 8 union all select 9) b,
																						(select 0 as a union all select 1 union all select 2 union all select 3 union all select 4 union all select 5 union all select 6 union all select 7
																						union all select 8 union all select 9) c,
																						(select @minDate := '",from_date,"', @maxDate := '",to_date,"') d) e
																						where DATE_FORMAT(aDate,'%Y-%m') BETWEEN DATE_FORMAT(@minDate, '%Y-%m') AND DATE_FORMAT(@maxDate, '%Y-%m')) cal
																						WHERE 1=1");

	IF vehicle_team_ids IS NOT NULL AND vehicle_team_ids != "" THEN
		SET query = CONCAT(query," ","AND vt.id IN (",vehicle_team_ids,")");
	END IF;

	IF partner_ids IS NOT NULL AND partner_ids != "" THEN
		SET query = CONCAT(query," ","AND vt.partner_id IN (",partner_ids,")");
	END IF;

	SET query = CONCAT(query," ",") tmp1 LEFT JOIN (SELECT tmp.date,tmp.vehicle_team_id,SUM(status_complete) as status_complete");

	SET query = CONCAT(query," ","FROM (SELECT DISTINCT o.id,
																						(CASE WHEN (dvt.vehicle_team_id IS NULL OR dvt.vehicle_team_id = 0 ) THEN 0 ELSE dvt.vehicle_team_id END) as vehicle_team_id,
																						o.amount as status_complete , ");

	IF dayCondition = 1 THEN
		SET query = CONCAT(query," "," DATE_FORMAT(o.ETD_date,'%Y-%m') as date");
	ELSEIF dayCondition = 2 THEN
		SET query = CONCAT(query," "," DATE_FORMAT(o.ETD_date_reality,'%Y-%m') as date");
	ELSEIF dayCondition = 3 THEN
		SET query = CONCAT(query," "," DATE_FORMAT(o.ETA_date,'%Y-%m') as date");
	ELSE
		SET query = CONCAT(query," "," DATE_FORMAT(o.ETA_date_reality,'%Y-%m') as date");
	END IF;

	SET query = CONCAT(query," ","FROM orders o LEFT JOIN driver_vehicle_team dvt ON dvt.driver_id = o.primary_driver_id");

	SET query = CONCAT(query," ","WHERE o.del_flag = 0 ");

	IF dayCondition = 1 THEN
		SET query = CONCAT(query," "," AND o.status IN(2,3,4,5,7) AND DATE_FORMAT(o.ETD_date,'%Y-%m') BETWEEN DATE_FORMAT('",from_date,"','%Y-%m') AND DATE_FORMAT('",to_date,"','%Y-%m') ");
	ELSEIF dayCondition = 2 THEN
		SET query = CONCAT(query," "," AND o.status IN (4,5) AND DATE_FORMAT(o.ETD_date_reality,'%Y-%m') BETWEEN DATE_FORMAT('",from_date,"','%Y-%m') AND DATE_FORMAT('",to_date,"','%Y-%m') ");
	ELSEIF dayCondition = 3 THEN
		SET query = CONCAT(query," "," AND o.status IN(2,3,4,5,7) AND DATE_FORMAT(o.ETA_date,'%Y-%m') BETWEEN DATE_FORMAT('",from_date,"','%Y-%m') AND DATE_FORMAT('",to_date,"','%Y-%m') ");
	ELSE
		SET query = CONCAT(query," "," AND o.status = 5 AND DATE_FORMAT(o.ETA_date_reality,'%Y-%m') BETWEEN DATE_FORMAT('",from_date,"','%Y-%m') AND DATE_FORMAT('",to_date,"','%Y-%m') ");
	END IF;

	IF vehicle_team_ids IS NOT NULL AND vehicle_team_ids != "" THEN
		SET query = CONCAT(query," "," AND dvt.vehicle_team_id IN (",vehicle_team_ids,")");
	END IF;
	IF driver_ids IS NOT NULL AND driver_ids != "" THEN
		SET query = CONCAT(query," ","AND o.primary_driver_id IN (",driver_ids,")");
	END IF;
	IF vehicle_ids IS NOT NULL AND vehicle_ids != "" THEN
		SET query = CONCAT(query," ","AND o.vehicle_id IN (",vehicle_ids,")");
	END IF;
	IF customer_ids IS NOT NULL AND customer_ids != "" THEN
		SET query = CONCAT(query," ","AND o.customer_id IN (",customer_ids,")");
	END IF;
	IF partner_ids IS NOT NULL AND partner_ids != "" THEN
		SET query = CONCAT(query," ","AND o.partner_id IN (",partner_ids,")");
	END IF;

	SET query = CONCAT(query," ",") tmp GROUP BY tmp.vehicle_team_id,tmp.date ) tmp2 ON tmp1.id = tmp2.vehicle_team_id AND tmp1.date = tmp2.date");

	SET query = CONCAT(query," ","WHERE tmp1.del_flag = 0 OR (tmp2.status_complete IS NOT NULL AND tmp2.status_complete != 0 )");

	 IF summary = 0 THEN
			SET @SQLText = CONCAT(query,' ','ORDER BY tmp1.name,tmp1.date');
			-- SELECT @SQLText;
			PREPARE stmt FROM @SQLText;
	 ELSE
			SET summaryQuery = "SELECT tmp3.date as date,SUM(tmp3.status_complete) as total";

			SET @SQLText = CONCAT(summaryQuery," FROM(", query,") tmp3 GROUP BY tmp3.date ORDER BY STR_TO_DATE(tmp3.date,'%m-%Y')");
			PREPARE stmt FROM @SQLText;
	 END IF;

   EXECUTE stmt;
   DEALLOCATE PREPARE stmt;
END
;


-- ----------------------------
-- Procedure structure for proc_report_vehicle_team_by_profit
-- ----------------------------
DROP PROCEDURE IF EXISTS `proc_report_vehicle_team_by_profit`;

CREATE PROCEDURE `proc_report_vehicle_team_by_profit`(IN summary BOOLEAN,IN vehicle_team_ids TEXT,
	IN vehicle_ids TEXT,IN driver_ids TEXT,IN customer_ids TEXT,IN from_date TEXT,IN to_date TEXT,IN dayCondition TEXT,IN partner_ids TEXT)
BEGIN

	DECLARE query TEXT;
	DECLARE summaryQuery TEXT;

	SET query = "SELECT tmp1.id as entity_id, tmp1.name as entity_name,DATE_FORMAT(tmp1.date,'%d-%m-%Y') as date,
								((CASE WHEN tmp2.income IS NULL THEN 0 ELSE tmp2.income END) - (CASE WHEN tmp3.cost IS NULL THEN 0 ELSE tmp3.cost END)) as status_complete";

	SET query = CONCAT(query," ","FROM (SELECT vt.id,vt.name,cal.date,vt.del_flag,vt.partner_id FROM ((SELECT vtm.id,vtm.name,vtm.del_flag,vtm.partner_id FROM vehicle_team vtm) UNION (SELECT 0 as id ,'*Chưa gán đội tài xế' as name,0 as del_flag,0 as partner_id)) vt");

	SET query = CONCAT(query," ","CROSS JOIN (
										SELECT selected_date as date FROM (
													SELECT adddate('1970-01-01',t4.i*10000 + t3.i*1000 + t2.i*100 + t1.i*10 + t0.i) selected_date
													FROM
														 (SELECT 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t0,
														 (SELECT 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t1,
														 (SELECT 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t2,
														 (SELECT 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t3,
														 (SELECT 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t4
										) d
										WHERE selected_date BETWEEN '",from_date,"' AND '",to_date,"'
										) cal
										WHERE 1=1 ");

	IF vehicle_team_ids IS NOT NULL AND vehicle_team_ids != "" THEN
		SET query = CONCAT(query," ","AND vt.id IN (",vehicle_team_ids,")");
	END IF;

	IF partner_ids IS NOT NULL AND partner_ids != "" THEN
		SET query = CONCAT(query," ","AND vt.partner_id IN (",partner_ids,")");
	END IF;

	SET query = CONCAT(query," ",") tmp1 LEFT JOIN (SELECT tmp.date,tmp.vehicle_team_id,SUM(status_complete) as income");

	SET query = CONCAT(query," ","FROM (SELECT DISTINCT o.id,
																(CASE WHEN (dvt.vehicle_team_id IS NULL OR dvt.vehicle_team_id = 0 ) THEN 0 ELSE dvt.vehicle_team_id END) as vehicle_team_id,
																o.amount as status_complete , ");

	IF dayCondition = 1 THEN
		SET query = CONCAT(query," "," o.ETD_date as date");
	ELSEIF dayCondition = 2 THEN
		SET query = CONCAT(query," "," o.ETD_date_reality as date");
	ELSEIF dayCondition = 3 THEN
		SET query = CONCAT(query," "," o.ETA_date as date");
	ELSE
		SET query = CONCAT(query," "," o.ETA_date_reality as date");
	END IF;

	SET query = CONCAT(query," ","FROM orders o LEFT JOIN driver_vehicle_team dvt ON dvt.driver_id = o.primary_driver_id");

	SET query = CONCAT(query," ","WHERE o.del_flag = 0 ");

	IF dayCondition = 1 THEN
		SET query = CONCAT(query," "," AND o.status IN(2,3,4,5,7) AND o.ETD_date BETWEEN '",from_date,"' AND '",to_date,"' ");
	ELSEIF dayCondition = 2 THEN
		SET query = CONCAT(query," "," AND o.status IN (4,5) AND o.ETD_date_reality BETWEEN '",from_date,"' AND '",to_date,"' ");
	ELSEIF dayCondition = 3 THEN
		SET query = CONCAT(query," "," AND o.status IN(2,3,4,5,7) AND o.ETA_date BETWEEN '",from_date,"' AND '",to_date,"' ");
	ELSE
		SET query = CONCAT(query," "," AND o.status = 5 AND o.ETA_date_reality BETWEEN '",from_date,"' AND '",to_date,"' ");
	END IF;

	IF vehicle_team_ids IS NOT NULL AND vehicle_team_ids != "" THEN
		SET query = CONCAT(query," "," AND dvt.vehicle_team_id IN (",vehicle_team_ids,")");
	END IF;
	IF driver_ids IS NOT NULL AND driver_ids != "" THEN
		SET query = CONCAT(query," ","AND o.primary_driver_id IN (",driver_ids,")");
	END IF;
	IF vehicle_ids IS NOT NULL AND vehicle_ids != "" THEN
		SET query = CONCAT(query," ","AND o.vehicle_id IN (",vehicle_ids,")");
	END IF;
	IF customer_ids IS NOT NULL AND customer_ids != "" THEN
		SET query = CONCAT(query," ","AND o.customer_id IN (",customer_ids,")");
	END IF;
	IF partner_ids IS NOT NULL AND partner_ids != "" THEN
		SET query = CONCAT(query," ","AND o.partner_id IN (",partner_ids,")");
	END IF;

	SET query = CONCAT(query," ",") tmp GROUP BY tmp.vehicle_team_id,tmp.date ) tmp2 ON tmp1.id = tmp2.vehicle_team_id AND tmp1.date = tmp2.date");

	SET query = CONCAT(query," ","LEFT JOIN (SELECT tmp.date , tmp.vehicle_team_id, SUM(tmp.final_cost) as cost");

		SET query = CONCAT(query," ","FROM (SELECT DISTINCT r.id,
																	(CASE WHEN (dvt.vehicle_team_id IS NULL OR dvt.vehicle_team_id = 0 ) THEN 0 ELSE dvt.vehicle_team_id END) as vehicle_team_id,
																	r.final_cost");
		IF dayCondition = 1 THEN
			SET query = CONCAT(query," ",",r.ETD_date as date");
		ELSEIF dayCondition = 2 THEN
			SET query = CONCAT(query," ",",r.ETD_date_reality as date");
		ELSEIF dayCondition = 3 THEN
			SET query = CONCAT(query," ",",r.ETA_date as date");
		ELSE
			SET query = CONCAT(query," ",",r.ETA_date_reality as date");
		END IF;

		SET query = CONCAT(query," ","FROM routes r
																	LEFT JOIN ( SELECT DISTINCT ro.route_id, o.customer_id FROM route_order ro
																							INNER JOIN orders o ON o.id = ro.order_id WHERE ro.del_flag = 0 AND o.del_flag = 0
																						) a ON a.route_id = r.id
																	LEFT JOIN driver_vehicle_team dvt ON dvt.driver_id = r.driver_id
																	WHERE r.del_flag = 0 AND r.is_approved = 1 AND r.route_status IN (1,0)");
		IF dayCondition = 1 THEN
			SET query = CONCAT(query," "," AND r.ETD_date IS NOT NULL AND r.ETD_date BETWEEN '",from_date,"' AND '",to_date,"' ");
		ELSEIF dayCondition = 2 THEN
			SET query = CONCAT(query," "," AND r.ETD_date_reality IS NOT NULL AND r.ETD_date_reality BETWEEN '",from_date,"' AND '",to_date,"' ");
		ELSEIF dayCondition = 3 THEN
			SET query = CONCAT(query," "," AND r.ETA_date IS NOT NULL AND r.ETA_date BETWEEN '",from_date,"' AND '",to_date,"' ");
		ELSE
			SET query = CONCAT(query," "," AND r.ETA_date_reality IS NOT NULL AND r.ETA_date_reality BETWEEN '",from_date,"' AND '",to_date,"' ");
		END IF;

		IF vehicle_team_ids IS NOT NULL AND vehicle_team_ids != "" THEN
			SET query = CONCAT(query," ","AND dvt.vehicle_team_id IN (",vehicle_team_ids,")");
		END IF;
		IF driver_ids IS NOT NULL AND driver_ids != "" THEN
			SET query = CONCAT(query," ","AND r.driver_id IN (",driver_ids,")");
		END IF;
		IF vehicle_ids IS NOT NULL AND vehicle_ids != "" THEN
			SET query = CONCAT(query," ","AND r.vehicle_id IN (",vehicle_ids,")");
		END IF;
		IF customer_ids IS NOT NULL AND customer_ids != "" THEN
			SET query = CONCAT(query," ","AND a.customer_id IN (",customer_ids,")");
		END IF;
		IF partner_ids IS NOT NULL AND partner_ids != "" THEN
			SET query = CONCAT(query," ","AND r.partner_id IN (",partner_ids,")");
		END IF;

		SET query = CONCAT(query," ",") tmp GROUP BY tmp.vehicle_team_id, DATE_FORMAT(tmp.date,'%Y-%m-%d')
																	) tmp3 ON tmp1.id = tmp3.vehicle_team_id AND tmp1.date = tmp3.date ");

	SET query = CONCAT(query," ","WHERE tmp1.del_flag = 0 OR ((CASE WHEN tmp2.income IS NULL THEN 0 ELSE tmp2.income END) - (CASE WHEN tmp3.cost IS NULL THEN 0 ELSE tmp3.cost END)) != 0 ");

	 IF summary = 0 THEN
			SET @SQLText = CONCAT(query,' ','ORDER BY tmp1.name,tmp1.date');
			-- SELECT @SQLText;
			PREPARE stmt FROM @SQLText;
	 ELSE
			SET summaryQuery = "SELECT tmp4.date as date,SUM(tmp4.status_complete) as total";

			SET @SQLText = CONCAT(summaryQuery," FROM(", query,") tmp4 GROUP BY tmp4.date ORDER BY STR_TO_DATE(tmp4.date,'%d-%m-%Y')");
			PREPARE stmt FROM @SQLText;
	 END IF;

   EXECUTE stmt;
   DEALLOCATE PREPARE stmt;
END
;


-- ----------------------------
-- Procedure structure for proc_report_vehicle_team_by_profit_monthly
-- ----------------------------
DROP PROCEDURE IF EXISTS `proc_report_vehicle_team_by_profit_monthly`;

CREATE PROCEDURE `proc_report_vehicle_team_by_profit_monthly`(IN summary BOOLEAN,IN vehicle_team_ids TEXT,
	IN vehicle_ids TEXT,IN driver_ids TEXT,IN customer_ids TEXT,IN from_date TEXT,IN to_date TEXT,IN dayCondition TEXT,IN partner_ids TEXT)
BEGIN

	DECLARE query TEXT;
	DECLARE summaryQuery TEXT;

	SET query = "SELECT tmp1.id as entity_id, tmp1.name as entity_name,DATE_FORMAT(STR_TO_DATE(tmp1.date,'%Y-%m'),'%m-%Y') as date,
								((CASE WHEN tmp2.income IS NULL THEN 0 ELSE tmp2.income END) - (CASE WHEN tmp3.cost IS NULL THEN 0 ELSE tmp3.cost END)) as status_complete";

		SET query = CONCAT(query," ","FROM (SELECT vt.id,vt.name,cal.date,vt.del_flag,vt.partner_id FROM ((SELECT vtm.id,vtm.name,vtm.del_flag,vtm.partner_id FROM vehicle_team vtm) UNION (SELECT 0 as id ,'*Chưa gán đội tài xế' as name,0 as del_flag,0 as partner_id)) vt");

	SET query = CONCAT(query," ","CROSS JOIN (select DATE_FORMAT(aDate,'%Y-%m') as date from (select @maxDate - interval (a.a + (10 * b.a) + (100 * c.a)) month as aDate from
																						(select 0 as a union all select 1 union all select 2 union all select 3 union all select 4 union all select 5 union all select 6 union all select 7
																						union all select 8 union all select 9) a,
																						(select 0 as a union all select 1 union all select 2 union all select 3 union all select 4 union all select 5 union all select 6 union all select 7
																						union all select 8 union all select 9) b,
																						(select 0 as a union all select 1 union all select 2 union all select 3 union all select 4 union all select 5 union all select 6 union all select 7
																						union all select 8 union all select 9) c,
																						(select @minDate := '",from_date,"', @maxDate := '",to_date,"') d) e
																						where DATE_FORMAT(aDate,'%Y-%m') BETWEEN DATE_FORMAT(@minDate, '%Y-%m') AND DATE_FORMAT(@maxDate, '%Y-%m')) cal
																						WHERE 1=1");

	IF vehicle_team_ids IS NOT NULL AND vehicle_team_ids != "" THEN
		SET query = CONCAT(query," ","AND vt.id IN (",vehicle_team_ids,")");
	END IF;

	IF partner_ids IS NOT NULL AND partner_ids != "" THEN
		SET query = CONCAT(query," ","AND vt.partner_id IN (",partner_ids,")");
	END IF;

	SET query = CONCAT(query," ",") tmp1 LEFT JOIN (SELECT tmp.date,tmp.vehicle_team_id,SUM(status_complete) as income");

	SET query = CONCAT(query," ","FROM (SELECT DISTINCT o.id,
																						(CASE WHEN (dvt.vehicle_team_id IS NULL OR dvt.vehicle_team_id = 0 ) THEN 0 ELSE dvt.vehicle_team_id END) as vehicle_team_id,
																						o.amount as status_complete , ");

	IF dayCondition = 1 THEN
		SET query = CONCAT(query," "," DATE_FORMAT(o.ETD_date,'%Y-%m') as date");
	ELSEIF dayCondition = 2 THEN
		SET query = CONCAT(query," "," DATE_FORMAT(o.ETD_date_reality,'%Y-%m') as date");
	ELSEIF dayCondition = 3 THEN
		SET query = CONCAT(query," "," DATE_FORMAT(o.ETA_date,'%Y-%m') as date");
	ELSE
		SET query = CONCAT(query," "," DATE_FORMAT(o.ETA_date_reality,'%Y-%m') as date");
	END IF;

	SET query = CONCAT(query," ","FROM orders o LEFT JOIN driver_vehicle_team dvt ON dvt.driver_id = o.primary_driver_id");

	SET query = CONCAT(query," ","WHERE o.del_flag = 0 ");

	IF dayCondition = 1 THEN
		SET query = CONCAT(query," "," AND o.status IN(2,3,4,5,7) AND DATE_FORMAT(o.ETD_date,'%Y-%m') BETWEEN DATE_FORMAT('",from_date,"','%Y-%m') AND DATE_FORMAT('",to_date,"','%Y-%m') ");
	ELSEIF dayCondition = 2 THEN
		SET query = CONCAT(query," "," AND o.status IN (4,5) AND DATE_FORMAT(o.ETD_date_reality,'%Y-%m') BETWEEN DATE_FORMAT('",from_date,"','%Y-%m') AND DATE_FORMAT('",to_date,"','%Y-%m') ");
	ELSEIF dayCondition = 3 THEN
		SET query = CONCAT(query," "," AND o.status IN(2,3,4,5,7) AND DATE_FORMAT(o.ETA_date,'%Y-%m') BETWEEN DATE_FORMAT('",from_date,"','%Y-%m') AND DATE_FORMAT('",to_date,"','%Y-%m') ");
	ELSE
		SET query = CONCAT(query," "," AND o.status = 5 AND DATE_FORMAT(o.ETA_date_reality,'%Y-%m') BETWEEN DATE_FORMAT('",from_date,"','%Y-%m') AND DATE_FORMAT('",to_date,"','%Y-%m') ");
	END IF;

	IF vehicle_team_ids IS NOT NULL AND vehicle_team_ids != "" THEN
		SET query = CONCAT(query," "," AND dvt.vehicle_team_id IN (",vehicle_team_ids,")");
	END IF;
	IF driver_ids IS NOT NULL AND driver_ids != "" THEN
		SET query = CONCAT(query," ","AND o.primary_driver_id IN (",driver_ids,")");
	END IF;
	IF vehicle_ids IS NOT NULL AND vehicle_ids != "" THEN
		SET query = CONCAT(query," ","AND o.vehicle_id IN (",vehicle_ids,")");
	END IF;
	IF customer_ids IS NOT NULL AND customer_ids != "" THEN
		SET query = CONCAT(query," ","AND o.customer_id IN (",customer_ids,")");
	END IF;
	IF partner_ids IS NOT NULL AND partner_ids != "" THEN
		SET query = CONCAT(query," ","AND o.partner_id IN (",partner_ids,")");
	END IF;

	SET query = CONCAT(query," ",") tmp GROUP BY tmp.vehicle_team_id,tmp.date ) tmp2 ON tmp1.id = tmp2.vehicle_team_id AND tmp1.date = tmp2.date");

	SET query = CONCAT(query," ","LEFT JOIN (SELECT DATE_FORMAT(tmp.date,'%Y-%m') date, tmp.vehicle_team_id, SUM(tmp.final_cost) as cost");

		SET query = CONCAT(query," ","FROM (SELECT DISTINCT r.id,
																	(CASE WHEN (dvt.vehicle_team_id IS NULL OR dvt.vehicle_team_id = 0 ) THEN 0 ELSE dvt.vehicle_team_id END) as vehicle_team_id,
																	r.final_cost");
		IF dayCondition = 1 THEN
			SET query = CONCAT(query," ",",r.ETD_date as date");
		ELSEIF dayCondition = 2 THEN
			SET query = CONCAT(query," ",",r.ETD_date_reality as date");
		ELSEIF dayCondition = 3 THEN
			SET query = CONCAT(query," ",",r.ETA_date as date");
		ELSE
			SET query = CONCAT(query," ",",r.ETA_date_reality as date");
		END IF;

		SET query = CONCAT(query," ","FROM routes r
																	LEFT JOIN ( SELECT DISTINCT ro.route_id, o.customer_id FROM route_order ro
																							INNER JOIN orders o ON o.id = ro.order_id WHERE ro.del_flag = 0 AND o.del_flag = 0
																						) a ON a.route_id = r.id
																	LEFT JOIN driver_vehicle_team dvt ON dvt.driver_id = r.driver_id
																	WHERE r.del_flag = 0 AND r.is_approved = 1 AND r.route_status IN (1,0)");
		IF dayCondition = 1 THEN
			SET query = CONCAT(query," "," AND r.ETD_date IS NOT NULL AND DATE_FORMAT(r.ETD_date,'%Y-%m') BETWEEN DATE_FORMAT('",from_date,"', '%Y-%m') AND DATE_FORMAT('",to_date,"', '%Y-%m')");
		ELSEIF dayCondition = 2 THEN
			SET query = CONCAT(query," "," AND r.ETD_date_reality IS NOT NULL AND DATE_FORMAT(r.ETD_date_reality,'%Y-%m') BETWEEN DATE_FORMAT('",from_date,"', '%Y-%m') AND DATE_FORMAT('",to_date,"', '%Y-%m')");
		ELSEIF dayCondition = 3 THEN
			SET query = CONCAT(query," "," AND r.ETA_date IS NOT NULL AND DATE_FORMAT(r.ETA_date,'%Y-%m') BETWEEN DATE_FORMAT('",from_date,"', '%Y-%m') AND DATE_FORMAT('",to_date,"', '%Y-%m')");
		ELSE
			SET query = CONCAT(query," "," AND r.ETA_date_reality IS NOT NULL AND DATE_FORMAT(r.ETA_date_reality,'%Y-%m') BETWEEN DATE_FORMAT('",from_date,"', '%Y-%m') AND DATE_FORMAT('",to_date,"', '%Y-%m')");
		END IF;

		IF vehicle_team_ids IS NOT NULL AND vehicle_team_ids != "" THEN
			SET query = CONCAT(query," ","AND dvt.vehicle_team_id IN (",vehicle_team_ids,")");
		END IF;
		IF driver_ids IS NOT NULL AND driver_ids != "" THEN
			SET query = CONCAT(query," ","AND r.driver_id IN (",driver_ids,")");
		END IF;
		IF vehicle_ids IS NOT NULL AND vehicle_ids != "" THEN
			SET query = CONCAT(query," ","AND r.vehicle_id IN (",vehicle_ids,")");
		END IF;
		IF customer_ids IS NOT NULL AND customer_ids != "" THEN
			SET query = CONCAT(query," ","AND a.customer_id IN (",customer_ids,")");
		END IF;
		IF partner_ids IS NOT NULL AND partner_ids != "" THEN
			SET query = CONCAT(query," ","AND r.partner_id IN (",partner_ids,")");
		END IF;

		SET query = CONCAT(query," ",") tmp GROUP BY tmp.vehicle_team_id, DATE_FORMAT(tmp.date,'%Y-%m')
																	) tmp3 ON tmp1.id = tmp3.vehicle_team_id AND tmp1.date = tmp3.date ");

	SET query = CONCAT(query," ","WHERE tmp1.del_flag = 0 OR ((CASE WHEN tmp2.income IS NULL THEN 0 ELSE tmp2.income END) - (CASE WHEN tmp3.cost IS NULL THEN 0 ELSE tmp3.cost END)) != 0 ");

	 IF summary = 0 THEN
			SET @SQLText = CONCAT(query,' ','ORDER BY tmp1.name,tmp1.date');
			-- SELECT @SQLText;
			PREPARE stmt FROM @SQLText;
	 ELSE
			SET summaryQuery = "SELECT tmp4.date as date,SUM(tmp4.status_complete) as total";

			SET @SQLText = CONCAT(summaryQuery," FROM(", query,") tmp4 GROUP BY tmp4.date ORDER BY STR_TO_DATE(tmp4.date,'%m-%Y')");
			PREPARE stmt FROM @SQLText;
	 END IF;

   EXECUTE stmt;
   DEALLOCATE PREPARE stmt;
END
;


-- ----------------------------
-- Procedure structure for proc_report_vehicle_team_by_turn
-- ----------------------------
DROP PROCEDURE IF EXISTS `proc_report_vehicle_team_by_turn`;

CREATE PROCEDURE `proc_report_vehicle_team_by_turn`(IN summary BOOLEAN,IN vehicle_team_ids TEXT,
	IN vehicle_ids TEXT,IN driver_ids TEXT,
	IN customer_ids TEXT,IN status_all BOOLEAN,IN status_incomplete BOOLEAN,
	IN status_complete BOOLEAN,IN status_cancel BOOLEAN,IN from_date TEXT,IN to_date TEXT, IN partner_ids TEXT)
BEGIN

	DECLARE query TEXT;
	DECLARE summaryQuery TEXT;

	SET query = "SELECT tmp1.id as entity_id, tmp1.name as entity_name,DATE_FORMAT(tmp1.date,'%d-%m-%Y') as date";
		IF status_all = 1 THEN SET query = CONCAT(query,",","tmp2.status_all"); END IF;
		IF status_complete = 1 THEN
			SET query = CONCAT(query,",","tmp2.status_complete");
			SET query = CONCAT(query,",","tmp2.status_on_time");
			SET query = CONCAT(query,",","tmp2.status_late");
		END IF;
		IF status_incomplete = 1 THEN SET query = CONCAT(query,",","tmp2.status_incomplete"); END IF;
		IF status_cancel  = 1 THEN SET query = CONCAT(query,",","tmp2.status_cancel "); END IF;

	SET query = CONCAT(query," ","FROM (SELECT vt.id,vt.name,cal.date,vt.del_flag,vt.partner_id FROM ((SELECT vtm.id,vtm.name,vtm.del_flag,vtm.partner_id FROM vehicle_team vtm) UNION (SELECT 0 as id ,'*Chưa gán đội tài xế' as name,0 as del_flag,0 as partner_id)) vt");

	SET query = CONCAT(query," ","CROSS JOIN (
										SELECT selected_date as date FROM (
													SELECT adddate('1970-01-01',t4.i*10000 + t3.i*1000 + t2.i*100 + t1.i*10 + t0.i) selected_date
													FROM
														 (SELECT 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t0,
														 (SELECT 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t1,
														 (SELECT 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t2,
														 (SELECT 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t3,
														 (SELECT 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t4
										) d
										WHERE selected_date BETWEEN '",from_date,"' AND '",to_date,"'
										) cal
										WHERE 1=1 ");

	IF vehicle_team_ids IS NOT NULL AND vehicle_team_ids != "" THEN
		SET query = CONCAT(query," ","AND vt.id IN (",vehicle_team_ids,")");
	END IF;

	IF partner_ids IS NOT NULL AND partner_ids != "" THEN
		SET query = CONCAT(query," ","AND vt.partner_id IN (",partner_ids,")");
	END IF;

	SET query = CONCAT(query," ",") tmp1 LEFT JOIN (SELECT tmp.date,tmp.vehicle_team_id");

		IF status_all = 1 THEN SET query = CONCAT(query,",","(SUM(status_complete) + SUM(status_incomplete) + SUM(status_cancel))  as status_all"); END IF;
		IF status_complete = 1 THEN
			SET query = CONCAT(query,",","SUM(status_complete) as status_complete");
			SET query = CONCAT(query,",","SUM(status_on_time) as status_on_time");
			SET query = CONCAT(query,",","SUM(status_late) as status_late");
		END IF;
		IF status_incomplete = 1 THEN SET query = CONCAT(query,",","SUM(status_incomplete) as status_incomplete"); END IF;
		IF status_cancel = 1 THEN SET query = CONCAT(query,",","SUM(status_cancel) as status_cancel"); END IF;

	SET query = CONCAT(query," ","FROM (SELECT DISTINCT o.id, (CASE WHEN (o.ETA_date_reality IS NOT NULL AND o.status = 5) THEN o.ETA_date_reality ELSE o.ETA_date END) date,
																							(CASE WHEN (dvt.vehicle_team_id IS NULL OR dvt.vehicle_team_id = 0 ) THEN 0 ELSE dvt.vehicle_team_id END) as vehicle_team_id");
		IF status_complete = 1 THEN
			SET query = CONCAT(query,",","(CASE WHEN (o.ETA_date_reality IS NOT NULL AND o.status = 5) THEN 1 ELSE 0 END) as status_complete");
			SET query = CONCAT(query,",","(CASE WHEN (o.ETA_date_reality IS NOT NULL AND o.status = 5
																	AND DATE_FORMAT(CONCAT(o.ETA_date,' ',o.ETA_time), '%Y-%m-%d %H:%i') >= DATE_FORMAT(CONCAT(o.ETA_date_reality,' ',o.ETA_time_reality)
																	, '%Y-%m-%d %H:%i')) THEN 1 ELSE 0 END) as status_on_time");
			SET query = CONCAT(query,",","(CASE WHEN (o.ETA_date_reality IS NOT NULL AND o.status = 5
																								AND DATE_FORMAT(CONCAT(o.ETA_date,' ',o.ETA_time), '%Y-%m-%d %H:%i') < DATE_FORMAT(CONCAT(o.ETA_date_reality,' ',o.ETA_time_reality)
																								, '%Y-%m-%d %H:%i')) THEN 1 ELSE 0 END) as status_late");
		ELSE
			SET query = CONCAT(query,",","0 as status_complete");
		END IF;
		IF status_incomplete = 1 THEN SET query = CONCAT(query,",","(CASE WHEN o.status IN (2,3,4,7) THEN 1 ELSE 0 END) as status_incomplete");
		ELSE
			SET query = CONCAT(query,",","0 as status_incomplete");
		END IF;

		IF status_cancel = 1 THEN SET query = CONCAT(query,",","(CASE WHEN o.status = 6 THEN 1 ELSE 0 END) as status_cancel");
		ELSE
			SET query = CONCAT(query,",","0 as status_cancel");
		END IF;

	SET query = CONCAT(query," ","FROM orders o LEFT JOIN driver_vehicle_team dvt ON dvt.driver_id = o.primary_driver_id");

	SET query = CONCAT(query," ","WHERE o.del_flag = 0 AND o.status != 1 AND CASE WHEN o.ETA_date_reality IS NOT NULL AND o.status = 5
																																								THEN o.ETA_date_reality BETWEEN '",from_date,"' AND '",to_date,"'
																																								ELSE o.ETA_date BETWEEN '",from_date,"' AND '",to_date,"' END");

	IF vehicle_team_ids IS NOT NULL AND vehicle_team_ids != "" THEN
		SET query = CONCAT(query," "," AND dvt.vehicle_team_id IN (",vehicle_team_ids,")");
	END IF;
	IF driver_ids IS NOT NULL AND driver_ids != "" THEN
		SET query = CONCAT(query," ","AND o.primary_driver_id IN (",driver_ids,")");
	END IF;
	IF vehicle_ids IS NOT NULL AND vehicle_ids != "" THEN
		SET query = CONCAT(query," ","AND o.vehicle_id IN (",vehicle_ids,")");
	END IF;
	IF customer_ids IS NOT NULL AND customer_ids != "" THEN
		SET query = CONCAT(query," ","AND o.customer_id IN (",customer_ids,")");
	END IF;
	IF partner_ids IS NOT NULL AND partner_ids != "" THEN
		SET query = CONCAT(query," ","AND o.partner_id IN (",partner_ids,")");
	END IF;

	SET query = CONCAT(query," ",") tmp GROUP BY tmp.vehicle_team_id,tmp.date ) tmp2 ON tmp1.id = tmp2.vehicle_team_id AND tmp1.date = tmp2.date");

	SET query = CONCAT(query," ","WHERE tmp1.del_flag = 0 OR (tmp2.status_all IS NOT NULL AND tmp2.status_all != 0 )");

	 IF summary = 0 THEN
			SET @SQLText = CONCAT(query,' ','ORDER BY tmp1.name,tmp1.date');
			-- SELECT @SQLText;
			PREPARE stmt FROM @SQLText;
	 ELSE
			SET summaryQuery = "SELECT tmp3.date as date,";

			IF status_all = 1 THEN SET summaryQuery = CONCAT(summaryQuery,"SUM(tmp3.status_all) as total");
			ELSEIF status_incomplete = 1 THEN SET summaryQuery = CONCAT(summaryQuery,"SUM(tmp3.status_incomplete) as total");
			ELSEIF status_complete = 1 THEN SET summaryQuery = CONCAT(summaryQuery,"SUM(tmp3.status_complete) as total");
			ELSEIF status_cancel = 1 THEN SET summaryQuery = CONCAT(summaryQuery,"SUM(tmp3.status_cancel) as total");
			END IF;

			SET @SQLText = CONCAT(summaryQuery," FROM(", query,") tmp3 GROUP BY tmp3.date ORDER BY STR_TO_DATE(tmp3.date,'%d-%m-%Y')");
			PREPARE stmt FROM @SQLText;
	 END IF;

   EXECUTE stmt;
   DEALLOCATE PREPARE stmt;
END
;


-- ----------------------------
-- Procedure structure for proc_report_vehicle_team_by_turn_monthly
-- ----------------------------
DROP PROCEDURE IF EXISTS `proc_report_vehicle_team_by_turn_monthly`;

CREATE PROCEDURE `proc_report_vehicle_team_by_turn_monthly`(IN summary BOOLEAN,IN vehicle_team_ids TEXT,
	IN vehicle_ids TEXT,IN driver_ids TEXT,
	IN customer_ids TEXT,IN status_all BOOLEAN,IN status_incomplete BOOLEAN,
	IN status_complete BOOLEAN,IN status_cancel BOOLEAN,IN from_date TEXT,IN to_date TEXT, IN partner_ids TEXT)
BEGIN

	DECLARE query TEXT;
	DECLARE summaryQuery TEXT;

	SET query = "SELECT tmp1.id as entity_id, tmp1.name as entity_name,DATE_FORMAT(STR_TO_DATE(tmp1.date,'%Y-%m'),'%m-%Y') as date";
		IF status_all = 1 THEN SET query = CONCAT(query,",","tmp2.status_all"); END IF;
		IF status_complete = 1 THEN
			SET query = CONCAT(query,",","tmp2.status_complete");
			SET query = CONCAT(query,",","tmp2.status_on_time");
			SET query = CONCAT(query,",","tmp2.status_late");
		END IF;
		IF status_incomplete = 1 THEN SET query = CONCAT(query,",","tmp2.status_incomplete"); END IF;
		IF status_cancel  = 1 THEN SET query = CONCAT(query,",","tmp2.status_cancel"); END IF;

	SET query = CONCAT(query," ","FROM (SELECT vt.id,vt.name,cal.date,vt.del_flag,vt.partner_id FROM ((SELECT vtm.id,vtm.name,vtm.del_flag,vtm.partner_id FROM vehicle_team vtm) UNION (SELECT 0 as id ,'*Chưa gán đội tài xế' as name,0 as del_flag,0 as partner_id)) vt");

	SET query = CONCAT(query," ","CROSS JOIN (select DATE_FORMAT(aDate,'%Y-%m') as date from (select @maxDate - interval (a.a + (10 * b.a) + (100 * c.a)) month as aDate from
																						(select 0 as a union all select 1 union all select 2 union all select 3 union all select 4 union all select 5 union all select 6 union all select 7
																						union all select 8 union all select 9) a,
																						(select 0 as a union all select 1 union all select 2 union all select 3 union all select 4 union all select 5 union all select 6 union all select 7
																						union all select 8 union all select 9) b,
																						(select 0 as a union all select 1 union all select 2 union all select 3 union all select 4 union all select 5 union all select 6 union all select 7
																						union all select 8 union all select 9) c,
																						(select @minDate := '",from_date,"', @maxDate := '",to_date,"') d) e
																						where DATE_FORMAT(aDate,'%Y-%m') BETWEEN DATE_FORMAT(@minDate, '%Y-%m') AND DATE_FORMAT(@maxDate, '%Y-%m')) cal
																						WHERE 1=1");

	IF vehicle_team_ids IS NOT NULL AND vehicle_team_ids != "" THEN
		SET query = CONCAT(query," ","AND vt.id IN (",vehicle_team_ids,")");
	END IF;

	IF partner_ids IS NOT NULL AND partner_ids != "" THEN
		SET query = CONCAT(query," ","AND vt.partner_id IN (",partner_ids,")");
	END IF;

	SET query = CONCAT(query," ",") tmp1 LEFT JOIN (SELECT tmp.date,tmp.vehicle_team_id");

		IF status_all = 1 THEN SET query = CONCAT(query,",","(SUM(status_complete) + SUM(status_incomplete) + SUM(status_cancel))  as status_all"); END IF;
		IF status_complete = 1 THEN
			SET query = CONCAT(query,",","SUM(status_complete) as status_complete");
			SET query = CONCAT(query,",","SUM(status_on_time) as status_on_time");
			SET query = CONCAT(query,",","SUM(status_late) as status_late");
		END IF;
		IF status_incomplete = 1 THEN SET query = CONCAT(query,",","SUM(status_incomplete) as status_incomplete"); END IF;
		IF status_cancel = 1 THEN SET query = CONCAT(query,",","SUM(status_cancel) as status_cancel"); END IF;

	SET query = CONCAT(query," ","FROM (SELECT DISTINCT o.id, (CASE WHEN (o.ETA_date_reality IS NOT NULL AND o.status = 5) THEN DATE_FORMAT(o.ETA_date_reality,'%Y-%m') ELSE DATE_FORMAT(o.ETA_date,'%Y-%m') END) date,
																						(CASE WHEN (dvt.vehicle_team_id IS NULL OR dvt.vehicle_team_id = 0 ) THEN 0 ELSE dvt.vehicle_team_id END) as vehicle_team_id");
		IF status_complete = 1 THEN
			SET query = CONCAT(query,",","(CASE WHEN (o.ETA_date_reality IS NOT NULL AND o.status = 5) THEN 1 ELSE 0 END) as status_complete");
			SET query = CONCAT(query,",","(CASE WHEN (o.ETA_date_reality IS NOT NULL AND o.status = 5
																	AND DATE_FORMAT(CONCAT(o.ETA_date,' ',o.ETA_time), '%Y-%m-%d %H:%i') >= DATE_FORMAT(CONCAT(o.ETA_date_reality,' ',o.ETA_time_reality)
																	, '%Y-%m-%d %H:%i')) THEN 1 ELSE 0 END) as status_on_time");
			SET query = CONCAT(query,",","(CASE WHEN (o.ETA_date_reality IS NOT NULL AND o.status = 5
																		AND DATE_FORMAT(CONCAT(o.ETA_date,' ',o.ETA_time), '%Y-%m-%d %H:%i') < DATE_FORMAT(CONCAT(o.ETA_date_reality,' ',o.ETA_time_reality)
																		, '%Y-%m-%d %H:%i')) THEN 1 ELSE 0 END) as status_late");
		ELSE
			SET query = CONCAT(query,",","0 as status_complete");
		END IF;
		IF status_incomplete = 1 THEN SET query = CONCAT(query,",","(CASE WHEN o.status IN (2,3,4,7) THEN 1 ELSE 0 END) as status_incomplete");
		ELSE
			SET query = CONCAT(query,",","0 as status_incomplete");
		END IF;

		IF status_cancel = 1 THEN SET query = CONCAT(query,",","(CASE WHEN o.status = 6 THEN 1 ELSE 0 END) as status_cancel");
		ELSE
			SET query = CONCAT(query,",","0 as status_cancel");
		END IF;

	SET query = CONCAT(query," ","FROM orders o LEFT JOIN driver_vehicle_team dvt ON dvt.driver_id = o.primary_driver_id");

	SET query = CONCAT(query," ","WHERE o.del_flag = 0 AND o.status != 1 AND CASE WHEN o.ETA_date_reality IS NOT NULL AND o.status = 5
																																								THEN o.ETA_date_reality BETWEEN '",from_date,"' AND '",to_date,"'
																																								ELSE o.ETA_date BETWEEN '",from_date,"' AND '",to_date,"' END");

	IF vehicle_team_ids IS NOT NULL AND vehicle_team_ids != "" THEN
		SET query = CONCAT(query," "," AND dvt.vehicle_team_id IN (",vehicle_team_ids,")");
	END IF;
	IF driver_ids IS NOT NULL AND driver_ids != "" THEN
		SET query = CONCAT(query," ","AND o.primary_driver_id IN (",driver_ids,")");
	END IF;
	IF vehicle_ids IS NOT NULL AND vehicle_ids != "" THEN
		SET query = CONCAT(query," ","AND o.vehicle_id IN (",vehicle_ids,")");
	END IF;
	IF customer_ids IS NOT NULL AND customer_ids != "" THEN
		SET query = CONCAT(query," ","AND o.customer_id IN (",customer_ids,")");
	END IF;
	IF partner_ids IS NOT NULL AND partner_ids != "" THEN
		SET query = CONCAT(query," ","AND o.partner_id IN (",partner_ids,")");
	END IF;

	SET query = CONCAT(query," ",") tmp GROUP BY tmp.vehicle_team_id,tmp.date ) tmp2 ON tmp1.id = tmp2.vehicle_team_id AND tmp1.date = tmp2.date");

	SET query = CONCAT(query," ","WHERE tmp1.del_flag = 0 OR (tmp2.status_all IS NOT NULL AND tmp2.status_all != 0 )");

	 IF summary = 0 THEN
			SET @SQLText = CONCAT(query,' ','ORDER BY tmp1.name,tmp1.date');
			-- SELECT @SQLText;
			PREPARE stmt FROM @SQLText;
	 ELSE
			SET summaryQuery = "SELECT tmp3.date as date,";

			IF status_all = 1 THEN SET summaryQuery = CONCAT(summaryQuery,"SUM(tmp3.status_all) as total");
			ELSEIF status_incomplete = 1 THEN SET summaryQuery = CONCAT(summaryQuery,"SUM(tmp3.status_incomplete) as total");
			ELSEIF status_complete = 1 THEN SET summaryQuery = CONCAT(summaryQuery,"SUM(tmp3.status_complete) as total");
			ELSEIF status_cancel = 1 THEN SET summaryQuery = CONCAT(summaryQuery,"SUM(tmp3.status_cancel) as total");
			END IF;

			SET @SQLText = CONCAT(summaryQuery," FROM(", query,") tmp3 GROUP BY tmp3.date ORDER BY STR_TO_DATE(tmp3.date,'%m-%Y')");
			PREPARE stmt FROM @SQLText;
	 END IF;

   EXECUTE stmt;
   DEALLOCATE PREPARE stmt;
END
;

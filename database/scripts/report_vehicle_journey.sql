/*
 Navicat Premium Data Transfer

 Source Server         : ceta_dev
 Source Server Type    : MySQL
 Source Server Version : 100145
 Source Host           : 45.118.145.59:3388
 Source Schema         : ceta_dev

 Target Server Type    : MySQL
 Target Server Version : 100145
 File Encoding         : 65001

 Date: 17/07/2020 08:41:30
*/

-- ----------------------------
-- Procedure structure for proc_report_vehicle_by_distance
-- ----------------------------
DROP PROCEDURE IF EXISTS `proc_report_vehicle_by_distance`;

CREATE PROCEDURE `proc_report_vehicle_by_distance`(IN summary BOOLEAN,
	IN vehicle_ids TEXT,IN vehicle_team_ids TEXT,IN driver_ids TEXT,IN from_date TEXT,IN to_date TEXT,IN partner_ids TEXT)
BEGIN

	DECLARE query TEXT;
	DECLARE summaryQuery TEXT;

	SET query = "SELECT tmp1.vehicle_id, tmp1.reg_no , tmp1.driver_name, DATE_FORMAT(tmp1.date,'%d-%m-%Y') as date, (tmp2.distance/1000) as distance, (tmp2.distance_with_goods/1000) as distance_with_goods, (tmp2.distance_without_goods/1000) as distance_without_goods";

	SET query = CONCAT(query," ","FROM (SELECT vh.id as vehicle_id ,vh.reg_no,vh.driver_name,cal.date,vh.partner_id FROM");

	SET query = CONCAT(query," ","(SELECT v.id,v.reg_no,v.partner_id,GROUP_CONCAT(DISTINCT d.full_name SEPARATOR ' , ') driver_name FROM vehicle v");
	SET query = CONCAT(query," ","LEFT JOIN driver_vehicle dv ON dv.vehicle_id = v.id");
	SET query = CONCAT(query," ","LEFT JOIN drivers d ON d.id = dv.driver_id");
	SET query = CONCAT(query," ","WHERE v.del_flag = 0 GROUP BY v.id) vh");

	IF (vehicle_team_ids IS NOT NULL AND vehicle_team_ids != "") OR (driver_ids IS NOT NULL AND driver_ids != "") THEN
		SET query = CONCAT(query," ","INNER JOIN driver_vehicle dv ON dv.vehicle_id = vh.id");

		IF driver_ids IS NOT NULL AND driver_ids != "" THEN
			SET query = CONCAT(query," ","AND dv.driver_id IN (",driver_ids,")");
		END IF;

		IF vehicle_team_ids IS NOT NULL AND vehicle_team_ids != "" THEN
			SET query = CONCAT(query," ","INNER JOIN driver_vehicle_team dvt ON dvt.driver_id = dv.driver_id AND dvt.vehicle_team_id IN (",		vehicle_team_ids,")");
		END IF;

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
		SET query = CONCAT(query," ","AND vh.id IN (",vehicle_ids,")");
	END IF;

	IF partner_ids IS NOT NULL AND partner_ids != "" THEN
		SET query = CONCAT(query," ","AND vh.partner_id IN (",partner_ids,")");
	END IF;

	SET query = CONCAT(query," ",") tmp1 LEFT JOIN (SELECT v.id as vehicle_id,vdr.date");
	SET query = CONCAT(query," ",", CASE WHEN vdr.distance IS NOT NULL AND vdr.distance > 0 THEN vdr.distance ELSE 0 END as distance");
	SET query = CONCAT(query," ",", SUM(ddr.distance_with_goods) as distance_with_goods ,(vdr.distance - SUM(ddr.distance_with_goods)) as distance_without_goods");

	SET query = CONCAT(query," ","FROM vehicle v
																LEFT JOIN vehicle_daily_report vdr ON vdr.vehicle_plate = v.vehicle_plate AND v.del_flag = 0
																LEFT JOIN distance_daily_report ddr ON ddr.vehicle_plate = vdr.vehicle_plate AND vdr.date = ddr.date AND ddr.del_flag = 0");
	SET query = CONCAT(query," ","WHERE v.del_flag = 0 AND vdr.date BETWEEN '",from_date,"' AND '",to_date,"'");

	SET query = CONCAT(query," ","GROUP BY v.id,vdr.date ) tmp2 ON tmp1.vehicle_id = tmp2.vehicle_id AND tmp1.date = tmp2.date");

	 IF summary = 0 THEN
			SET @SQLText = CONCAT(query,' ','ORDER BY tmp1.reg_no,tmp1.date');
		  -- SELECT @SQLText;
		  PREPARE stmt FROM @SQLText;
	 ELSE
			SET summaryQuery = "SELECT tmp3.date as date, SUM(tmp3.distance) as distance,SUM(tmp3.distance_with_goods) as distance_with_goods ,SUM(tmp3.distance_without_goods) as distance_without_goods";

			SET @SQLText = CONCAT(summaryQuery," FROM(", query,") tmp3 GROUP BY tmp3.date ORDER BY STR_TO_DATE(tmp3.date,'%d-%m-%Y')");
			PREPARE stmt FROM @SQLText;
	 END IF;

   EXECUTE stmt;
   DEALLOCATE PREPARE stmt;
END
;

-- ----------------------------
-- Procedure structure for proc_uptime_vehicle_daily
-- ----------------------------
DROP PROCEDURE IF EXISTS `proc_uptime_vehicle_daily`;

CREATE PROCEDURE `proc_uptime_vehicle_daily`(IN date TEXT, IN gps_company_id TEXT)
BEGIN

	DECLARE query TEXT;

	SET query = CONCAT("SELECT DISTINCT rt.id route_id, rt.vehicle_id, rt.reg_no, rt.vehicle_plate, rt.gps_id, vdr.distance,
										CASE WHEN DATE_FORMAT(rt.ETD_reality,'%Y-%m-%d') = DATE_FORMAT('",date,"','%Y-%m-%d') THEN rt.ETD_reality
												ELSE DATE_FORMAT('",date," 00:00','%Y-%m-%d %H:%i') END as ETD_reality,
										CASE WHEN DATE_FORMAT(rt.ETA_reality,'%Y-%m-%d') = DATE_FORMAT('",date,"','%Y-%m-%d') AND rt.route_status = 1 THEN rt.ETA_reality
												ELSE DATE_FORMAT('",date," 23:59','%Y-%m-%d %H:%i') END as ETA_reality
								FROM (");
	SET query = CONCAT(query," ","SELECT r.id, r.vehicle_id, v.reg_no, v.vehicle_plate, v.gps_id, r.route_status
																	, MIN(DATE_FORMAT(CONCAT(o.ETD_date_reality,' ',o.ETD_time_reality),'%Y-%m-%d %H:%i')) as ETD_reality,
																		DATE_FORMAT(CONCAT(r.ETA_date_reality,' ',r.ETA_time_reality),'%Y-%m-%d %H:%i') as ETA_reality
																FROM routes r
																INNER JOIN vehicle v ON v.id = r.vehicle_id AND v.gps_company_id IN (",gps_company_id,")
																			AND (r.route_status = 0 OR (r.route_status = 1 AND r.ETA_date_reality >= DATE_FORMAT('",date,"','%Y-%m-%d')))
																INNER JOIN orders o ON o.route_id = r.id
																WHERE r.del_flag = 0 AND o.del_flag = 0
																GROUP BY r.id");

	SET query = CONCAT(query," ",") rt LEFT JOIN vehicle_daily_report vdr ON vdr.vehicle_plate = rt.vehicle_plate AND 		DATE_FORMAT(vdr.date,'%Y-%m-%d') = DATE_FORMAT('",date,"','%Y-%m-%d')");

	SET query = CONCAT(query," ","WHERE rt.ETD_reality IS NOT NULL AND DATE_FORMAT(rt.ETD_reality,'%Y-%m-%d') <= DATE_FORMAT('",date,"','%Y-%m-%d') ORDER BY rt.vehicle_id");


	SET @SQLText = query;
	-- SELECT @SQLText;
	PREPARE stmt FROM @SQLText;

  EXECUTE stmt;
  DEALLOCATE PREPARE stmt;
END
;
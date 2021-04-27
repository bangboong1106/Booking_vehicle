<?php
return [
    /**
     * Lưu ý, khi đổi tên APP_NAME, thì đổi tên các file excel trong public/file:
     * {{APP_NAME}}_****
     */

    'APP_NAME' => 'McLean',
    'APP_EMAIL' => 'mclean@gmail.com.vn',
    'APP_EMAIL_SUPPORT' => 'mclean@gmail.com.vn',
    'APP_HOTLINE' => '024 7300 9559',
    'APP_ADDRESS' => 'Số 96 Hoàng Ngân, Cầu Giấy, Hà Nội',
    'APP_WEB' => 'http://c20.com.vn',
    'APP_COMPANY' => 'C20',

    'SUPPER_ADMIN_TYPE' => 0,
    'ADMIN_TYPE' => 1,
    'MODERATOR_TYPE' => 2,
    'EVENT_CONTROLLER_TYPE' => 'controller',
    'EVENT_MODEL_TYPE' => 'eloquent',

    // order precedence
    'ORDER_PRECEDENCE_NORMAL' => 4,
    'ORDER_PRECEDENCE_SPECIAL' => 3,
    'ORDER_PRECEDENCE_LOW' => 5,

    // driver type
    'PRIMARY_DRIVER_TYPE' => 1,
    'SECONDARY_DRIVER_TYPE' => 2,

    // customer type
    'CORPORATE_CUSTOMERS' => 1,
    'INDIVIDUAL_CUSTOMERS' => 2,

    //order status
    'KHOI_TAO' => 1,
    'SAN_SANG' => 2,
    'CHO_NHAN_HANG' => 3,
    'DANG_VAN_CHUYEN' => 4,
    'HOAN_THANH' => 5,
    'HUY' => 6,
    'TAI_XE_XAC_NHAN' => 7,

    //order status partner
    'PARTNER_CHO_GIAO_DOI_TAC_VAN_TAI' => 0,
    'PARTNER_CHO_XAC_NHAN' => 1,
    'PARTNER_XAC_NHAN' => 2,
    'PARTNER_HUY' => 3,
    'PARTNER_YEU_CAU_SUA' => 4,

    // sex type
    'MALE_SEX_TYPE' => 'male',
    'FEMALE_SEX_TYPE' => 'female',

    //system code type
    'sc_order' => 1,
    'sc_customer' => 2,
    'sc_driver' => 3,
    'sc_good_type' => 4,
    'sc_goods' => 4,
    'sc_good_unit' => 5,
    'sc_location' => 6,
    'sc_vehicle_team' => 7,
    'sc_vehicle_group' => 8,
    'sc_route' => 9,
    'sc_quota' => 10,
    'sc_order_customer' => 11,
    'sc_customer_group' => 12,
    'sc_price_quote' => 13,
    'sc_payroll' => 14,
    'sc_location_group' => 15,
    'sc_repair_ticket' => 16,
    'sc_goods_group' => 17,
    'sc_partner' => 18,
    'sc_customer_default' => 19,

    //table id
    'cf_order' => 1,
    'cf_vehicle' => 2,
    'cf_driver' => 3,
    'cf_customer' => 4,
    'cf_route' => 5,
    'cf_document' => 6,
    'cf_order_customer' => 7,
    'cf_price_quote' => 8,
    'cf_payroll' => 9,
    'cf_order_price' => 10,
    'cf_quota' => 11,
    'cf_location' => 12,
    'cf_location_group' => 13,
    'cf_customer_group' => 14,
    'cf_vehicle_team' => 15,
    'cf_admin' => 16,
    'cf_contact' => 17,
    'cf_contract' => 18,
    'cf_role' => 19,
    'cf_template' => 20,
    'cf_template_payment' => 21,
    'cf_good_type' => 22,
    'cf_good_unit' => 23,
    'cf_currency' => 24,
    'cf_contract_type' => 25,
    'cf_location_type' => 26,
    'cf_system_code_config' => 27,
    'cf_vehicle_config_file' => 28,
    'cf_vehicle_config_specification' => 29,
    'cf_report_schedule' => 30,
    'cf_merge_order' => 31,
    'cf_accessory' => 32,
    'cf_repair_ticket' => 33,
    'cf_partner' => 34,

    //route status
    'status_incomplete' => 0, // Dữ liệu cũ đang là null
    'status_complete' => 1,
    'status_cancel' => 2,


    // insured good
    'yes' => 1,
    'no' => 0,

    'cell_locations' => [
        'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
        'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ',
        'BA', 'BB', 'BC', 'BD', 'BE', 'BF', 'BG', 'BH', 'BI', 'BJ', 'BK', 'BL', 'BM', 'BN', 'BO', 'BP', 'BQ', 'BR', 'BS', 'BT', 'BU', 'BV', 'BW', 'BX', 'BY', 'BZ',
        'CA', 'CB', 'CC', 'CD', 'CE', 'CF', 'CG', 'CH', 'CI', 'CJ', 'CK', 'CL', 'CM', 'CN', 'CO', 'CP', 'CQ', 'CR', 'CS', 'CT', 'CU', 'CV', 'CW', 'CX', 'CY', 'CZ',
        'DA', 'DB', 'DC', 'DD', 'DE', 'DF', 'DG', 'DH', 'DI', 'DJ', 'DK', 'DL', 'DM', 'DN', 'DO', 'DP', 'DQ', 'DR', 'DS', 'DT', 'DU', 'DV', 'DW', 'DX', 'DY', 'DZ',
        'EA', 'EB', 'EC', 'ED', 'EE', 'EF', 'EG', 'EH', 'EI', 'EJ', 'EK', 'EL', 'EM', 'EN', 'EO', 'EP', 'EQ', 'ER', 'ES', 'ET', 'EU', 'EV', 'EW', 'EX', 'EY', 'EZ',
        'FA', 'FB', 'FC', 'FD', 'FE', 'FF', 'FG', 'FH', 'FI', 'FJ', 'FK', 'FL', 'FM', 'FN', 'FO', 'FP', 'FQ', 'FR', 'FS', 'FT', 'FU', 'FV', 'FW', 'FX', 'FY', 'FZ',
        'GA', 'GB', 'GC', 'GD', 'GE', 'GF', 'GG', 'GH', 'GI', 'GJ', 'GK', 'GL', 'GM', 'GN', 'GO', 'GP', 'GQ', 'GR', 'GS', 'GT', 'GU', 'GV', 'GW', 'GX', 'GY', 'GZ',
        'HA', 'HB', 'HC', 'HD', 'HE', 'HF', 'HG', 'HH', 'HI', 'HJ', 'HK', 'HL', 'HM', 'HN', 'HO', 'HP', 'HQ', 'HR', 'HS', 'HT', 'HU', 'HV', 'HW', 'HX', 'HY', 'HZ',
        'IA', 'IB', 'IC', 'ID', 'IE', 'IF', 'IG', 'II', 'II', 'IJ', 'IK', 'IL', 'IM', 'IN', 'IO', 'IP', 'IQ', 'IR', 'IS', 'IT', 'IU', 'IV', 'IW', 'IX', 'IY', 'IZ',
    ],

    // location type
    'DESTINATION' => 1,
    'ARRIVAL' => 2,

    'ROUTE_FILE_TYPE_GENERAL' => 0,
    'ROUTE_FILE_TYPE_COST' => 1,

    'GC_BINH_ANH' => 1,
    'GC_VIET_MAPS' => 2,
    'GC_ADA' => 3,
    'GC_BINH_ANH_2' => 4,
    'GC_EUPFIN' => 5,
    'GC_VCOMSAT' => 6,
    'GC_EPOSI' => 7,
    'GC_ADSUN' => 8,
    'GC_VINHHIEN' => 9,

    // receipt payment type
    'REVENUE' => 1,
    'COST' => 2,

    //document order status
    'CHUA_THU_DU' => 1,
    'DA_THU_DU' => 2,
    'QUA_HAN' => 3,
    'DEN_HAN_VAO_HOM_SAU' => 4,
    'DEN_HAN_VAO_HOM_NAY' => 5,

    'PHAN_TRAM_HOA_HONG' => 1,
    'TONG_TIEN_HOA_HONG' => 2,

    // Location type
    'LOCATION_DIFFICULT' => 1,
    'LOCATION_NORMAL' => 2,
    'LOCATION_EASY' => 3,


    //route is approval
    "CHUA_PHE_DUYET" => 0,
    "DA_PHE_DUYET" => 1,

    'MULTIPLE_SHEET' => 1,
    'SINGLE_SHEET' => 2,

    'ORDER' => 1,
    'DOCUMENT' => 2,
    'ROUTE' => 3,
    'QUOTA' => 4,
    'CUSTOMER' => 5,
    'DRIVER' => 6,
    'VEHICLE' => 7,
    'ORDER_CUSTOMER' => 8,

    //order customer source
    'FROM_ADMIN' => 0,
    'FROM_CLIENT' => 1,

    // 3P Config
    '3P_1MG' => 1,

    //Action 3P
    'UPDATE_ORDER' => 'update_order',
    '1MG_NAME' => '1MG',

    'PRICE_QUOTE_VEHICLE_GROUP' => 1,
    'PRICE_QUOTE_WEIGHT' => 2,
    'PRICE_QUOTE_VOLUME' => 3,
    'PRICE_QUOTE_QUANTITY' => 4,

    //order review point
    'review_point_1' => 1,
    'review_point_2' => 2,
    'review_point_3' => 3,
    'review_point_4' => 4,
    'review_point_5' => 5,
    'FILE_REVIEW_ORDER_TYPE' => 98,

    //Hinh thuc thanh toan
    'CHUYEN_KHOAN' => 1,
    'TIEN_MAT' => 2,


    //order status
    'ORDER_CUSTOMER_STATUS' => [
        'CHO_CHU_HANG_XAC_NHAN' => 1,
        'CHU_HANG_XAC_NHAN' => 2,
        'CHU_HANG_HUY' => 3,
        'CHU_HANG_YEU_CAU_SUA' => 4,
        'DA_XUAT_HANG' => 5,
        'DANG_VAN_CHUYEN' => 6,
        'HOAN_THANH' => 7,
        'C20_HUY' => 8,
    ],
    'ORDER_CUSTOMER_STATUS_GOODS' => [
        'CON_HANG' => 1,
        'HET_HANG' => 2,
    ],

    //Nguon them don hang
    'SOURCE_CREATE_C20_ORDER_FORM' => 1,
    'SOURCE_CREATE_C20_ORDER_EXCEL' => 2,
    'SOURCE_CREATE_C20_ORDER_ADVANCE' => 3,
    'SOURCE_CREATE_C20_ORDER_APP_MANAGE' => 4,
    'SOURCE_CREATE_C20_ORDER_SPLIT' => 5,
    'SOURCE_CREATE_C20_ORDER_MERGE' => 6,
    'SOURCE_CREATE_CHU_HANG_ORDER_FORM' => 7,
    'SOURCE_CREATE_CHU_HANG_ORDER_EXCEL' => 8,
    'SOURCE_CREATE_CHU_HANG_ORDER_ADVANCE' => 9,
    'SOURCE_CREATE_CHU_HANG_ORDER_APP_MANAGE' => 10,
    'SOURCE_CREATE_C20_ORDER_CUSTOMER_FORM' => 11,
    'SOURCE_CREATE_C20_ORDER_CUSTOMER_EXCEL' => 12,
    'SOURCE_CREATE_C20_ORDER_CUSTOMER_ADVANCE' => 13,
    'SOURCE_CREATE_C20_ORDER_CUSTOMER_APP_MANAGE' => 14,
    'SOURCE_CREATE_KHACH_HANG_ORDER_FORM' => 15,

    'PLAY_STORE_URL' => 'https://play.google.com/store/apps/details?id=',
    'APP_STORE_URL' => 'https://apps.apple.com/app/id',

    //Customer type
    'CHU_HANG' => 1,
    'NGUOI_DUNG' => 2,
    'KHACH_HANG' => 3,
];

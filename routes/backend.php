<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "backend" middleware group. Now create something great!
|
*/
Route::group(['middleware' => ['XSS']], function () {
    Route::get('notification/nearest-location', [
        'as' => 'notification.nearest-location',
        'uses' => 'NotificationController@notifyNearestLocation'
    ]);
    Route::get('report', [
        'as' => 'report.index',
        'uses' => 'ReportController@index'
    ]);
    Route::get('report-journey', [
        'as' => 'report-journey.index',
        'uses' => 'ReportJourneyController@index'
    ]);
    Route::get('report-customer', [
        'as' => 'report-customer.index',
        'uses' => 'ReportCustomerController@index'
    ]);
    Route::get('report-vehicle', [
        'as' => 'report-vehicle.index',
        'uses' => 'ReportVehicleController@index'
    ]);
    Route::get('report-vehicle-team', [
        'as' => 'report-vehicle-team.index',
        'uses' => 'ReportVehicleTeamController@index'
    ]);
    Route::get('/', [
        'as' => 'board.index',
        'uses' => 'BoardController@index'
    ]);
    Route::get('/dashboard', [
        'as' => 'dashboard.index',
        'uses' => 'BoardController@index'
    ]);
    Route::post('/board/report', [
        'as' => 'board.report',
        'uses' => 'BoardController@report'
    ]);
    Route::post('/board/general-info-order', [
        'as' => 'board.generalInfoOrder',
        'uses' => 'BoardController@generalInfoOrder'
    ]);

    Route::post('/board/general-info-customer', [
        'as' => 'board.generalInfoCustomer',
        'uses' => 'BoardController@generalInfoCustomer'
    ]);

    Route::post('/board/general-info-revenue', [
        'as' => 'board.generalInfoRevenue',
        'uses' => 'BoardController@generalInfoRevenue'
    ]);

    Route::post('/board/general-info-document', [
        'as' => 'board.generalInfoDocument',
        'uses' => 'BoardController@generalInfoDocument'
    ]);

    Route::get('/order-board', [
        'as' => 'order-board.index',
        'uses' => 'OrderBoardController@index'
    ]);

    Route::get('order-board/vehicle-search', [
        'as' => 'order-board.vehicle-search',
        'uses' => 'OrderBoardController@vehicleSearch'
    ]);


    Route::get('order-board/event', [
        'as' => 'order-board.event',
        'uses' => 'OrderBoardController@event'
    ]);

    Route::get('order-board/order-list', [
        'as' => 'order-board.order-list',
        'uses' => 'OrderBoardController@orderList'
    ]);

    Route::get('order-board/vehicle', [
        'as' => 'order-board.vehicle',
        'uses' => 'OrderBoardController@vehicle'
    ]);

    Route::post('order-board/trip-detail', [
        'as' => 'order-board.trip-detail',
        'uses' => 'OrderBoardController@tripdetail'
    ]);
    Route::post('order-board/remove-trip-from-vehicle', [
        'as' => 'order-board.remove-trip-from-vehicle',
        'uses' => 'OrderBoardController@removeTripFromVehicle'
    ]);
    Route::post('order-board/remove-trip', [
        'as' => 'order-board.remove-trip',
        'uses' => 'OrderBoardController@removeTrip'
    ]);
    Route::post('order-board/change-vehicle-for-trip', [
        'as' => 'order-board.change-vehicle-for-trip',
        'uses' => 'OrderBoardController@changeVehicleForTrip'
    ]);
    Route::post('order-board/remove-trip', [
        'as' => 'order-board.remove-trip',
        'uses' => 'OrderBoardController@removeTrip'
    ]);
    Route::post('order-board/mass-add-trip', [
        'as' => 'order-board.mass-add-trip',
        'uses' => 'OrderBoardController@massAddTrip'
    ]);
    Route::get('order-board/order', [
        'as' => 'order-board.order',
        'uses' => 'OrderBoardController@order'
    ]);

    Route::post('order-board/add-trip', [
        'as' => 'order-board.addTrip',
        'uses' => 'OrderBoardController@addTrip'
    ]);
    Route::post('order-board/resize-date-trip', [
        'as' => 'order-board.resizeDateTrip',
        'uses' => 'OrderBoardController@resizeDateTrip'
    ]);
    // Lệnh vận chuyển
    Route::get('/order-customer-board', [
        'as' => 'order-customer-board.index',
        'uses' => 'OrderCustomerBoardController@index'
    ]);

    Route::get('order-customer-board/event', [
        'as' => 'order-customer-board.event',
        'uses' => 'OrderCustomerBoardController@event'
    ]);

    Route::get('order-customer-board/vehicle', [
        'as' => 'order-customer-board.vehicle',
        'uses' => 'OrderCustomerBoardController@vehicle'
    ]);

    Route::get('order-customer-board/counttrips', [
        'as' => 'order-customer-board.counttrips',
        'uses' => 'OrderCustomerBoardController@counttrips'
    ]);
    Route::get('order-customer-board/{id}/detail', [
        'as' => 'order-customer-board.detail',
        'uses' => 'OrderCustomerBoardController@detail'
    ]);
    // Bảng điều chuyến
    Route::get('/route-board', [
        'as' => 'route-board.index',
        'uses' => 'RouteBoardController@index'
    ]);

    Route::get('route-board/vehicle-list', [
        'as' => 'route-board.vehicle-list',
        'uses' => 'RouteBoardController@vehicleList'
    ]);

    Route::get('route-board/vehicleSearch', [
        'as' => 'route-board.vehicleSearch',
        'uses' => 'RouteBoardController@vehicleSearch'
    ]);

    Route::get('route-board/route-list', [
        'as' => 'route-board.route-list',
        'uses' => 'RouteBoardController@routeList'
    ]);

    Route::get('route-board/vehicle-detail', [
        'as' => 'route-board.vehicle-detail',
        'uses' => 'RouteBoardController@vehicleDetail'
    ]);

    Route::get('route-board/order-list', [
        'as' => 'route-board.order-list',
        'uses' => 'RouteBoardController@orderList'
    ]);
    Route::post('route-board/choose-route', [
        'as' => 'route-board.choose-route',
        'uses' => 'RouteBoardController@chooseRoute'
    ]);
    Route::post('route-board/merge-route', [
        'as' => 'route-board.merge-route',
        'uses' => 'RouteBoardController@mergeRoute'
    ]);
    //--------------------------
    Route::match(['get', 'post'], 'admin/profile', [
        'as' => 'admin.profile',
        'uses' => 'AdminController@profile'
    ]);
    Route::resource('admin', 'AdminController');

    Route::match(['get', 'post'], 'partner-admin/profile', [
        'as' => 'partner-admin.profile',
        'uses' => 'PartnerAdminController@profile'
    ]);
    Route::resource('partner-admin', 'PartnerAdminController');

    Route::post('file/uploadFile', [
        'as' => 'file.uploadFile',
        'uses' => 'FileController@uploadFile'
    ]);
    Route::get('file/{id}/get-image', [
        'as' => 'file.getImage',
        'uses' => 'FileController@getImage'
    ]);
    Route::get('file/{id}/download-file', [
        'as' => 'file.downloadFile',
        'uses' => 'FileController@downloadFile'
    ]);
    Route::resource('file', 'FileController')->except(['ajaxSearch']);

    Route::get('order/exportCustomTemplate', [
        'as' => 'order.exportCustomTemplate',
        'uses' => 'OrderController@exportCustomTemplate'
    ]);
    Route::get('document/exportCustomTemplate', [
        'as' => 'document.exportCustomTemplate',
        'uses' => 'DocumentController@exportCustomTemplate'
    ]);
    Route::get('route/exportCustomTemplate', [
        'as' => 'route.exportCustomTemplate',
        'uses' => 'RouteController@exportCustomTemplate'
    ]);
    Route::get('quota/exportCustomTemplate', [
        'as' => 'quota.exportCustomTemplate',
        'uses' => 'QuotaController@exportCustomTemplate'
    ]);
    Route::get('customer/exportCustomTemplate', [
        'as' => 'customer.exportCustomTemplate',
        'uses' => 'CustomerController@exportCustomTemplate'
    ]);
    Route::get('driver/exportCustomTemplate', [
        'as' => 'driver.exportCustomTemplate',
        'uses' => 'DriverController@exportCustomTemplate'
    ]);
    Route::get('vehicle/exportCustomTemplate', [
        'as' => 'vehicle.exportCustomTemplate',
        'uses' => 'VehicleController@exportCustomTemplate'
    ]);
    Route::get('order-customer/exportCustomTemplate', [
        'as' => 'order-customer.exportCustomTemplate',
        'uses' => 'OrderCustomerController@exportCustomTemplate'
    ]);
    /* Driver */
    Route::get('driver/generateHeadTable', [
        'as' => 'driver.generateHeadTable',
        'uses' => 'DriverController@generateHeadTable'
    ]);
    Route::get('driver/get-vehicle-driver', [
        'as' => 'driver.getVehicleDriver',
        'uses' => 'DriverController@getVehicleDriver'
    ]);
    Route::get('driver/combo-driver', [
        'as' => 'driver.combo-driver',
        'uses' => 'DriverController@getDataForComboBox'
    ]);
    Route::get('driver/driver-history', [
        'as' => 'driver.driver-history',
        'uses' => 'DriverController@getDriverHistory'
    ]);
    Route::get('driver/order-table-action', [
        'as' => 'driver.order-table-action',
        'uses' => 'DriverController@orderTableAction'
    ]);
    Route::get('driver/vehicle-table-action', [
        'as' => 'driver.vehicle-table-action',
        'uses' => 'DriverController@vehicleTableAction'
    ]);
    Route::post('driver/import', [
        'as' => 'driver.import',
        'uses' => 'DriverController@import'
    ]);
    Route::get('driver/export', [
        'as' => 'driver.export',
        'uses' => 'DriverController@export'
    ]);
    Route::get('driver/exportTemplate', [
        'as' => 'driver.exportTemplate',
        'uses' => 'DriverController@exportTemplate'
    ]);
    Route::get('driver/exportConfirm', [
        'as' => 'driver.exportConfirm',
        'uses' => 'DriverController@exportConfirm'
    ]);
    Route::resource('driver', 'DriverController');
    /*End Driver */

    /* Partner Driver */
    Route::post('partner-driver/import', [
        'as' => 'partner-driver.import',
        'uses' => 'PartnerDriverController@import'
    ]);
    Route::get('partner-driver/export', [
        'as' => 'partner-driver.export',
        'uses' => 'PartnerDriverController@export'
    ]);
    Route::get('partner-driver/exportTemplate', [
        'as' => 'partner-driver.exportTemplate',
        'uses' => 'PartnerDriverController@exportTemplate'
    ]);
    Route::resource('partner-driver', 'PartnerDriverController');
    /*End Partner Driver */

    /* Vehicle */
    Route::get('vehicle/generateHeadTable', [
        'as' => 'vehicle.generateHeadTable',
        'uses' => 'VehicleController@generateHeadTable'
    ]);
    Route::get('vehicle/combo-vehicle', [
        'as' => 'vehicle.combo-vehicle',
        'uses' => 'VehicleController@getDataForComboBox'
    ]);
    Route::get('vehicle/vehicle-history', [
        'as' => 'vehicle.vehicle-history',
        'uses' => 'VehicleController@getVehicleHistory'
    ]);
    Route::get('vehicle/order-table-action', [
        'as' => 'vehicle.order-table-action',
        'uses' => 'VehicleController@orderTableAction'
    ]);
    Route::get('vehicle/driver-table-action', [
        'as' => 'vehicle.driver-table-action',
        'uses' => 'VehicleController@driverTableAction'
    ]);
    Route::get('vehicle/get-default-driver', [
        'as' => 'vehicle.getDefaultDriver',
        'uses' => 'VehicleController@getDefaultDriver'
    ]);
    Route::post('vehicle/import', [
        'as' => 'vehicle.import',
        'uses' => 'VehicleController@import'
    ]);
    Route::get('vehicle/export', [
        'as' => 'vehicle.export',
        'uses' => 'VehicleController@export'
    ]);
    Route::get('vehicle/exportTemplate', [
        'as' => 'vehicle.exportTemplate',
        'uses' => 'VehicleController@exportTemplate'
    ]);
    Route::get('vehicle/vehicle-gps-history', [
        'as' => 'vehicle.vehicle-gps-history',
        'uses' => 'VehicleController@getVehicleGpsHistory'
    ]);
    Route::resource('vehicle', 'VehicleController');
    /* End Vehicle */

    /* Partner Vehicle */
    Route::post('partner-vehicle/import', [
        'as' => 'partner-vehicle.import',
        'uses' => 'PartnerVehicleController@import'
    ]);
    Route::get('partner-vehicle/export', [
        'as' => 'partner-vehicle.export',
        'uses' => 'PartnerVehicleController@export'
    ]);
    Route::get('partner-vehicle/exportTemplate', [
        'as' => 'partner-vehicle.exportTemplate',
        'uses' => 'PartnerVehicleController@exportTemplate'
    ]);

    Route::resource('partner-vehicle', 'PartnerVehicleController');
    /* End Partner Vehice */

    authRoutes('backend');

    /* Tinh, thanh */
    Route::get('ward/get-district', [
        'as' => 'ward.getDistrict',
        'uses' => 'WardController@getDistrict'
    ]);
    Route::get('ward/get-ward', [
        'as' => 'ward.getWard',
        'uses' => 'WardController@getWards'
    ]);
    Route::resource('ward', 'WardController');
    /* Tinh, thanh */


    /* Đội xe */
    Route::get('vehicle-team/dataAjax', [
        'as' => 'vehicle-team.dataAjax',
        'uses' => 'VehicleTeamController@dataAjax'
    ]);
    Route::get('vehicle-team/combo-vehicle-team', [
        'as' => 'vehicle-team.combo-vehicle-team',
        'uses' => 'VehicleTeamController@getDataForComboBox'
    ]);

    Route::resource('vehicle-team', 'VehicleTeamController');
    /* Đội xe */

    /* Partner Đội xe */
    Route::get('partner-vehicle-team/dataAjax', [
        'as' => 'partner-vehicle-team.dataAjax',
        'uses' => 'PartnerVehicleTeamController@dataAjax'
    ]);
    Route::get('partner-vehicle-team/combo-vehicle-team', [
        'as' => 'partner-vehicle-team.combo-vehicle-team',
        'uses' => 'PartnerVehicleTeamController@getDataForComboBox'
    ]);

    Route::resource('partner-vehicle-team', 'PartnerVehicleTeamController');
    /* Partner Đội xe */

    /* Orders */
    Route::get('order/generateHeadTable', [
        'as' => 'order.generateHeadTable',
        'uses' => 'OrderController@generateHeadTable'
    ]);
    Route::any('order/advance', [
        'as' => 'order.advance',
        'uses' => 'OrderController@advance'
    ]);
    Route::get('order/combo-order', [
        'as' => 'order.combo-order',
        'uses' => 'OrderController@getDataForComboBox'
    ]);
    Route::get('order/order-history', [
        'as' => 'order.order-history',
        'uses' => 'OrderController@getOrderHistory'
    ]);
    Route::get('order/order-route-map', [
        'as' => 'order.order-route-map',
        'uses' => 'OrderController@getOrderRouteMap'
    ]);
    Route::post('order/import', [
        'as' => 'order.import',
        'uses' => 'OrderController@import'
    ]);
    Route::get('order/export', [
        'as' => 'order.export',
        'uses' => 'OrderController@export'
    ]);
    Route::get('order/exportTemplate', [
        'as' => 'order.exportTemplate',
        'uses' => 'OrderController@exportTemplate'
    ]);
    Route::get('order/exportConfirm', [
        'as' => 'order.exportConfirm',
        'uses' => 'OrderController@exportConfirm'
    ]);
    Route::get('order/exportReportOrderTemplate', [
        'as' => 'order.exportReportOrderTemplate',
        'uses' => 'OrderController@exportReportOrderTemplate'
    ]);
    Route::get('order/exportUpdate', [
        'as' => 'order.exportUpdate',
        'uses' => 'OrderController@exportUpdate'
    ]);
    Route::post('order/updateDocuments', [
        'as' => 'order.updateDocuments',
        'uses' => 'OrderController@updateDocuments'
    ]);
    Route::get('order/suggestion-location', [
        'as' => 'order.suggestionLocation',
        'uses' => 'OrderController@suggestionLocation'
    ]);
    Route::post('order/printBillFromUrl', [
        'as' => 'order.printBillFromUrl',
        'uses' => 'OrderController@printBillFromUrl'
    ]);
    Route::post('order/qrcode', [
        'as' => 'order.qrcode',
        'uses' => 'OrderController@qrcode'
    ]);
    Route::get('order/update-revenue', [
        'as' => 'order.update-revenue',
        'uses' => 'OrderController@updateRevenue'
    ]);
    Route::post('order/mass-update-revenue', [
        'as' => 'order.mass-update-revenue',
        'uses' => 'OrderController@massUpdateRevenue'
    ]);
    Route::get('order/update-vin-no', [
        'as' => 'order.update-vin-no',
        'uses' => 'OrderController@updateVinNo'
    ]);
    Route::post('order/mass-update-vin-no', [
        'as' => 'order.mass-update-vin-no',
        'uses' => 'OrderController@massUpdateVinNo'
    ]);
    Route::post('order/lock', [
        'as' => 'order.lock',
        'uses' => 'OrderController@lock'
    ]);
    Route::post('order/unlock', [
        'as' => 'order.unlock',
        'uses' => 'OrderController@unlock'
    ]);
    Route::post('order/update-partner-form', [
        'as' => 'order.updatePartnerForm',
        'uses' => 'OrderController@updatePartnerForm'
    ]);
    Route::post('order/update-partner', [
        'as' => 'order.updatePartner',
        'uses' => 'OrderController@updatePartner'
    ]);
    Route::resource('order', 'OrderController');
    /* Orders */

    // location
    Route::get('location/combo-location', [
        'as' => 'location.combo-location',
        'uses' => 'LocationController@getDataForComboBox'
    ]);
    Route::any('location/advance', [
        'as' => 'location.advance',
        'uses' => 'LocationController@advance'
    ]);
    Route::post('location/import', [
        'as' => 'location.import',
        'uses' => 'LocationController@import'
    ]);
    Route::post('location/export', [
        'as' => 'location.export',
        'uses' => 'LocationController@export'
    ]);
    Route::get('location/exportTemplate', [
        'as' => 'location.exportTemplate',
        'uses' => 'LocationController@exportTemplate'
    ]);
    Route::get('location/deduplicate', [
        'as' => 'location.deduplicate',
        'uses' => 'LocationController@deduplicate'
    ]);
    Route::post('location/process-deduplicate', [
        'as' => 'location.process-deduplicate',
        'uses' => 'LocationController@processDeduplicate'
    ]);
    Route::resource('location', 'LocationController');


    // Chủ hàng
    Route::get('customer/generateHeadTable', [
        'as' => 'customer.generateHeadTable',
        'uses' => 'CustomerController@generateHeadTable'
    ]);
    Route::any('customer/advance', [
        'as' => 'customer.advance',
        'uses' => 'CustomerController@advance'
    ]);
    Route::get('customer/combo-customer', [
        'as' => 'customer.combo-customer',
        'uses' => 'CustomerController@getDataForComboBox'
    ]);
    Route::post('customer/import', [
        'as' => 'customer.import',
        'uses' => 'CustomerController@import'
    ]);
    Route::get('customer/export', [
        'as' => 'customer.export',
        'uses' => 'CustomerController@export'
    ]);
    Route::get('customer/exportTemplate', [
        'as' => 'customer.exportTemplate',
        'uses' => 'CustomerController@exportTemplate'
    ]);
    Route::get('customer/deduplicate', [
        'as' => 'customer.deduplicate',
        'uses' => 'CustomerController@deduplicate'
    ]);
    Route::post('customer/process-deduplicate', [
        'as' => 'customer.process-deduplicate',
        'uses' => 'CustomerController@processDeduplicate'
    ]);
    Route::resource('customer', 'CustomerController');

    // Khách hàng
    Route::get('client/generateHeadTable', [
        'as' => 'client.generateHeadTable',
        'uses' => 'ClientController@generateHeadTable'
    ]);
    Route::post('client/import', [
        'as' => 'client.import',
        'uses' => 'ClientController@import'
    ]);
    Route::get('client/export', [
        'as' => 'client.export',
        'uses' => 'ClientController@export'
    ]);
    Route::get('client/exportTemplate', [
        'as' => 'client.exportTemplate',
        'uses' => 'ClientController@exportTemplate'
    ]);
    Route::get('client/deduplicate', [
        'as' => 'client.deduplicate',
        'uses' => 'ClientController@deduplicate'
    ]);
    Route::post('client/process-deduplicate', [
        'as' => 'client.process-deduplicate',
        'uses' => 'ClientController@processDeduplicate'
    ]);
    // Khách hàng

    // Hợp đồng
    Route::any('contract/advance', [
        'as' => 'contract.advance',
        'uses' => 'ContractController@advance'
    ]);
    Route::resource('contract', 'ContractController');

    Route::get('quicksearch/order-data', [
        'as' => 'quicksearch.order',
        'uses' => 'QuickSearchController@order'
    ]);

    Route::get('quicksearch/order-customer-data', [
        'as' => 'quicksearch.orderCustomer',
        'uses' => 'QuickSearchController@orderCustomer'
    ]);

    Route::get('quicksearch/vehicle-data', [
        'as' => 'quicksearch.vehicle',
        'uses' => 'QuickSearchController@vehicle'
    ]);

    Route::get('quicksearch/driver-data', [
        'as' => 'quicksearch.driver',
        'uses' => 'QuickSearchController@driver'
    ]);

    Route::get('quicksearch/contact-data', [
        'as' => 'quicksearch.contact',
        'uses' => 'QuickSearchController@contact'
    ]);
    Route::get('quicksearch/routes-data', [
        'as' => 'quicksearch.routes',
        'uses' => 'QuickSearchController@routes'
    ]);
    Route::get('quicksearch/quota-data', [
        'as' => 'quicksearch.quota',
        'uses' => 'QuickSearchController@quota'
    ]);
    Route::get('quicksearch/goods-data', [
        'as' => 'quicksearch.goods',
        'uses' => 'QuickSearchController@goods'
    ]);
    Route::get('quicksearch/location-data', [
        'as' => 'quicksearch.location',
        'uses' => 'QuickSearchController@location'
    ]);

    Route::get('quicksearch/customer-data', [
        'as' => 'quicksearch.customer',
        'uses' => 'QuickSearchController@customer'
    ]);

    Route::get('quicksearch/vehicle-team-data', [
        'as' => 'quicksearch.vehicle-team',
        'uses' => 'QuickSearchController@vehicleTeam'
    ]);
    Route::get('quicksearch/full-search', [
        'as' => 'quicksearch.full-search',
        'uses' => 'QuickSearchController@fullSearch'
    ]);

    /* Trip */

    // System code config
    Route::any('system-code-config/advance', [
        'as' => 'system-code-config.advance',
        'uses' => 'SystemCodeConfigController@advance'
    ]);

    Route::get('system-code-config/get-code-config', [
        'as' => 'system-code-config.getCodeConfig',
        'uses' => 'SystemCodeConfigController@getCodeConfig'
    ]);
    Route::resource('system-code-config', 'SystemCodeConfigController');

    Route::get('system-code/get-code', [
        'as' => 'system-code.getCode',
        'uses' => 'SystemCodeConfigController@getCode'
    ]);


    // Web Fcm
    Route::get('notification-log/urlUpdateNotification', [
        'as' => 'notification-log.urlUpdateNotification',
        'uses' => 'NotificationLogController@urlUpdateNotification'
    ]);
    Route::get('notification-log/updateTokenFcm', [
        'as' => 'notification-log.updateTokenFcm',
        'uses' => 'NotificationLogController@updateTokenFcm'
    ]);
    Route::get('notification-log/clickToNotificationItem/{id}', [
        'as' => 'notification-log.clickToNotificationItem',
        'uses' => 'NotificationLogController@clickToNotificationItem'
    ]);
    Route::get('notification-log/displayNotification', [
        'as' => 'notification-log.displayNotification',
        'uses' => 'NotificationLogController@displayNotification'
    ]);
    Route::get('notification-log/makeReadAllNotification', [
        'as' => 'notification-log.makeReadAllNotification',
        'uses' => 'NotificationLogController@makeReadAllNotification'
    ]);
    Route::get('notification-log/getNotification', [
        'as' => 'notification-log.getNotification',
        'uses' => 'NotificationLogController@getNotification'
    ]);
    Route::post('notification-log/update-read-notify', [
        'as' => 'notification-log.updateReadNotify',
        'uses' => 'NotificationLogController@updateReadNotify'
    ]);
    Route::resource('notification-log', 'NotificationLogController');

    // Notification
    Route::resource('notification', 'NotificationController');
    //Column Config
    Route::post('column-config/save-column-config', [
        'as' => 'column-config.saveColumnConfig',
        'uses' => 'ColumnConfigController@saveColumnConfig'
    ]);
    Route::resource('column-config', 'ColumnConfigController');
    /* Route */

    /* Báo cáo xe */
    Route::post('report/getReportData', [
        'as' => 'report.getReportData',
        'uses' => 'ReportController@getReportData'
    ]);

    Route::post('report/report-vehicle-distance', [
        'as' => 'report.reportVehicleDistance',
        'uses' => 'ReportJourneyController@reportVehicleDistance'
    ]);
    Route::post('report/sync-report-vehicle-distance', [
        'as' => 'report.syncDistanceReportDaily',
        'uses' => 'ReportJourneyController@syncDistanceReportDaily'
    ]);
    Route::post('report-customer/report', [
        'as' => 'reportCustomer.report',
        'uses' => 'ReportCustomerController@report'
    ]);
    Route::post('report-vehicle/report', [
        'as' => 'report-vehicle.report',
        'uses' => 'ReportVehicleController@report'
    ]);
    Route::post('report-vehicle-team/report', [
        'as' => 'reportVehicleTeam.report',
        'uses' => 'ReportVehicleTeamController@report'
    ]);
    /* Báo cáo xe */


    Route::get('route/{id}/approval', [
        'as' => 'route.approval',
        'uses' => 'RouteController@approval'
    ]);
    Route::post('route/{id}/approval', [
        'as' => 'route.approval',
        'uses' => 'RouteController@approval'
    ]);
    Route::get('route/generateHeadTable', [
        'as' => 'route.generateHeadTable',
        'uses' => 'RouteController@generateHeadTable'
    ]);
    Route::get('route/combo-route', [
        'as' => 'route.combo-route',
        'uses' => 'RouteController@getDataForComboBox'
    ]);
    Route::get('route/combo-location', [
        'as' => 'route.combo-location',
        'uses' => 'RouteController@getLocationsByOrder'
    ]);
    Route::post('route/import', [
        'as' => 'route.import',
        'uses' => 'RouteController@import'
    ]);
    Route::get('route/export', [
        'as' => 'route.export',
        'uses' => 'RouteController@export'
    ]);
    Route::get('route/exportTemplate', [
        'as' => 'route.exportTemplate',
        'uses' => 'RouteController@exportTemplate'
    ]);
    Route::get('route/{id}/price-policy', [
        'as' => 'route.pricePolicy',
        'uses' => 'RouteController@pricePolicy'
    ]);
    Route::get('route/calc-price', [
        'as' => 'route.calcPrice',
        'uses' => 'RouteController@calcPrice'
    ]);
    Route::post('route/calc-revenue', [
        'as' => 'route.calcRevenue',
        'uses' => 'RouteController@calcRevenue'
    ]);
    Route::get('route/calc-capacity', [
        'as' => 'route.calcCapacity',
        'uses' => 'RouteController@calcCapacity'
    ]);
    Route::get('route/{id}/payroll', [
        'as' => 'route.payroll',
        'uses' => 'RouteController@payroll'
    ]);
    Route::get('route/calc-payroll', [
        'as' => 'route.calcPayroll',
        'uses' => 'RouteController@calcPayroll'
    ]);
    Route::post('route/save-pay-roll', [
        'as' => 'route.savePayroll',
        'uses' => 'RouteController@savePayroll'
    ]);
    Route::get('route/getVehicleDriverByRoute', [
        'as' => 'route.getVehicleDriverByRoute',
        'uses' => 'RouteController@getVehicleDriverByRoute'
    ]);
    Route::get('route/{id}/shipping-order', [
        'as' => 'route.shipping-order',
        'uses' => 'RouteController@shippingOrder'
    ]);
    Route::post('route/lock', [
        'as' => 'route.lock',
        'uses' => 'RouteController@lock'
    ]);
    Route::post('route/unlock', [
        'as' => 'route.unlock',
        'uses' => 'RouteController@unlock'
    ]);
    Route::resource('route', 'RouteController');

    /* Quota */
    Route::post('quota/suggest-costs-by-locations', [
        'as' => 'quota.suggest-costs-by-locations',
        'uses' => 'QuotaController@getCostsByLocations'
    ]);
    Route::get('quota/combo-quota', [
        'as' => 'quota.combo-quota',
        'uses' => 'QuotaController@getDataForComboBox'
    ]);
    Route::get('quota/get-costs-by-quota', [
        'as' => 'quota.get-costs-by-quota',
        'uses' => 'QuotaController@getCostsByQuota'
    ]);
    Route::post('quota/import', [
        'as' => 'quota.import',
        'uses' => 'QuotaController@import'
    ]);
    Route::get('quota/export', [
        'as' => 'quota.export',
        'uses' => 'QuotaController@export'
    ]);
    Route::get('quota/exportTemplate', [
        'as' => 'quota.exportTemplate',
        'uses' => 'QuotaController@exportTemplate'
    ]);
    Route::resource('quota', 'QuotaController');

    // map
    Route::get('journey', [
        'as' => 'journey.index',
        'uses' => 'JourneyController@index'
    ]);
    Route::get('journey/detail/{id}', [
        'as' => 'journey.detail',
        'uses' => 'JourneyController@detail'
    ]);
    // log activity
    Route::get('activity-log', [
        'as' => 'activity-log.index',
        'uses' => 'ActivityLogController@index'
    ]);
    Route::get('activity-log/ajaxSearch', [
        'as' => 'activity-log.ajaxSearch',
        'uses' => 'ActivityLogController@ajaxSearch'
    ]);

    Route::get('import-history', [
        'as' => 'import-history.index',
        'uses' => 'ImportHistoryController@index'
    ]);
    Route::get('import-history/ajaxSearch', [
        'as' => 'import-history.ajaxSearch',
        'uses' => 'ImportHistoryController@ajaxSearch'
    ]);

    Route::post('order/quick-save', [
        'as' => 'order.quickSave',
        'uses' => 'OrderController@quickSave'
    ]);
    Route::post('driver/quick-save', [
        'as' => 'driver.quickSave',
        'uses' => 'DriverController@quickSave'
    ]);
    Route::post('vehicle/quick-save', [
        'as' => 'vehicle.quickSave',
        'uses' => 'VehicleController@quickSave'
    ]);
    Route::post('customer/quick-save', [
        'as' => 'customer.quickSave',
        'uses' => 'CustomerController@quickSave'
    ]);
    Route::post('quota/quick-save', [
        'as' => 'quota.quickSave',
        'uses' => 'QuotaController@quickSave'
    ]);
    Route::post('route/quick-save', [
        'as' => 'route.quickSave',
        'uses' => 'RouteController@quickSave'
    ]);

    Route::post('order-customer/quick-save', [
        'as' => 'order-customer.quickSave',
        'uses' => 'OrderCustomerController@quickSave'
    ]);
    /* Documents */

    Route::post('document/import', [
        'as' => 'document.import',
        'uses' => 'DocumentController@import'
    ]);
    Route::get('document/export', [
        'as' => 'document.export',
        'uses' => 'DocumentController@export'
    ]);
    Route::get('document/exportUpdate', [
        'as' => 'document.exportUpdate',
        'uses' => 'DocumentController@exportUpdate'
    ]);
    Route::get('document/exportConfirm', [
        'as' => 'document.exportConfirm',
        'uses' => 'DocumentController@exportConfirm'
    ]);
    Route::get('document/generateHeadTable', [
        'as' => 'document.generateHeadTable',
        'uses' => 'DocumentController@generateHeadTable'
    ]);
    Route::resource('document', 'DocumentController');

    /* System config */

    Route::post('system-config/updateSystemConfig', [
        'as' => 'system-config.updateSystemConfig',
        'uses' => 'SystemConfigController@updateSystemConfig'
    ]);

    /* Company info */

    Route::post('company-info/stamp', [
        'as' => 'company-info.stamp',
        'uses' => 'CompanyInfoController@stamp'
    ]);
    /* DHKH */
    Route::get('order-customer/generateHeadTable', [
        'as' => 'order-customer.generateHeadTable',
        'uses' => 'OrderCustomerController@generateHeadTable'
    ]);
    Route::get('order-customer/combo-order', [
        'as' => 'order-customer.combo-order',
        'uses' => 'OrderCustomerController@getDataForComboBox'
    ]);
    Route::get('order-customer/check-order-no', [
        'as' => 'order-customer.check-order-no',
        'uses' => 'OrderCustomerController@checkOrderNo'
    ]);
    Route::post('order-customer/import', [
        'as' => 'order-customer.import',
        'uses' => 'OrderCustomerController@import'
    ]);
    Route::get('order-customer/export', [
        'as' => 'order-customer.export',
        'uses' => 'OrderCustomerController@export'
    ]);
    Route::get('order-customer/exportTemplate', [
        'as' => 'order-customer.exportTemplate',
        'uses' => 'OrderCustomerController@exportTemplate'
    ]);
    Route::get('order-customer/orderClient', [
        'as' => 'order-customer.orderClient',
        'uses' => 'OrderCustomerController@orderClient'
    ]);
    Route::post('order-customer/approvedOrderClient', [
        'as' => 'order-customer.approvedOrderClient',
        'uses' => 'OrderCustomerController@approvedOrderClient'
    ]);
    Route::post('order-customer/lock', [
        'as' => 'order-customer.lock',
        'uses' => 'OrderCustomerController@lock'
    ]);
    Route::post('order-customer/unlock', [
        'as' => 'order-customer.unlock',
        'uses' => 'OrderCustomerController@unlock'
    ]);
    Route::post('order-customer/calc-eta', [
        'as' => 'order-customer.calc-eta',
        'uses' => 'OrderCustomerController@calcETA'
    ]);
    Route::get('order-customer/update-revenue', [
        'as' => 'order-customer.update-revenue',
        'uses' => 'OrderCustomerController@updateRevenue'
    ]);
    Route::post('order-customer/mass-update-revenue', [
        'as' => 'order-customer.mass-update-revenue',
        'uses' => 'OrderCustomerController@massUpdateRevenue'
    ]);
    /* DHKH */

    /* Template */
    Route::get('template/mergeTemplate/{type}', [
        'as' => 'template.mergeTemplate',
        'uses' => 'TemplateController@mergeTemplate'
    ]);

    Route::get('template/printCustom', [
        'as' => 'template.printCustom',
        'uses' => 'TemplateController@printCustom'
    ]);

    Route::get('partner-template/printCustom', [
        'as' => 'partner-template.printCustom',
        'uses' => 'PartnerTemplateController@printCustom'
    ]);

    Route::resource('template', 'TemplateController');

    /* Template */

    /* Notify */
    Route::post('notify/load-notify-page', [
        'as' => 'notify.loadNotifyPage',
        'uses' => 'NotificationController@loadNotifyPage'
    ]);
    Route::post('notify/vehicle-notify-detail', [
        'as' => 'notify.vehicleNotifyDetail',
        'uses' => 'NotificationController@vehicleNotifyDetail'
    ]);
    /* Notify */

    /* Customer Group */
    Route::get('customer-group/combo-customer-group', [
        'as' => 'customer-group.combo-customer-group',
        'uses' => 'CustomerGroupController@getDataForComboBox'
    ]);
    /* Customer Group */

    /* Price Quote */
    Route::get('price-quote/combo-price-quote', [
        'as' => 'price-quote.combo-price-quote',
        'uses' => 'PriceQuoteController@getDataForComboBox'
    ]);
    Route::post('price-quote/auto-price-quote', [
        'as' => 'price-quote.auto-price-quote',
        'uses' => 'PriceQuoteController@autoPrice'
    ]);
    /* Price Quote */

    /* Order Price */
    Route::post('order-price/price', [
        'as' => 'order-price.price',
        'uses' => 'OrderPriceController@price'
    ]);
    /* Order Price */

    /* Location Group */
    Route::get('location-group/combo-location-group', [
        'as' => 'location-group.combo-location-group',
        'uses' => 'LocationGroupController@getDataForComboBox'
    ]);

    Route::get('location-group/select-location-group/', [
        'as' => 'location-group.select-location-group',
        'uses' => 'LocationGroupController@getDataForSelect'
    ]);
    /* Location Group */

    /* receipt-payment */
    Route::post('receipt-payment/order', [
        'as' => 'receipt-payment.order',
        'uses' => 'ReceiptPaymentController@order'
    ]);
    /* receipt-payment */

    /* Payroll */
    Route::get('payroll/combo-payroll', [
        'as' => 'payroll.combo-payroll',
        'uses' => 'PayrollController@getDataForComboBox'
    ]);
    /* Payroll */
    /* Phụ tùng */
    Route::get('accessory/generateHeadTable', [
        'as' => 'accessory.generateHeadTable',
        'uses' => 'AccessoryController@generateHeadTable'
    ]);

    /* Phụ tùng */

    /* Phiếu sửa chữa */
    Route::get('repair-ticket/generateHeadTable', [
        'as' => 'repair-ticket.generateHeadTable',
        'uses' => 'RepairTicketController@generateHeadTable'
    ]);
    Route::post('repair-ticket/import', [
        'as' => 'repair-ticket.import',
        'uses' => 'RepairTicketController@import'
    ]);
    Route::get('repair-ticket/export', [
        'as' => 'repair-ticket.export',
        'uses' => 'RepairTicketController@export'
    ]);
    Route::get('repair-ticket/exportTemplate', [
        'as' => 'repair-ticket.exportTemplate',
        'uses' => 'RepairTicketController@exportTemplate'
    ]);
    /* Phiếu sửa chữa */

    /* merge-order */
    Route::get('merge-order/generateHeadTable', [
        'as' => 'merge-order.generateHeadTable',
        'uses' => 'MergeOrderController@generateHeadTable'
    ]);
    Route::post('merge-order/mergeOrderForm', [
        'as' => 'merge-order.mergeOrderForm',
        'uses' => 'MergeOrderController@mergeOrderForm'
    ]);
    Route::post('merge-order/mergeOrderSave', [
        'as' => 'merge-order.mergeOrderSave',
        'uses' => 'MergeOrderController@mergeOrderSave'
    ]);
    Route::post('merge-order/getVehicleByOrders', [
        'as' => 'merge-order.getVehicleByOrders',
        'uses' => 'MergeOrderController@getVehicleByOrders'
    ]);
    Route::post('merge-order/getRouteByVehicles', [
        'as' => 'merge-order.getRouteByVehicles',
        'uses' => 'MergeOrderController@getRouteByVehicles'
    ]);
    Route::post('merge-order/default', [
        'as' => 'merge-order.default',
        'uses' => 'MergeOrderController@default'
    ]);
    /* merge-order */

    /* order editor */
    Route::get('order-editor/customer', [
        'as' => 'order-editor.customer',
        'uses' => 'OrderEditorController@customer'
    ]);
    Route::get('order-editor/customer-detail/{id}', [
        'as' => 'order-editor.customer-detail',
        'uses' => 'OrderEditorController@customerDetail'
    ]);
    Route::get('order-editor/vehicle', [
        'as' => 'order-editor.vehicle',
        'uses' => 'OrderEditorController@vehicle'
    ]);
    Route::get('order-editor/driver', [
        'as' => 'order-editor.driver',
        'uses' => 'OrderEditorController@driver'
    ]);
    Route::get('order-editor/location', [
        'as' => 'order-editor.location',
        'uses' => 'OrderEditorController@location'
    ]);
    Route::get('order-editor/user', [
        'as' => 'order-editor.user',
        'uses' => 'OrderEditorController@user'
    ]);
    Route::get('order-editor/columns', [
        'as' => 'order-editor.columns',
        'uses' => 'OrderEditorController@columns'
    ]);
    Route::post('order-editor/import', [
        'as' => 'order-editor.import',
        'uses' => 'OrderEditorController@import'
    ]);
    /* order editor*/
    /* Dữ liệu mặc định khách hàng */
    Route::get('customer-default-data/ajaxSearch', [
        'as' => 'customer-default-data.ajaxSearch',
        'uses' => 'CustomerDefaultDataController@ajaxSearch'
    ]);

    Route::get('customer-default-data/default', [
        'as' => 'customer-default-data.default',
        'uses' => 'CustomerDefaultDataController@defaultData'
    ]);

    /* excel converter */
    Route::get('excel-converter/convert', [
        'as' => 'excel-converter.convert',
        'uses' => 'ExcelConverterController@convert'
    ]);
    /* excel converter*/
    Route::get('redirect-to-store/{type}', [
        'as' => 'redirect-to-store.redirect',
        'uses' => 'GetStartedController@redirect'
    ]);

    Route::get('redirect-to-page/{type?}', [
        'as' => 'redirect-to-page.redirect',
        'uses' => 'GetStartedController@redirectToPage'
    ]);

    Route::post('send-link-app', [
        'as' => 'app.send-link',
        'uses' => 'GetStartedController@getLinkAppByMail'
    ]);

    Route::get('location-type/combo-route', [
        'as' => 'location-type.combo-customer',
        'uses' => 'LocationTypeController@getDataForComboBox'
    ]);

    Route::get('customers/combo-goods-owner', [
        'as' => 'customer.combo-owner',
        'uses' => 'CustomerController@getGoodsOwnerForComboBox'
    ]);

    /* partner-order */
    Route::get('partner-order/generateHeadTable', [
        'as' => 'partner-order.generateHeadTable',
        'uses' => 'PartnerOrderController@generateHeadTable'
    ]);
    Route::post('partner-order/mergeOrderForm', [
        'as' => 'partner-order.mergeOrderForm',
        'uses' => 'PartnerOrderController@mergeOrderForm'
    ]);
    Route::post('partner-order/mergeOrderSave', [
        'as' => 'partner-order.mergeOrderSave',
        'uses' => 'PartnerOrderController@mergeOrderSave'
    ]);
    Route::post('partner-order/getVehicleByOrders', [
        'as' => 'partner-order.getVehicleByOrders',
        'uses' => 'PartnerOrderController@getVehicleByOrders'
    ]);
    Route::post('partner-order/getRouteByVehicles', [
        'as' => 'partner-order.getRouteByVehicles',
        'uses' => 'PartnerOrderController@getRouteByVehicles'
    ]);
    Route::post('partner-order/default', [
        'as' => 'partner-order.default',
        'uses' => 'PartnerOrderController@default'
    ]);
    Route::post('partner-order/requestEditOrderSave', [
        'as' => 'partner-order.requestEditOrderSave',
        'uses' => 'PartnerOrderController@requestEditOrderSave'
    ]);
    Route::post('partner-order/cancelOrderSave', [
        'as' => 'partner-order.cancelOrderSave',
        'uses' => 'PartnerOrderController@cancelOrderSave'
    ]);
    Route::post('partner-order/acceptOrderSave', [
        'as' => 'partner-order.acceptOrderSave',
        'uses' => 'PartnerOrderController@acceptOrderSave'
    ]);
    Route::post('partner-order/completeOrderSave', [
        'as' => 'partner-order.completeOrderSave',
        'uses' => 'PartnerOrderController@completeOrderSave'
    ]);
    /* partner-order */

    Route::get('/partner-dashboard', [
        'as' => 'partner-dashboard.index',
        'uses' => 'PartnerBoardController@index'
    ]);

    /* Location Type */
    Route::get('location-type/combo-location-type/', [
        'as' => 'location-type.combo-location-type',
        'uses' => 'LocationTypeController@getDataForComboBox'
    ]);

    Route::get('location-type/select-location-type/', [
        'as' => 'location-type.select-location-type',
        'uses' => 'LocationTypeController@getDataForSelect'
    ]);
    /* Location Type */

    /* Goods Unit */
    Route::get('goods-unit/combo-goods-unit/', [
        'as' => 'goods-unit.combo-goods-unit',
        'uses' => 'GoodsUnitController@getDataForComboBox'
    ]);
    /* Goods Unit */

    Route::get('order/splitOrder/{id}', [
        'as' => 'order.showSplitOrder',
        'uses' => 'OrderController@showSplitOrder'
    ]);

    Route::post('order/split-order-save', [
        'as' => 'order.splitOrderSave',
        'uses' => 'OrderController@splitOrderSave'
    ]);

    Route::post('order/merge-order-confirm', [
        'as' => 'order.mergeOrderConfirm',
        'uses' => 'OrderController@mergeOrderConfirm'
    ]);

    Route::post('order/merge-order-save', [
        'as' => 'order.mergeOrderSave',
        'uses' => 'OrderController@mergeOrderSave'
    ]);

    Route::get('partner/combo-partner/', [
        'as' => 'partner.combo-partner',
        'uses' => 'PartnerController@getDataForComboBox'
    ]);

    Route::post('vehicle-group/get-vehicle-groups', [
        'as' => 'vehicle-group.getVehicleGroups',
        'uses' => 'VehicleGroupController@getVehicleGroups'
    ]);

    Route::get('vehicle-group/combo-vehicle-group', [
        'as' => 'vehicle-group.combo-vehicle-group',
        'uses' => 'VehicleGroupController@getDataCombobox'
    ]);

    Route::get('client/combo-client', [
        'as' => 'client.combo-client',
        'uses' => 'ClientController@getClientForComboBox'
    ]);

    /* List Resources */
    Route::resources([
        'goods-type' => 'GoodsTypeController',
        'goods-unit' => 'GoodsUnitController',
        'contract-type' => 'ContractTypeController',
        'alert-log' => 'AlertLogController',
        'contact' => 'ContactController',
        'currency' => 'CurrencyController',
        'report-schedule' => 'ReportScheduleController',
        'system-config' => 'SystemConfigController',
        'receipt-payment' => 'ReceiptPaymentController',
        'driver-config-file' => 'DriverConfigFileController',
        'vehicle-config-file' => 'VehicleConfigFileController',
        'vehicle-config-specification' => 'VehicleConfigSpecificationController',
        'province' => 'ProvinceController',
        'district' => 'DistrictController',
        'role' => 'RoleController',
        'vehicle-group' => 'VehicleGroupController',
        'order-customer' => 'OrderCustomerController',
        'location-type' => 'LocationTypeController',
        'location-group' => 'LocationGroupController',
        'notify' => 'NotificationController',
        'customer-group' => 'CustomerGroupController',
        'price-quote' => 'PriceQuoteController',
        'template-payment' => 'TemplatePaymentController',
        'accessory' => 'AccessoryController',
        'repair-ticket' => 'RepairTicketController',
        'payroll' => 'PayrollController',
        'order-price' => 'OrderPriceController',
        'merge-order' => 'MergeOrderController',
        'company-info' => 'CompanyInfoController',
        'order-editor' => 'OrderEditorController',
        'customer-default-data' => 'CustomerDefaultDataController',
        'goods-group' => 'GoodsGroupController',
        'template-excel-converter' => 'TemplateExcelConverterController',
        'excel-converter' => 'ExcelConverterController',
        'get-started' => 'GetStartedController',
        'partner' => 'PartnerController',
        'partner-vehicle' => 'PartnerVehicleController',
        'partner-order' => 'PartnerOrderController',
        'customer-role' => 'CustomerRoleController',
        'partner-get-started' => 'PartnerGetStartedController',
        'partner-board' => 'PartnerBoardController',
        'partner-vehicle-group' => 'PartnerVehicleGroupController',
        'partner-template' => 'PartnerTemplateController',
        'client' => 'ClientController',
    ]);
});

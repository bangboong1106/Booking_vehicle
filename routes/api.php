<?php

use Illuminate\Support\Facades\Route;

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

Route::post('auth/login', 'ApiAppClient\AuthController@login');
Route::get('app-info', 'AppInfoApiController@getAppInfoById');

Route::post('c-login', 'ApiAppClient\AuthIntegrationController@loginM');
Route::post('c-goods-group/list', 'ApiAppClient\ApiWithoutAuthentication\GoodsGroupApiController@list');

Route::group(['middleware' => 'fjwt.auth'], function () {
    Route::get('auth/user', 'ApiAppClient\AuthController@user');
    Route::post('auth/logout', 'ApiAppClient\AuthController@logout');

    Route::get('c-system-code/generate', 'ApiAppClient\SystemCodeApiController@generate');

    Route::post('c-dashboard/status', 'ApiAppClient\DashboardApiController@status');
    Route::post('c-dashboard/order', 'ApiAppClient\DashboardApiController@order');
    Route::post('c-dashboard/profit', 'ApiAppClient\DashboardApiController@profit');


    Route::post('c-notification/update-token-fcm', 'ApiAppClient\NotificationApiController@updateTokenFcmForClient');
    Route::post('c-notification/list', 'ApiAppClient\NotificationApiController@list');
    Route::post('c-notification/read', 'ApiAppClient\NotificationApiController@readAllNotificationLogForCustomer');
    Route::post('c-notification/save', 'ApiAppClient\NotificationApiController@updateNotificationLog');
    Route::get('c-notification/total-unread', 'ApiAppClient\NotificationApiController@totalUnread');

    Route::post('c-staff/list', 'ApiAppClient\StaffApiController@list');
    Route::get('c-staff/detail', 'ApiAppClient\StaffApiController@detail');
    Route::post('c-staff/save', 'ApiAppClient\StaffApiController@save');
    Route::delete('c-staff', 'ApiAppClient\StaffApiController@delete');

    Route::post('c-location/list', 'ApiAppClient\LocationApiController@list');
    Route::get('c-location/detail', 'ApiAppClient\LocationApiController@detail');
    Route::post('c-location/save', 'ApiAppClient\LocationApiController@save');
    Route::delete('c-location', 'ApiAppClient\LocationApiController@delete');

    Route::post('c-client/list', 'ApiAppClient\ClientUserApiController@list');
    Route::get('c-client/detail', 'ApiAppClient\ClientUserApiController@detail');
    Route::delete('c-client', 'ApiAppClient\ClientUserApiController@delete');
    Route::post('c-client/save', 'ApiAppClient\ClientUserApiController@save');

    Route::post('c-user/list', 'ApiAppClient\CustomerApiController@list');
    Route::get('c-user/detail', 'ApiAppClient\CustomerApiController@userinfo');
    Route::post('c-user/save', 'ApiAppClient\CustomerApiController@updateField');
    Route::post('c-user/change-password', 'ApiAppClient\AuthController@changePassword');

    Route::post('c-order/list', 'ApiAppClient\OrderApiController@list');
    Route::get('c-order/detail', 'ApiAppClient\OrderApiController@detail');
    Route::get('c-order/history', 'ApiAppClient\OrderApiController@history');
    Route::get('c-order/review-info', 'ApiAppClient\OrderApiController@reviewInfo');
    Route::post('c-order/review', 'ApiAppClient\OrderApiController@doReview');

    Route::get('c-order-customer/event', 'ApiAppClient\OrderCustomerApiController@event');
    Route::post('c-order-customer/list', 'ApiAppClient\OrderCustomerApiController@list');
    Route::get('c-order-customer/detail', 'ApiAppClient\OrderCustomerApiController@detail');
    Route::post('c-order-customer/save', 'ApiAppClient\OrderCustomerApiController@save');
    Route::delete('c-order-customer', 'ApiAppClient\OrderCustomerApiController@delete');
    Route::get('c-order-customer/order', 'ApiAppClient\OrderCustomerApiController@order');
    Route::post('c-order-customer/save-status', 'ApiAppClient\OrderCustomerApiController@saveStatus');
    Route::post('c-order-customer/export-store', 'ApiAppClient\OrderCustomerApiController@exportStore');
    Route::post('c-order-customer/calc-eta', 'ApiAppClient\OrderCustomerApiController@calcETA');
    Route::post('c-order-customer/calc-amount-estimate', 'ApiAppClient\OrderCustomerApiController@calcAmountEstimate');

    Route::get('c-order-client/event', 'ApiAppClient\OrderClientApiController@event');
    Route::post('c-order-client/list', 'ApiAppClient\OrderClientApiController@list');
    Route::get('c-order-client/detail', 'ApiAppClient\OrderClientApiController@detail');
    Route::post('c-order-client/save', 'ApiAppClient\OrderClientApiController@save');
    Route::delete('c-order-client', 'ApiAppClient\OrderClientApiController@delete');

    //Route::get('c-order/event', 'ApiAppClient\OrderClientApiController@event');
    // Route::post('c-order/list', 'ApiAppClient\OrderClientApiController@list');
    //Route::get('c-order/detail', 'ApiAppClient\OrderClientApiController@detail');
    //Route::post('c-order/save', 'ApiAppClient\OrderClientApiController@saveFromClient');
    //Route::delete('c-order/delete', 'ApiAppClient\OrderClientApiController@delete');

    Route::post('c-location-client/list', 'ApiAppClient\LocationApiController@list');
    Route::get('c-location/detail', 'ApiAppClient\LocationApiController@detail');
    Route::post('c-location/save', 'ApiAppClient\LocationApiController@save');
    Route::delete('c-location', 'ApiAppClient\LocationApiController@delete');

    Route::post('c-goods/list', 'ApiAppClient\GoodsApiController@list');
    Route::get('c-goods/detail', 'ApiAppClient\GoodsApiController@detail');
    Route::post('c-goods/save', 'ApiAppClient\GoodsApiController@save');
    Route::delete('c-goods', 'ApiAppClient\GoodsApiController@delete');

    Route::post('c-default-data/list', 'ApiAppClient\CustomerDefaultDataApiController@list');
    Route::get('c-default-data/detail', 'ApiAppClient\CustomerDefaultDataApiController@detail');
    Route::post('c-default-data/save', 'ApiAppClient\CustomerDefaultDataApiController@save');
    Route::delete('c-default-data', 'ApiAppClient\CustomerDefaultDataApiController@delete');
    Route::get('c-default-data/default', 'ApiAppClient\CustomerDefaultDataApiController@defaultData');

    Route::get('c-goods-unit', 'ApiAppClient\GoodsUnitApiController@goodUnits');
    Route::post('c-goods-unit/list', 'ApiAppClient\GoodsUnitApiController@list');
    Route::get('c-goods-unit/detail', 'ApiAppClient\GoodsUnitApiController@detail');
    Route::post('c-goods-unit/save', 'ApiAppClient\GoodsUnitApiController@save');
    Route::delete('c-goods-unit', 'ApiAppClient\GoodsUnitApiController@delete');

    Route::post('c-location-type/list', 'ApiAppClient\LocationTypeApiController@list');
    Route::get('c-location-type/detail', 'ApiAppClient\LocationTypeApiController@detail');
    Route::post('c-location-type/save', 'ApiAppClient\LocationTypeApiController@save');
    Route::delete('c-location-type', 'ApiAppClient\LocationTypeApiController@delete');

    Route::post('c-location-group/list', 'ApiAppClient\LocationGroupApiController@list');
    Route::get('c-location-group/detail', 'ApiAppClient\LocationGroupApiController@detail');
    Route::post('c-location-group/save', 'ApiAppClient\LocationGroupApiController@save');
    Route::delete('c-location-group', 'ApiAppClient\LocationGroupApiController@delete');

    Route::post('c-province/list', 'ApiAppClient\ProvinceApiController@list');
    Route::post('c-district/list', 'ApiAppClient\DistrictApiController@list');
    Route::post('c-ward/list', 'ApiAppClient\WardApiController@list');

    Route::post('c-logout', 'ApiAppClient\AuthIntegrationController@logout');

    Route::post('c-uploadFiles', 'ApiAppClient\FileApiController@uploadFiles');
    Route::post('c-updateOrderFiles', 'ApiAppClient\OrderApiController@updateOrderFiles');
});
Route::group(['middleware' => 'fjwt.refresh'], function () {
    Route::get('auth/refresh', 'ApiAppClient\AuthController@refresh');
});

Route::post('versionReview', 'DomainApiController@versionReview');

Route::post('register', 'ApiAppManagement\AdminUserApiController@save');

Route::post('a-login', 'ApiAppManagement\AuthIntegrationController@loginM');
Route::post('d-login', 'AuthIntegrationController@login');
Route::group(['middleware' => 'auth.jwt'], function () {
    //-----------------------------------------------
    // Driver app
    Route::post('d-logout', 'AuthIntegrationController@logout');

    Route::post('d-user/change-password', 'AuthIntegrationController@changePassword');
    Route::post('d-user/detail', 'DriverApiController@userInfoEnquiry');

    Route::post('d-config', 'DomainApiController@configDriverApp');

    Route::post('d-file/upload', 'FileApiController@uploadFiles');
    Route::post('d-update-ready-status', 'DriverApiController@updateReadyStatus');

    Route::post('d-order/list', 'OrderApiController@getOrders');
    Route::get('d-order/detail', 'OrderApiController@getOrderDetail');
    Route::post('d-order/save', 'OrderApiController@updateOrders');
    Route::get('d-order/files', 'OrderApiController@getOrderFiles');
    Route::get('d-order/goods', 'RouteApiController@getGoodsByOrderId');
    Route::post('d-order/locations', 'RouteApiController@getLocationByOrderId');
    Route::post('d-order/bill-no', 'OrderApiController@updateBillNo');
    Route::post('d-order/save-files', 'OrderApiController@updateOrderFiles');
    Route::post('d-order/field', 'OrderApiController@updateOrderField');

    Route::post('d-notification/list', 'AlertLogApiController@loadNotification');
    Route::post('d-notification/save', 'AlertLogApiController@readNotificationDriver');
    Route::post('d-notification/read', 'AlertLogApiController@readAllNotificationDriver');
    Route::post('d-notification/push', 'AlertLogApiController@pushNotification');

    Route::post('d-route/list', 'RouteApiController@getRoutes');
    Route::get('d-route/orders', 'RouteApiController@getOrdersByRouteID');
    Route::get('d-route/detail', 'RouteApiController@getRouteDetail');
    Route::post('d-route/save', 'RouteApiController@updateRoutes');
    Route::post('d-route/save-files', 'RouteApiController@updateRouteFiles');
    Route::get('d-route/files', 'RouteApiController@getFilesRouteByRouteId');
    Route::post('d-route/save-costs', 'RouteApiController@updateCostRoute');
    Route::get('d-route/costs', 'RouteApiController@getCostRoute');
    Route::get('d-route/{id}/shipping-order', 'RouteApiController@shippingOrder');
    Route::post('d-route/statistic', 'AuthIntegrationController@loadCountTabs');

    Route::post('d-location/save', 'DriverApiController@updateLocationDirectly');

    Route::post('d-report/cost-overview', 'RouteApiController@getCostOverviewReport');

    Route::post('d-goods-type/list', 'GoodsTypeApiController@list');
    Route::post('d-goods-unit/list', 'GoodsUnitApiController@list');
    Route::post('d-order-goods/delete', 'OrderApiController@deleteGoods');
    Route::post('d-order-goods/save', 'OrderApiController@saveGoods');


    // ---------------------------------------------------------
    // Administrator App
    Route::post('a-file/upload', 'FileApiController@uploadFiles');
    Route::post('a-file/upload-order', 'ApiAppManagement\OrderApiController@files');
    Route::post('a-file/upload-route', 'RouteApiController@updateRouteFiles');

    Route::post('a-logout', 'ApiAppManagement\AuthIntegrationController@logout');
    Route::post('a-user/change-password', 'ApiAppManagement\AuthIntegrationController@changePassword');
    Route::get('a-user/info', 'ApiAppManagement\UserApiController@getUserInfo');
    Route::get('a-system-code/generate', 'ApiAppManagement\SystemCodeApiController@generate');

    Route::post('a-dashboard/getDashboardData', 'DBAppController@getDashboardData');
    Route::post('a-dashboard/orderCountByToday', 'DBAppController@getOrderCountByToday');

    // Đơn hàng
    Route::post('a-order/route-list', 'ApiAppManagement\OrderApiController@getOrderForRouteList');
    Route::post('a-order/list', 'ApiAppManagement\OrderApiController@list');
    Route::get('a-order/detail', 'ApiAppManagement\OrderApiController@detail');
    Route::get('a-order/history', 'ApiAppManagement\OrderApiController@history');
    Route::get('a-order/route', 'ApiAppManagement\OrderApiController@route');
    Route::post('a-order/control', 'ApiAppManagement\OrderApiController@control');
    Route::post('a-order/order', 'ApiAppManagement\OrderApiController@order');
    Route::get('a-order/auditing', 'ApiAppManagement\OrderApiController@auditing');
    Route::post('a-order/save', 'ApiAppManagement\OrderApiController@save');
    Route::delete('a-order/delete', 'ApiAppManagement\OrderApiController@delete');
    Route::post('a-order/save', 'ApiAppManagement\OrderApiController@save');
    Route::post('a-order/split-order', 'ApiAppManagement\OrderApiController@splitOrder');
    Route::post('a-order/merge-order', 'ApiAppManagement\OrderApiController@mergeOrder');
    Route::post('a-order/update-partner-order', 'ApiAppManagement\OrderApiController@updatePartnerOrder');


    // Chứng từ
    Route::post('a-document/list', 'ApiAppManagement\DocumentApiController@list');
    Route::get('a-document/detail', 'ApiAppManagement\DocumentApiController@detail');
    Route::post('a-document/save', 'ApiAppManagement\DocumentApiController@updateOrderDocument');

    // Thông báo
    Route::post('a-notification/list', 'ApiAppManagement\NotificationApiController@list');
    Route::post('a-notification/vehicle', 'ApiAppManagement\NotificationApiController@vehicle');
    Route::post('a-notification/read', 'ApiAppManagement\NotificationApiController@read');
    Route::get('a-notification/total-unread', 'ApiAppManagement\NotificationApiController@totalUnread');

    //Chuyến
    Route::post('a-route/list', 'ApiAppManagement\RouteApiController@list');
    Route::get('a-route/auditing', 'ApiAppManagement\RouteApiController@auditing');
    Route::get('a-route/detail', 'ApiAppManagement\RouteApiController@detail');
    Route::delete('a-route/delete', 'ApiAppManagement\RouteApiController@delete');
    Route::post('a-route/save', 'ApiAppManagement\RouteApiController@save');
    Route::get('a-route/approved-history', 'ApiAppManagement\RouteApiController@approvedHistory');
    Route::get('a-route/list-cost', 'ApiAppManagement\RouteApiController@getRouteCost');
    Route::post('a-route/approve', 'ApiAppManagement\RouteApiController@approve');
    Route::get('a-route/location', 'ApiAppManagement\RouteApiController@location');
    Route::post('a-route/control', 'ApiAppManagement\RouteApiController@control');

    //Khách hàng
    Route::post('a-customer/list', 'ApiAppManagement\CustomerApiController@list');
    Route::get('a-customer/detail', 'ApiAppManagement\CustomerApiController@detail');
    Route::delete('a-customer/delete', 'ApiAppManagement\CustomerApiController@delete');
    Route::get('a-customer/auditing', 'ApiAppManagement\CustomerApiController@auditing');
    Route::post('a-customer/save', 'ApiAppManagement\CustomerApiController@save');
    Route::post('a-customer/list-by-user', 'ApiAppManagement\CustomerApiController@getCustomersByUser');


    //Tài xế
    Route::post('a-driver/list', 'ApiAppManagement\DriverApiController@list');
    Route::get('a-driver/detail', 'ApiAppManagement\DriverApiController@detail');
    Route::get('a-driver/auditing', 'ApiAppManagement\DriverApiController@auditing');
    Route::delete('a-driver/delete', 'ApiAppManagement\DriverApiController@delete');
    Route::post('a-driver/save', 'ApiAppManagement\DriverApiController@save');
    Route::post('a-driver/list-by-user', 'ApiAppManagement\DriverApiController@getDriversByUser');

    //Xe
    Route::post('a-vehicle/list', 'ApiAppManagement\VehicleApiController@list');
    Route::post('a-vehicle/save', 'ApiAppManagement\VehicleApiController@save');
    Route::get('a-vehicle/detail', 'ApiAppManagement\VehicleApiController@detail');
    Route::get('a-vehicle/auditing', 'ApiAppManagement\VehicleApiController@auditing');
    Route::delete('a-vehicle/delete', 'ApiAppManagement\VehicleApiController@delete');
    Route::get('a-vehicle/map', 'ApiAppManagement\VehicleApiController@map');
    Route::get('a-vehicle/driver', 'ApiAppManagement\VehicleApiController@driver');
    Route::post('a-vehicle/list-by-user', 'ApiAppManagement\VehicleApiController@getVehiclesByUser');

    // Đơn hàng khách hàng
    Route::post('a-order-customer/list', 'ApiAppManagement\OrderCustomerApiController@list');
    Route::get('a-order-customer/detail', 'ApiAppManagement\OrderCustomerApiController@detail');
    Route::get('a-order-customer/auditing', 'ApiAppManagement\OrderCustomerApiController@auditing');
    Route::delete('a-order-customer/delete', 'ApiAppManagement\OrderCustomerApiController@delete');
    Route::post('a-order-customer/save', 'ApiAppManagement\OrderCustomerApiController@save');
    Route::post('a-order-customer/control', 'ApiAppManagement\OrderCustomerApiController@control');

    //Bảng định mức
    Route::post('a-quota/list', 'ApiAppManagement\QuotaApiController@list');
    Route::get('a-quota/detail', 'ApiAppManagement\QuotaApiController@detail');
    Route::delete('a-quota/delete', 'ApiAppManagement\QuotaApiController@delete');
    Route::post('a-quota/save', 'ApiAppManagement\QuotaApiController@save');

    // Chủng loại xe
    Route::post('a-vehicle-group/list', 'ApiAppManagement\VehicleGroupApiController@list');
    Route::get('a-vehicle-group/detail', 'ApiAppManagement\VehicleGroupApiController@detail');
    Route::delete('a-vehicle-group/delete', 'ApiAppManagement\VehicleGroupApiController@delete');
    Route::post('a-vehicle-group/save', 'ApiAppManagement\VehicleGroupApiController@save');

    // ĐỘi tài xế
    Route::post('a-vehicle-team/list', 'ApiAppManagement\VehicleTeamApiController@list');
    Route::get('a-vehicle-team/detail', 'ApiAppManagement\VehicleTeamApiController@detail');
    Route::delete('a-vehicle-team/delete', 'ApiAppManagement\VehicleTeamApiController@delete');
    Route::post('a-vehicle-team/save', 'ApiAppManagement\VehicleTeamApiController@save');

    // Đối tác
    Route::post('a-partner/list', 'ApiAppManagement\PartnerApiController@list');
    Route::get('a-partner/detail', 'ApiAppManagement\PartnerApiController@detail');
    Route::delete('a-partner/delete', 'ApiAppManagement\PartnerApiController@delete');
    Route::post('a-partner/save', 'ApiAppManagement\PartnerApiController@save');


    // Địa điểm
    Route::post('a-location/list', 'ApiAppManagement\LocationApiController@list');
    Route::get('a-location/detail', 'ApiAppManagement\LocationApiController@detail');
    Route::delete('a-location/delete', 'ApiAppManagement\LocationApiController@delete');
    Route::post('a-location/save', 'ApiAppManagement\LocationApiController@save');

    // Hàng hoá
    Route::post('a-goods/list', 'ApiAppManagement\GoodsApiController@list');
    Route::get('a-goods/detail', 'ApiAppManagement\GoodsApiController@detail');
    Route::delete('a-goods/delete', 'ApiAppManagement\GoodsApiController@delete');
    Route::post('a-goods/save', 'ApiAppManagement\GoodsApiController@save');

    Route::post('a-goods-unit/list', 'ApiAppManagement\GoodsUnitApiController@list');
    Route::get('a-goods-unit/detail', 'ApiAppManagement\GoodsUnitApiController@detail');
    Route::delete('a-goods-unit/delete', 'ApiAppManagement\GoodsUnitApiController@delete');
    Route::post('a-goods-unit/save', 'ApiAppManagement\GoodsUnitApiController@save');


    Route::post('a-province/list', 'ApiAppManagement\ProvinceApiController@list');
    Route::post('a-district/list', 'ApiAppManagement\DistrictApiController@list');
    Route::post('a-ward/list', 'ApiAppManagement\WardApiController@list');

    Route::post('a-payment/list', 'ApiAppManagement\PaymentApiController@list');

    Route::get('a-province/detail', 'ApiAppManagement\ProvinceApiController@detail');
    Route::get('a-district/detail', 'ApiAppManagement\DistrictApiController@detail');
    Route::get('a-ward/detail', 'ApiAppManagement\WardApiController@detail');
    Route::get('a-payment/detail', 'ApiAppManagement\PaymentApiController@detail');

    // Nhóm khách hàng
    Route::post('a-customer-group/list', 'ApiAppManagement\CustomerGroupApiController@list');
    Route::get('a-customer-group/detail', 'ApiAppManagement\CustomerGroupApiController@detail');
    Route::delete('a-customer-group/delete', 'ApiAppManagement\CustomerGroupApiController@delete');
    Route::post('a-customer-group/save', 'ApiAppManagement\CustomerGroupApiController@save');

    // Người dùng
    Route::post('a-admin-user/list', 'ApiAppManagement\AdminUserApiController@list');
    Route::get('a-admin-user/detail', 'ApiAppManagement\AdminUserApiController@detail');


    //Phụ tùng
    Route::post('a-accessory/list', 'ApiAppManagement\AccessoryApiController@list');
    Route::get('a-accessory/detail', 'ApiAppManagement\AccessoryApiController@detail');
    Route::delete('a-accessory/delete', 'ApiAppManagement\AccessoryApiController@delete');
    Route::post('a-accessory/save', 'ApiAppManagement\AccessoryApiController@save');

    //Phiếu sửa chữa
    Route::post('a-repair-ticket/list', 'ApiAppManagement\RepairTicketApiController@list');
    Route::get('a-repair-ticket/detail', 'ApiAppManagement\RepairTicketApiController@detail');
    Route::delete('a-repair-ticket/delete', 'ApiAppManagement\RepairTicketApiController@delete');
    Route::post('a-repair-ticket/save', 'ApiAppManagement\RepairTicketApiController@save');
});

Route::middleware('jwt.refresh')->get('/token/refresh', 'AuthIntegrationController@refresh');

<?php

use App\models\functions\AdminFunctions;
use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/', "CommonController@login");
Route::get('/login/', "CommonController@login")->name("login");
Route::get('logout/', "CommonController@logout")->name("logout");
Route::post('/login/', "CommonController@login");


Route::get("list-districts/", "CommonController@listDistricts")->name("get-list-districts");
Route::get("list-streets/", "CommonController@listStreets")->name("get-list-streets");
Route::get("common/get-notifications/", "CommonController@getNotifications")->middleware("auth")->name("get-notifications");
Route::post("/common/update-notification/", "CommonController@updateNotification")->middleware("auth")->name("update-notification");
Route::get("/common/check-notification/", "CommonController@checkNotification")->middleware("auth")->name("check-notification");
Route::get("get-notifications/", "CommonController@getNotifications")->middleware("auth")->name("get-notifications");
Route::post("update-notification/", "CommonController@updateNotification")->middleware("auth")->name("update-notification");
Route::get("check-notification/", "CommonController@checkNotification")->middleware("auth")->name("check-notification");
Route::get("fake-notification/", "CommonController@fakeNotification")->middleware("auth")->name("fake-notification");


Route::get('common/detail-product/', "CommonController@detailProduct")->name("detail-product");
Route::get('/search-product-code/', "CommonController@searchProductCode");

Route::get('/admin/', "AdminController@products")->middleware("auth", 'permission', 'notification')->name("admin-main");
Route::get('/admin/products/', "AdminController@products")->middleware("auth", 'permission', 'notification')->name("admin-products");
Route::get("/admin/form-update-product/", "AdminController@formUpdateProduct")->middleware("auth", 'permission')->name("admin-update-product");
Route::get('/admin/form-add-product/', "AdminController@formAddProduct")->middleware("auth", 'permission')->name("admin-form-add-product");
Route::post("/admin/add-product/", "AdminController@addProduct")->middleware("auth", 'permission')->name("admin-add-product");
Route::post("/admin/update-product/", "AdminController@updateProduct")->middleware("auth", 'permission')->name("admin-update-product");
Route::post("/admin/delete-product/", "AdminController@deleteProduct")->middleware("auth", 'permission')->name("admin-delete-product");

Route::get('/admin/users/', "AdminController@users")->middleware("auth", 'permission', 'notification')->name("admin-users");
Route::get('/admin/form-add-user/', "AdminController@formAddUser")->middleware("auth", 'permission')->name("admin-form-add-user");
Route::get('/admin/form-update-user/', "AdminController@formUpdateUser")->middleware("auth", 'permission')->name("admin-form-update-user");
Route::post('/admin/save-user/', "AdminController@saveUser")->name("admin-save-user");
Route::post('/admin/delete-user/', "AdminController@deleteUser")->middleware("auth", 'permission')->name("admin-delete-user");

Route::get("admin/discounts/", "AdminController@listDiscounts")->middleware("auth", 'permission', 'notification')->name("admin-discounts");
Route::get("admin/form-add-discount/", "AdminController@formAddDiscount")->middleware("auth", 'permission')->name("admin-form-add-discount");
Route::get("admin/form-update-discount/", "AdminController@formUpdateDiscount")->middleware("auth", 'permission')->name("admin-form-update-discount");
Route::post("admin/save-discount/", "AdminController@saveDiscount")->middleware("auth", 'permission')->name("admin-save-discount");
Route::post("admin/delete-discount/", "AdminController@deleteDiscount")->middleware("auth", 'permission')->name("admin-delete-discount");


Route::get("admin/campaign_names/", "AdminController@listCampaignNames")->middleware("auth", 'permission', 'notification')->name("admin-list-campaign-names");
Route::get("admin/form-add-campaign_name/", "AdminController@formAddCampaignName")->middleware("auth", 'permission')->name("admin-form-add-campaign_name");
Route::get("admin/form-update-campaign_name/", "AdminController@formUpdateCampaignName")->middleware("auth", 'permission')->name("admin-form-update-campaign_name");
Route::post("admin/delete-campaign-name/", "AdminController@deleteCampaignName")->middleware("auth", 'permission')->name("admin-delete-campaign-name");
Route::post("admin/save-campaign-name/", "AdminController@saveCampaignName")->middleware("auth", 'permission')->name("admin-save-campaign-name");


Route::get("admin/landing-pages/", "AdminController@listLandingPages")->middleware("auth", 'permission', 'notification')->name("admin-list-landing-pages");
Route::get("admin/form-add-landing-page/", "AdminController@formAddLandingPage")->middleware("auth", 'permission')->name("admin-form-add-landing-page");
Route::get("admin/form-update-landing-page/", "AdminController@formUpdateLandingPage")->middleware("auth", 'permission')->name("admin-form-update-landing-page");
Route::post("admin/delete-landing-page/", "AdminController@deleteLandingPage")->middleware("auth", 'permission')->name("admin-delete-landing-page");
Route::post("admin/save-landing-page/", "AdminController@saveLandingPage")->middleware("auth", 'permission')->name("admin-save-landing-page");

Route::get("admin/config/", "AdminController@config")->middleware("auth", 'permission', 'notification')->name("admin-config");
Route::post("admin/save-config/", "AdminController@saveConfig")->middleware("auth", 'permission')->name("admin-save-config");


Route::get('/storekeeper/', "StorekeeperController@importingProducts")->middleware("auth", 'permission', 'notification')->name("storekeeper-main");
Route::get('/storekeeper/importing-products/', "StorekeeperController@importingProducts")->middleware("auth", 'permission', 'notification')->name("storekeeper-importing-products");
Route::get("storekeeper/form-add-importing-product/", "StorekeeperController@formAddImportingProduct")->middleware("auth", 'permission')->name("storekeeper-form-add-importing-product");
Route::get("storekeeper/form-update-importing-product/", "StorekeeperController@formUpdateImportingProduct")->middleware("auth", 'permission')->name("storekeeper-form-update-importing-product");
Route::post("storekeeper/save-importing-product/", "StorekeeperController@saveImportingProduct")->middleware("auth", 'permission')->name("storekeeper-save-importing-product");
Route::post("storekeeper/delete-importing-product/", "StorekeeperController@deleteImportingProduct")->middleware("auth", 'permission')->name("storekeeper-delete-importing-product");

Route::get('/storekeeper/returning-products/', "StorekeeperController@returningProducts")->middleware("auth", 'permission', 'notification')->name("storekeeper-returning-products");
Route::get("storekeeper/form-add-returning-product/", "StorekeeperController@formAddReturningProduct")->middleware("auth", 'permission')->name("storekeeper-form-add-returning-product");
Route::get("storekeeper/form-update-returning-product/", "StorekeeperController@formUpdateReturningProduct")->middleware("auth", 'permission')->name("storekeeper-form-update-returning-product");
Route::post("storekeeper/save-returning-product/", "StorekeeperController@saveReturningProduct")->middleware("auth", 'permission')->name("storekeeper-save-returning-product");
Route::post("storekeeper/delete-returning-product/", "StorekeeperController@deleteReturningProduct")->middleware("auth", 'permission')->name("storekeeper-delete-returning-product");

Route::get('/storekeeper/failed-products/', "StorekeeperController@failedProducts")->middleware("auth", 'permission', 'notification')->name("storekeeper-failed-products");
Route::get("storekeeper/form-add-failed-product/", "StorekeeperController@formAddFailedProduct")->middleware("auth", 'permission')->name("storekeeper-form-add-failed-product");
Route::get("storekeeper/form-update-failed-product/", "StorekeeperController@formUpdateFailedProduct")->middleware("auth", 'permission')->name("storekeeper-form-update-failed-product");
Route::post("storekeeper/save-failed-product/", "StorekeeperController@saveFailedProduct")->middleware("auth", 'permission')->name("storekeeper-save-failed-product");
Route::post("storekeeper/delete-failed-product/", "StorekeeperController@deleteFailedProduct")->middleware("auth", 'permission')->name("storekeeper-delete-failed-product");

Route::get("storekeeper/inventory-report/", "StorekeeperController@inventoryReport")->middleware("auth", 'permission', 'notification')->name("storekeeper-inventory-report");
Route::get("storekeeper/importing-product-report/", "StorekeeperController@importingProductReport")->middleware("auth", 'permission', 'notification')->name("storekeeper-importing-product-report");
Route::get("storekeeper/returning-product-report/", "StorekeeperController@returningProductReport")->middleware("auth", 'permission', 'notification')->name("storekeeper-importing-product-report");
Route::get("storekeeper/failed-product-report/", "StorekeeperController@failedProductReport")->middleware("auth", 'permission', 'notification')->name("storekeeper-importing-product-report");
Route::get("storekeeper/exporting-product-report/", "StorekeeperController@exportingProductReport")->middleware("auth", 'permission', 'notification')->name("storekeeper-exporting-product-report");

Route::get("storekeeper/importing-product-history/", "StorekeeperController@importingProductHistory")->middleware("auth", 'permission', 'notification')->name("storekeeper-importing-product-history");
Route::get("storekeeper/failed-product-history/", "StorekeeperController@failedProductHistory")->middleware("auth", 'permission', 'notification')->name("storekeeper-failed-product-history");
Route::get("storekeeper/returning-product-history/", "StorekeeperController@returningProductHistory")->middleware("auth", 'permission', 'notification')->name("storekeeper-returning-product-history");
Route::get("storekeeper/exporting-product-history/", "StorekeeperController@exportingProductHistory")->middleware("auth", 'permission', 'notification')->name("storekeeper-exporting-product-history");


Route::get("marketing", "MarketingController@listProducts")->middleware("auth", 'permission', 'notification')->name("marketing-main");
Route::get("marketing/products/", "MarketingController@listProducts")->middleware("auth", 'permission', 'notification')->name("marketing-list-products");
Route::get("/marketing/form-update-product/", "MarketingController@formUpdateProduct")->middleware("auth", 'permission')->name("marketing-update-product");
Route::get('/marketing/form-add-product/', "MarketingController@formAddProduct")->middleware("auth", 'permission')->name("marketing-form-add-product");
Route::post("/marketing/add-product/", "MarketingController@addProduct")->middleware("auth", 'permission')->name("marketing-add-product");
Route::post("/marketing/update-product/", "MarketingController@updateProduct")->middleware("auth", 'permission')->name("marketing-update-product");
Route::post("/marketing/delete-product/", "MarketingController@deleteProduct")->middleware("auth", 'permission')->name("marketing-delete-product");


Route::get("marketing/marketing-products/", "MarketingController@listMarketingProducts")->middleware("auth", 'permission', 'notification')->name("marketing-list-marketing-products");
Route::get("marketing/form-add-marketing-product/", "MarketingController@getFormAddMarketingProduct")->middleware("auth", 'permission')->name("marketing-form-add-marketing-product");
Route::get("marketing/form-update-marketing-product/", "MarketingController@getFormUpdateMarketingProduct")->middleware("auth", 'permission')->name("marketing-form-update-marketing-product");
Route::get("marketing/detail-marketing-product/", "MarketingController@detailMarketingProductCode")->middleware("auth")->name("marketing-detail-marketing-product");
Route::post("marketing/save-marketing-product/", "MarketingController@saveMarketingProduct")->middleware("auth", 'permission')->name("marketing-save-marketing-product");
Route::post("marketing/delete-marketing-product", "MarketingController@deleteMarketingProduct")->middleware("auth", 'permission')->name("marketing-detail-marketing-product");


Route::get("marketing/marketing-sources/", "MarketingController@marketingSources")->middleware("auth", 'permission', 'notification')->name("marketing-sources");
Route::get("marketing/form-add-marketing-source/", "MarketingController@formAddMarketingSource")->middleware("auth", 'permission')->name("marketing-form-add-marketing-source");
Route::get("marketing/form-update-marketing-source/", "MarketingController@formUpdateMarketingSource")->middleware("auth", 'permission')->name("marketing-form-update-marketing-source");
Route::post("marketing/save-marketing-source/", "MarketingController@saveMarketingSource")->middleware("auth", 'permission')->name("marketing-save-marketing-source");
Route::post("marketing/delete-marketing_source/", "MarketingController@deleteMarketingSource")->middleware("auth", 'permission')->name("marketing-save-marketing-source");


Route::get("marketing/bank-accounts/", "MarketingController@listBankAccounts")->middleware("auth", 'permission', 'notification')->name("marketing-list-bank-accounts");
Route::get("marketing/form-add-bank_account/", "MarketingController@formAddBankAccount")->middleware("auth", 'permission')->name("marketing-form-add-bank_account");
Route::get("marketing/form-update-bank_account/", "MarketingController@formUpdateBankAccount")->middleware("auth", 'permission')->name("marketing-form-update-bank_account");
Route::post("marketing/save-bank-account/", "MarketingController@saveBankAccount")->middleware("auth", 'permission')->name("marketing-save-bank-account");
Route::post("marketing/delete-bank-account/", "MarketingController@deleteBankAccount")->middleware("auth", 'permission')->name("marketing-delete-bank-account");
Route::get("marketing/revenue-report/", "MarketingController@revenueReport")->middleware("auth", 'permission', 'notification')->name("marketing-revenue-report");


Route::get("marketing/inventory-report/", "MarketingController@inventoryReport")->middleware("auth", 'permission', 'notification')->name("marketing-inventory-report");

Route::get("sale/", "SaleController@listCustomers")->middleware("auth", 'permission', 'permission', 'notification')->name("sale-main");

Route::get("sale/customers/", "SaleController@listCustomers")->middleware("auth", 'permission', 'notification')->name("sale-list-customers");
Route::get("sale/detail-customer/", "SaleController@detailCustomer")->middleware("auth", 'permission')->name("sale-detail-customer");
Route::get("sale/form-add-customer/", "SaleController@formAddCustomer")->middleware("auth", 'permission')->name("sale-form-add-customer");
Route::get("sale/form-update-customer/", "SaleController@formUpdateCustomer")->middleware("auth", 'permission')->name("sale-form-update-customer");
Route::post("sale/save-customer/", "SaleController@saveCustomer")->middleware("auth", 'permission')->name("sale-save-customer");
Route::post("sale/delete-customer/", "SaleController@deleteCustomer")->middleware("auth", 'permission')->name("sale-delete-customer");

Route::get("sale/schedules/", "SaleController@schedules")->middleware("auth", 'permission', 'notification')->name("sale-list-schedule");
Route::get("sale/form-add-schedule", "SaleController@formAddSchedule")->middleware("auth", 'permission')->name("sale-form-add-schedule");
Route::get("sale/form-update-schedule", "SaleController@formUpdateSchedule")->middleware("auth", 'permission')->name("sale-form-update-schedule");
Route::post("sale/save-schedule/", "SaleController@saveSchedule")->middleware("auth", 'permission')->name("sale-save-schedule");
Route::post("sale/delete-schedule/", "SaleController@deleteSchedule")->middleware("auth", 'permission')->name("sale-delete-schedule");

Route::get("sale/products/", "SaleController@listProducts")->middleware("auth", 'permission', 'notification')->name("sale-list-products");
Route::get("sale/search-product-code/", "SaleController@searchProductCode")->middleware("auth", 'permission')->name("sale-search-product-code");

Route::get("sale/discounts/", "SaleController@listDiscounts")->middleware("auth", 'permission', 'notification')->name("sale-discounts");

Route::get("sale/order-fail-reasons/", "SaleController@listOrderFailReasons")->middleware("auth", 'permission', 'notification')->name("sale-order-fail-reasons");
Route::get("sale/leader/form-add-order-fail-reason/", "SaleController@formAddOrderFailReason")->middleware("auth", 'permission')->name("sale-leader-form-add-order-fail-reason");
Route::get("sale/leader/form-update-order-fail-reason/", "SaleController@formUpdateOrderFailReason")->middleware("auth", 'permission')->name("sale-leader-form-update-order-fail-reason");
Route::post("sale/leader/save-order-fail-reason/", "SaleController@saveOrderFailReason")->middleware("auth", 'permission')->name("sale-leader-save-order-fail-reason");
Route::post("sale/leader/delete-order-fail-reason/", "SaleController@deleteOrderFailReason")->middleware("auth", 'permission')->name("sale-leader-delete-order-fail-reason");


Route::get("sale/orders/", "SaleController@listOrder")->middleware("auth", 'permission', 'notification')->name("sale-list-orders");
Route::get("sale/form-add-order", "SaleController@formAddOrder")->middleware("auth", 'permission')->name("sale-form-add-order");
Route::get("sale/detail-order/", "SaleController@detailOrder")->middleware("auth", 'permission')->name("sale-detail-order");
Route::get("sale/form-update-order/", "SaleController@formUpdateOrder")->middleware("auth", 'permission')->name("sale-form-update-orders");
Route::post("sale/add-order", "SaleController@addOrder")->middleware("auth", 'permission')->name("sale-add-order");
Route::post("sale/update-order/", "SaleController@updateOrder")->middleware("auth", 'permission')->name("sale-update-order");
Route::post("sale/delete-order", "SaleController@deleteOrder")->middleware("auth", 'permission')->name("sale-delete-order");
Route::get("product/price/", "SaleController@queryMarketingProductPrice")->middleware("auth")->name("sale-product-price");

Route::get("sale/exporting-product-history/", "SaleController@exportingProductHistory")->middleware("auth", 'permission', 'notification')->name("sale-exporting-product-history");
Route::get("sale/order-history/", "SaleController@orderHistory")->middleware("auth", 'permission', 'notification')->name("sale-order-history");

Route::get("sale/order-deliver/", "SaleController@orderDeliver")->middleware("auth", 'permission', 'notification')->name("sale-leader-order-deliver");
Route::get("sale/form-prepare-order-deliver/", "SaleController@getFormPrepareOrderDeliver")->middleware("auth")->name("sale-form-prepare-order-deliver");
Route::get("sale/push-order-to-deliver/", "SaleController@pushOrderToGHTK")->middleware("auth")->name("sale-push-order-to-deliver");

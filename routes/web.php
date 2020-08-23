<?php

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

Route::get("get-notifications/", "CommonController@getNotifications")->middleware("auth")->name("get-notifications");
Route::post("update-notification/", "CommonController@updateNotification")->middleware("auth")->name("update-notification");
Route::get("check-notification/", "CommonController@checkNotification")->middleware("auth")->name("check-notification");
Route::get("fake-notification/", "CommonController@fakeNotification")->middleware("auth")->name("fake-notification");

Route::get('/', "CommonController@login");
Route::get('/login/', "CommonController@login")->name("login");
Route::get('logout/', "CommonController@logout")->name("logout");
Route::post('/login/', "CommonController@login");
Route::get('/detail-product/', "CommonController@detailProduct")->name("detail-product");
Route::get('/search-product-code/', "CommonController@searchProductCode");
Route::get('/admin/', "AdminController@products")->middleware("auth", 'notification')->name("admin-main");
Route::get('/admin/products/', "AdminController@products")->middleware("auth", 'notification')->name("admin-products");
Route::get("/admin/form-update-product/", "AdminController@formUpdateProduct")->name("admin-update-product");
Route::get('/admin/form-add-product/', "AdminController@formAddProduct")->name("admin-form-add-product");
Route::post("/admin/add-product/", "AdminController@addProduct")->name("admin-add-product");
Route::post("/admin/update-product/", "AdminController@updateProduct")->name("admin-update-product");
Route::post("/admin/delete-product/", "AdminController@deleteProduct")->name("admin-delete-product");

Route::get('/admin/users/', "AdminController@users")->middleware("auth", 'notification')->name("admin-users");
Route::get('/admin/form-add-user/', "AdminController@formAddUser")->name("admin-form-add-user");
Route::get('/admin/form-update-user/', "AdminController@formUpdateUser")->name("admin-form-update-user");
Route::post('/admin/save-user/', "AdminController@saveUser")->name("admin-save-user");
Route::post('/admin/delete-user/', "AdminController@deleteUser")->name("admin-delete-user");

Route::get("admin/discounts/", "AdminController@listDiscounts")->middleware("auth", 'notification')->name("admin-discounts");
Route::get("admin/form-add-discount/", "AdminController@formAddDiscount")->name("admin-form-add-discount");
Route::get("admin/form-update-discount/", "AdminController@formUpdateDiscount")->name("admin-form-update-discount");
Route::post("admin/save-discount/", "AdminController@saveDiscount")->name("admin-save-discount");
Route::post("admin/delete-discount/", "AdminController@deleteDiscount")->name("admin-delete-discount");


Route::get("admin/campaign_names/", "AdminController@listCampaignNames")->middleware("auth", 'notification')->name("admin-list-campaign-names");
Route::get("admin/form-add-campaign_name/", "AdminController@formAddCampaignName")->name("admin-form-add-campaign_name");
Route::get("admin/form-update-campaign_name/", "AdminController@formUpdateCampaignName")->name("admin-form-update-campaign_name");
Route::post("admin/delete-campaign-name/", "AdminController@deleteCampaignName")->name("admin-delete-campaign-name");
Route::post("admin/save-campaign-name/", "AdminController@saveCampaignName")->name("admin-save-campaign-name");


Route::get("admin/landing-pages/", "AdminController@listLandingPages")->middleware("auth", 'notification')->name("admin-list-landing-pages");
Route::get("admin/form-add-landing-page/", "AdminController@formAddLandingPage")->name("admin-form-add-landing-page");
Route::get("admin/form-update-landing-page/", "AdminController@formUpdateLandingPage")->name("admin-form-update-landing-page");
Route::post("admin/delete-landing-page/", "AdminController@deleteLandingPage")->name("admin-delete-landing-page");
Route::post("admin/save-landing-page/", "AdminController@saveLandingPage")->name("admin-save-landing-page");

Route::get("admin/config/", "AdminController@config")->middleware("auth", 'notification')->name("admin-config");
Route::post("admin/save-config/", "AdminController@saveConfig")->name("admin-save-config");


Route::get('/storekeeper/', "StorekeeperController@importingProducts")->middleware("auth", 'notification')->name("storekeeper-main");
Route::get('/storekeeper/importing-products/', "StorekeeperController@importingProducts")->middleware("auth", 'notification')->name("storekeeper-importing-products");
Route::get("storekeeper/form-add-importing-product/", "StorekeeperController@formAddImportingProduct")->name("storekeeper-form-add-importing-product");
Route::get("storekeeper/form-update-importing-product/", "StorekeeperController@formUpdateImportingProduct")->name("storekeeper-form-update-importing-product");
Route::post("storekeeper/save-importing-product/", "StorekeeperController@saveImportingProduct")->name("storekeeper-save-importing-product");
Route::post("storekeeper/delete-importing-product/", "StorekeeperController@deleteImportingProduct")->name("storekeeper-delete-importing-product");

Route::get('/storekeeper/returning-products/', "StorekeeperController@returningProducts")->middleware("auth", 'notification')->name("storekeeper-returning-products");
Route::get("storekeeper/form-add-returning-product/", "StorekeeperController@formAddReturningProduct")->name("storekeeper-form-add-returning-product");
Route::get("storekeeper/form-update-returning-product/", "StorekeeperController@formUpdateReturningProduct")->name("storekeeper-form-update-returning-product");
Route::post("storekeeper/save-returning-product/", "StorekeeperController@saveReturningProduct")->name("storekeeper-save-returning-product");
Route::post("storekeeper/delete-returning-product/", "StorekeeperController@deleteReturningProduct")->name("storekeeper-delete-returning-product");

Route::get('/storekeeper/failed-products/', "StorekeeperController@failedProducts")->middleware("auth", 'notification')->name("storekeeper-failed-products");
Route::get("storekeeper/form-add-failed-product/", "StorekeeperController@formAddFailedProduct")->name("storekeeper-form-add-failed-product");
Route::get("storekeeper/form-update-failed-product/", "StorekeeperController@formUpdateFailedProduct")->name("storekeeper-form-update-failed-product");
Route::post("storekeeper/save-failed-product/", "StorekeeperController@saveFailedProduct")->name("storekeeper-save-failed-product");
Route::post("storekeeper/delete-failed-product/", "StorekeeperController@deleteFailedProduct")->name("storekeeper-delete-failed-product");

Route::get("storekeeper/inventory-report/", "StorekeeperController@inventoryReport")->middleware("auth", 'notification')->name("storekeeper-inventory-report");
Route::get("storekeeper/importing-product-report/", "StorekeeperController@importingProductReport")->middleware("auth", 'notification')->name("storekeeper-importing-product-report");
Route::get("storekeeper/returning-product-report/", "StorekeeperController@returningProductReport")->middleware("auth", 'notification')->name("storekeeper-importing-product-report");
Route::get("storekeeper/failed-product-report/", "StorekeeperController@failedProductReport")->middleware("auth", 'notification')->name("storekeeper-importing-product-report");
Route::get("storekeeper/exporting-product-report/", "StorekeeperController@exportingProductReport")->middleware("auth", 'notification')->name("storekeeper-exporting-product-report");

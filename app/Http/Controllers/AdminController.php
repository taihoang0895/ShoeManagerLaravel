<?php


namespace App\Http\Controllers;


use App\models\functions\AdminFunctions;
use App\models\functions\CommonFunctions;
use App\models\functions\Log;
use App\models\functions\ResultCode;
use App\models\functions\Util;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function products(Request $request)
    {
        $productCode = trim($request->get("product_code", ""));
        $products = AdminFunctions::findProducts($productCode);

        return view("admin.products", [
            "search_product_code" => $productCode,
            "products" => $products
        ]);
    }

    public function formUpdateProduct(Request $request)
    {
        $response = array(
            "status" => 200,
            "content" => "",
            "message" => ""
        );
        $productCode = trim($request->get("product_code", ""));
        $product = AdminFunctions::getProduct($productCode);
        if ($product != null) {
            $list_suggest_product_sizes = config("settings.list_suggest_product_sizes");
            $list_suggest_product_colors = config("settings.list_suggest_product_color");

            $list_suggest_product_sizes = json_encode($list_suggest_product_sizes);
            $list_suggest_product_colors = json_encode($list_suggest_product_colors);
            $list_detail_products = CommonFunctions::findDetailProducts($product->code);
            $response['content'] = view("admin.admin_edit_product", [
                "product" => $product,
                "list_suggest_product_sizes" => $list_suggest_product_sizes,
                "list_suggest_product_colors" => $list_suggest_product_colors,
                'list_detail_products' => $list_detail_products
            ])->render();
        } else {
            $response['status'] = 406;
        }
        return response()->json($response);
    }


    public function formAddProduct(Request $request)
    {
        $response = array(
            "status" => 200,
            "content" => "",
            "message" => ""
        );


        $emptyProduct = new \stdClass();
        $emptyProduct->code = "";
        $emptyProduct->name = "";
        $emptyProduct->price = "";
        $emptyProduct->historical_cost = "";
        $list_suggest_product_sizes = config("settings.list_suggest_product_sizes");
        $list_suggest_product_colors = config("settings.list_suggest_product_color");

        $list_suggest_product_sizes = json_encode($list_suggest_product_sizes);
        $list_suggest_product_colors = json_encode($list_suggest_product_colors);

        $response['content'] = view("admin.admin_edit_product", [
            "product" => $emptyProduct,
            "list_suggest_product_sizes" => $list_suggest_product_sizes,
            "list_suggest_product_colors" => $list_suggest_product_colors,
            "list_detail_products" => []
        ])->render();
        return response()->json($response);
    }

    public function addProduct(Request $request)
    {
        $response = array(
            "status" => 406,
            "content" => "",
            "message" => ""
        );

        $productCode = trim($request->get("product_code", ''));
        $productName = trim($request->get("product_name", ''));
        $price = Util::parseInt($request->get("product_price", ''));
        $historical_cost = Util::parseInt($request->get("product_historical_cost", ''));

        $listDetailProductJson = json_decode($request->get("list_detail_products", '[]'), true);


        if ($productCode != '' && $productName != '' && $price != null && $price >= 0 && $historical_cost != null &&
            $historical_cost >= 0) {

            $product = new \stdClass();
            $product->code = $productCode;
            $product->name = $productName;
            $product->price = $price;
            $product->historical_cost = $historical_cost;
            $listProductDetails = array();
            foreach ($listDetailProductJson as $detailProductJson) {
                $productSize = trim($detailProductJson['product_size']);
                $productColor = trim($detailProductJson['product_color']);
                if ($productSize == "" || $productColor == "") {
                    $listProductDetails = array();
                    break;
                }
                $detailProduct = new \stdClass();
                $detailProduct->size = $productSize;
                $detailProduct->color = $productColor;
                array_push($listProductDetails, $detailProduct);
            }

            if (count($listProductDetails) != 0) {
                $resultCode = AdminFunctions::addProduct($product, $listProductDetails);
                if ($resultCode == ResultCode::SUCCESS) {
                    $response['status'] = 200;
                } else {
                    if ($resultCode == ResultCode::FAILED_PRODUCT_DUPLICATE_CODE) {
                        $response['message'] = "Trùng mã sản phẩm";
                    }
                }
            }
        }
        return response()->json($response);
    }

    public function updateProduct(Request $request)
    {
        $response = array(
            "status" => 406,
            "content" => "",
            "message" => "Lỗi sửa sản phẩm đã được tạo trong hóa đơn"
        );

        $productCode = trim($request->get("product_code", ''));
        $productName = trim($request->get("product_name", ''));
        $price = Util::parseInt($request->get("product_price", ''));
        $historical_cost = Util::parseInt($request->get("product_historical_cost", ''));

        $listDetailProductJson = json_decode($request->get("list_detail_products", '[]'), true);


        if ($productCode != '' && $productName != '' && $price != null && $price >= 0 && $historical_cost != null &&
            $historical_cost >= 0) {

            $product = new \stdClass();
            $product->code = $productCode;
            $product->name = $productName;
            $product->price = $price;
            $product->historical_cost = $historical_cost;
            $listProductDetails = array();
            foreach ($listDetailProductJson as $detailProductJson) {
                $productSize = trim($detailProductJson['product_size']);
                $productColor = trim($detailProductJson['product_color']);
                if ($productSize == "" || $productColor == "") {
                    $listProductDetails = array();
                    break;
                }
                $detailProduct = new \stdClass();
                $detailProduct->size = $productSize;
                $detailProduct->color = $productColor;
                array_push($listProductDetails, $detailProduct);
            }

            if (count($listProductDetails) != 0) {
                if (AdminFunctions::updateProduct($product, $listProductDetails) == ResultCode::SUCCESS) {
                    $response['status'] = 200;
                }
            }
        }
        return response()->json($response);
    }

    public function deleteProduct(Request $request)
    {
        $response = array(
            "status" => 406,
            "content" => "",
            "message" => ""
        );
        $productCode = trim($request->get("product_code", ''));
        if (AdminFunctions::deleteProduct($productCode) == ResultCode::SUCCESS) {
            $response['status'] = 200;
        }
        return response()->json($response);
    }

    public function users(Request $request)
    {
        $searchUsername = trim($request->get("username", ""));
        $users = AdminFunctions::findUsers(Auth::user(), $searchUsername);

        return view("admin.admin_list_users", [
            "list_users" => $users,
            "search_username" => $searchUsername
        ]);
    }

    public function formAddUser(Request $request)
    {
        $response = array(
            "status" => 200,
            "content" => "",
            "message" => ""
        );

        $emptyUser = new \stdClass();
        $emptyUser->id = -1;
        $emptyUser->username = "";
        $emptyUser->password = "";
        $emptyUser->alias_name = "";
        $emptyUser->role_name = "Member";
        $emptyUser->department_name = "Sale";
        $response['content'] = view("admin.admin_edit_user", [
            "user" => $emptyUser
        ])->render();
        return response()->json($response);
    }

    public function formUpdateUser(Request $request)
    {
        $response = array(
            "status" => 200,
            "content" => "",
            "message" => ""
        );
        $userId = $request->get("user_id", "");
        if ($userId == "") {
            $response['status'] = 406;
            $response['message'] = '';
        } else {
            $user = AdminFunctions::getUser($userId);
            $response['content'] = view("admin.admin_edit_user", [
                "user" => $user
            ])->render();
            return response()->json($response);
        }
    }

    public function saveUser(Request $request)
    {
        $response = array(
            "status" => 406,
            "content" => "",
            "message" => ""
        );
        $userId = trim($request->get('user_id', ''));
        $username = trim($request->get('username', ''));
        $password = trim($request->get('password', ''));
        $aliasName = trim($request->get('alias_name', ''));
        $departmentName = trim($request->get('department_name', ''));
        $roleName = trim($request->get('role_name', ''));
        if ($userId == '-1') {
            $userId = null;
        }

        if ($username != "" && $password != "" && $aliasName != "" & $departmentName != "" && $roleName != "") {
            $userInfo = new \stdClass();
            $userInfo->id = $userId;
            $userInfo->username = $username;
            $userInfo->password = $password;
            $userInfo->alias_name = $aliasName;
            $userInfo->department_name = $departmentName;
            $userInfo->role_name = $roleName;
            $resultCode = AdminFunctions::saveUser($userInfo);
            if ($resultCode == ResultCode::SUCCESS) {
                $response['status'] = 200;
            } else {
                if ($resultCode == ResultCode::FAILED_USER_DUPLICATE_USERNAME) {
                    $response['message'] = "Username này đã tồn tại";
                }
            }
        }
        return response()->json($response);
    }

    public function deleteUser(Request $request)
    {
        $response = array(
            "status" => 200,
            "content" => "",
            "message" => ""
        );
        $userId = $request->get('user_id');
        $resultCode = AdminFunctions::deleteUser($userId);
        if ($resultCode != ResultCode::SUCCESS) {
            $response['status'] = 406;
            $response['message'] = 'Lỗi xóa user';
        }
        return response()->json($response);
    }

    public function listDiscounts(Request $request)
    {
        $discountCode = $request->get("discount_code", "");
        $listDiscounts = AdminFunctions::findDiscount($discountCode);
        return view("admin.admin_list_discounts", [
            "list_discounts" => $listDiscounts,
            "search_discount_code" => $discountCode
        ]);

    }

    public function formAddDiscount(Request $request)
    {
        $response = array(
            "status" => 200,
            "content" => "",
            "message" => ""
        );

        $emptyDiscount = new \stdClass();
        $emptyDiscount->id = -1;
        $emptyDiscount->code = "";
        $emptyDiscount->name = "";
        $emptyDiscount->start_time = "";
        $emptyDiscount->end_time = "";
        $emptyDiscount->start_time_str = "";
        $emptyDiscount->end_time_str = "";
        $emptyDiscount->note = "";
        $emptyDiscount->discount_value = 20000;

        $response['content'] = view("admin.admin_edit_discount", [
            "discount" => $emptyDiscount
        ])->render();
        return response()->json($response);

    }

    public function formUpdateDiscount(Request $request)
    {
        $response = array(
            "status" => 406,
            "content" => "",
            "message" => ""
        );

        $discountId = $request->get("discount_id", null);
        if ($discountId != null) {
            $discount = AdminFunctions::getDiscount($discountId);
            $discount->start_time_str = $discount->getStartTimeStr();
            $discount->end_time_str = $discount->getEndTimeStr();
            $response['status'] = 200;
            $response['content'] = view("admin.admin_edit_discount", [
                "discount" => $discount
            ])->render();
        }
        return response()->json($response);


    }

    public function saveDiscount(Request $request)
    {
        $response = array(
            "status" => 406,
            "content" => "",
            "message" => "Lỗi lưu"
        );
        $discountId = $request->get('discount_id', null);
        $name = trim($request->get('name', ''));
        $startTimeStr = $request->get('start_time', '');
        $endTimeStr = $request->get('end_time', '');
        $note = $request->get('note', '');
        $discountValue = Util::parseInt($request->get('discount_value', -1), 0);
        if ($discountValue <= 0) {
            return response()->json($response);
        }
        if ($discountId == "-1") {
            $discountId = null;
        }
        if ($note == null) {
            $note = "";
        }
        $startTime = Util::safeParseDate($startTimeStr);
        $endTime = Util::safeParseDate($endTimeStr);
        $discountInfo = new \stdClass();
        $discountInfo->id = $discountId;
        $discountInfo->name = $name;
        $discountInfo->start_time = $startTime;
        $discountInfo->end_time = $endTime;
        $discountInfo->end_time = $endTime;
        $discountInfo->note = $note;
        $discountInfo->discount_value = $discountValue;
        $resultCode = AdminFunctions::saveDiscount($discountInfo);
        if ($resultCode == ResultCode::SUCCESS) {
            $response['status'] = 200;
        }
        return response()->json($response);
    }

    public function deleteDiscount(Request $request)
    {
        $response = array(
            "status" => 200,
            "content" => "",
            "message" => ""
        );
        $discountId = $request->get('discount_id');
        $resultCode = AdminFunctions::deleteDiscount($discountId);
        if ($resultCode != ResultCode::SUCCESS) {
            $response['status'] = 406;
            $response['message'] = 'Lỗi xóa khuyến mại';
        }
        return response()->json($response);
    }

    public function listCampaignNames(Request $request)
    {
        $campaignName = $request->get('campaign_name', "");
        $listCampaignNames = AdminFunctions::findCampaignName($campaignName);

        return view("admin.admin_list_campaign_names", [
            "list_campaign_names" => $listCampaignNames,
            "search_campaign_name" => $campaignName
        ]);
    }

    public function saveCampaignName(Request $request)
    {
        $response = array(
            "status" => 200,
            "content" => "",
            "message" => ""
        );
        $CampaignNameId = $request->get("campaign_name_id", "");
        $CampaignName = $request->get("name", "");
        $campaignNameInfo = new \stdClass();
        $campaignNameInfo->id = $CampaignNameId;
        $campaignNameInfo->name = $CampaignName;
        $resultCode = AdminFunctions::saveCampaignName($campaignNameInfo);
        if ($resultCode != ResultCode::SUCCESS) {
            $response['status'] = 406;
            $response['message'] = 'Lỗi thêm';
        }
        return response()->json($response);
    }

    public function formAddCampaignName(Request $request)
    {
        $response = array(
            "status" => 200,
            "content" => "",
            "message" => ""
        );
        $emptyCampaignName = new \stdClass();
        $emptyCampaignName->id = -1;
        $emptyCampaignName->name = "";

        $response['content'] = view("admin.admin_edit_campaign_name", [
            "campaign_name" => $emptyCampaignName
        ])->render();
        return response()->json($response);
    }

    public function formUpdateCampaignName(Request $request)
    {
        $response = array(
            "status" => 200,
            "content" => "",
            "message" => ""
        );
        $CampaignNameId = $request->get("campaign_name_id", "");
        $campaignName = AdminFunctions::getCampaignName($CampaignNameId);
        if ($campaignName != null) {
            $response['content'] = view("admin.admin_edit_campaign_name", [
                "campaign_name" => $campaignName
            ])->render();
        } else {
            $response['status'] = 406;
        }


        return response()->json($response);
    }

    public function deleteCampaignName(Request $request)
    {
        $response = array(
            "status" => 200,
            "content" => "",
            "message" => ""
        );
        $campaignNameId = $request->get("campaign_name_id", "");
        $resultCode = AdminFunctions::deleteCampaignName($campaignNameId);
        if ($resultCode != ResultCode::SUCCESS) {
            $response['status'] = 406;
            $response['message'] = 'Lỗi xóa khuyến mại';
        }
        return response()->json($response);
    }

    public function config(Request $request)
    {
        $config = CommonFunctions::getConfig();
        return view("admin.admin_config", [
            "config" => $config,
        ]);
    }

    public function saveConfig(Request $request)
    {
        $response = array(
            "status" => 406,
            "content" => "",
            "message" => ""
        );
        $billCostThreshold = Util::parseInt($request->get("bill_cost_threshold"));
        $commentCostThreshold = Util::parseInt($request->get("comment_cost_threshold"));
        if ($billCostThreshold != null && $commentCostThreshold != null) {
            $configInfo = new \stdClass();
            Log::log("threshold_bill_cost_green", $billCostThreshold);
            $configInfo->threshold_bill_cost_green = $billCostThreshold;
            $configInfo->threshold_comment_cost_green = $commentCostThreshold;
            $resultCode = CommonFunctions::saveConfig($configInfo);
            if ($resultCode == ResultCode::SUCCESS) {
                $response['status'] = 200;
            } else {
                $response['message'] = 'Lỗi xóa khuyến mại';
            }
        }
        return response()->json($response);
    }

    public function formAddLandingPage(Request $request)
    {
        $response = array(
            "status" => 200,
            "content" => "",
            "message" => ""
        );
        $landingPage = new \stdClass();
        $landingPage->id = -1;
        $landingPage->name = "";
        $landingPage->note = "";

        $response['content'] = view("admin.admin_edit_landing_page", [
            "landing_page" => $landingPage
        ])->render();
        return response()->json($response);
    }

    public function listLandingPages(Request $request)
    {
        $landingPageName = $request->get('landing_page_name', "");
        $listLandingPages = AdminFunctions::findLandingPage($landingPageName);

        return view("admin.admin_list_landing_pages", [
            "list_landing_pages" => $listLandingPages,
            "search_landing_page_name" => $landingPageName
        ]);
    }

    public function saveLandingPage(Request $request)
    {
        $response = array(
            "status" => 200,
            "content" => "",
            "message" => ""
        );
        $landingPageId = $request->get("landing_page_id", "");
        $name = $request->get("name", "");
        $note = $request->get("note", "");
        $landingPageInfo = new \stdClass();
        $landingPageInfo->id = $landingPageId;
        $landingPageInfo->name = $name;
        $landingPageInfo->note = $note;
        $resultCode = AdminFunctions::saveLandingPage($landingPageInfo);
        if ($resultCode != ResultCode::SUCCESS) {
            $response['status'] = 406;
            $response['message'] = 'Lỗi thêm';
        }
        return response()->json($response);
    }

    public function formUpdateLandingPage(Request $request)
    {
        $response = array(
            "status" => 200,
            "content" => "",
            "message" => ""
        );
        $landingPageId = $request->get("landing_page_id", "");
        $landingPage = AdminFunctions::getLandingPage($landingPageId);
        if ($landingPage != null) {
            $response['content'] = view("admin.admin_edit_landing_page", [
                "landing_page" => $landingPage
            ])->render();
        } else {
            $response['status'] = 406;
        }


        return response()->json($response);
    }

    public function deleteLandingPage(Request $request)
    {
        $response = array(
            "status" => 200,
            "content" => "",
            "message" => ""
        );
        $landingPageId = $request->get("landing_page_id", "");
        $resultCode = AdminFunctions::deleteLandingPage($landingPageId);
        if ($resultCode != ResultCode::SUCCESS) {
            $response['status'] = 406;
            $response['message'] = 'Lỗi xóa';
        }
        return response()->json($response);
    }

}

<?php


namespace App\Http\Controllers;


use App\models\functions\AdminFunctions;
use App\models\functions\CommonFunctions;
use App\models\functions\Log;
use App\models\functions\ResultCode;
use App\models\functions\SaleFunctions;
use App\models\functions\Util;
use App\models\Order;
use App\models\OrderState;
use App\models\Storage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
            $list_detail_products = $product->listDetailProducts;
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
        $emptyUser->storage_id = -1;
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
            if ($user->storage_id == null) {
                $user->storage_id = -1;
            }
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
        if ($note == null) {
            $note = "";
        }
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

    public function syncOrderState(Request $request)
    {
        $response = array(
            "status" => 200,
            "content" => "",
            "message" => ""
        );
        try {
            $listOrders = Order::where("order_state", OrderState::STATE_PAYMENT_SUCCESSFUL)->get();
            foreach ($listOrders as $order) {
                SaleFunctions::syncOrderState(Auth::user(), $order->id);
            }
        } catch (\Exception $e) {
            $response['status'] = 406;
            $response['message'] = 'Lỗi syncOrderState';
        }
        return response()->json($response);


    }

    public function syncSumActuallyCollected(Request $request)
    {
        $response = array(
            "status" => 200,
            "content" => "Done",
            "message" => ""
        );

        $listRows = DB::table("detail_orders")
            ->select(DB::raw('SUM(actually_collected) as sum_actually_collected'), "order_id")
            ->groupBy("order_id")->get();
        foreach ($listRows as $row) {
            $order = Order::where("id", $row->order_id)->first();
            if ($order != null) {
                $order->sum_actually_collected = $row->sum_actually_collected;
                $order->save();
            }
        }
        return response()->json($response);
    }

    public function syncCustomerSource(Request $request)
    {
        $response = array(
            "status" => 200,
            "content" => "",
            "message" => ""
        );
        $resultCode = AdminFunctions::syncCustomerSource();
        $response['message'] = $resultCode;
        if ($resultCode == ResultCode::SUCCESS) {
            $response['content'] = "syncCustomerSource success";
        } else {
            $response['content'] = "syncCustomerSource failed";
        }
        return response()->json($response);
    }

    public static function syncCustomerState(Request $request)
    {
        $response = array(
            "status" => 200,
            "content" => "",
            "message" => ""
        );
        $resultCode = AdminFunctions::syncCustomerState();
        if ($resultCode == ResultCode::SUCCESS) {
            $response['content'] = "syncCustomerSource success";
        } else {
            $response['content'] = "syncCustomerSource failed";
        }
        return response()->json($response);
    }

    public static function reportProductRevenue(Request $request)
    {

        $listMembers = [];
        $filterMemberId = Util::parseInt($request->get('filter_member_id', -1));
        $filterMemberStr = Auth::user()->alias_name;

        $filterOrderTimeType = Util::parseInt($request->get('filter_order_time_type'), 0);
        $filterOrderTimeTypeText = "";
        $reportTime1Str = $request->get('time1');
        $reportTime2Str = $request->get('time2');
        $fromDateStr = "";
        $toDateStr = "";
        switch ($filterOrderTimeType) {
            case 0:
                $filterOrderTimeTypeText = "Ngày";
                $fromDateStr = $reportTime1Str;
                $toDateStr = $reportTime2Str;
                break;
            case 1:
                $filterOrderTimeTypeText = "Tháng";
                $fromDateStr = "1/" . $reportTime1Str;
                $toDateStr = "1/" . $reportTime2Str;
                break;
            case 2:
                $filterOrderTimeTypeText = "Năm";
                $fromDateStr = "1/1/" . $reportTime1Str;
                $toDateStr = "1/1/" . $reportTime2Str;
                break;
        }
        $fromDate = null;
        $toDate = null;


        $fromDate = Util::safeParseDate($fromDateStr, null);
        $toDate = Util::safeParseDate($toDateStr, null);


        $actives = ["", "", "", "", ""];
        $actives[1] = "active";

        $listRows = AdminFunctions::reportProductRevenue($filterMemberId, $fromDate, $toDate, $filterOrderTimeType);

        $result = SaleFunctions::findAllSales();
        if (Auth::user()->isAdmin()) {
            array_push($listMembers, Auth::user());
        }
        foreach ($result as $member) {
            array_push($listMembers, $member);
        }

        if ($filterMemberId != -1) {
            foreach ($listMembers as $member) {
                if ($member->id == $filterMemberId) {
                    $filterMemberStr = $member->alias_name;
                    break;
                }
            }
        } else {
            $filterMemberId = -1;
            $filterMemberStr = "_______";
        }

        Log::log("taih", "time1 ".$reportTime1Str);

        return view("admin.admin_product_revenue_report", [
            "actives" => $actives,
            "list_rows" => $listRows,
            'filter_member_id' => strval($filterMemberId),
            'filter_member_str' => $filterMemberStr,
            'list_members' => $listMembers,
            "time1" => $reportTime1Str,
            "time2" => $reportTime2Str,
            'filter_order_time_type_text' => $filterOrderTimeTypeText,
            'filter_order_time_type' => $filterOrderTimeType
        ]);
    }

    public static function reportOrderType(Request $request)
    {


        $filterOrderTimeType = Util::parseInt($request->get('filter_order_time_type'), 0);
        $filterOrderTimeTypeText = "";
        $reportTime1Str = $request->get('time1');
        $reportTime2Str = $request->get('time2');
        $fromDateStr = "";
        $toDateStr = "";

        switch ($filterOrderTimeType) {
            case 0:
                $filterOrderTimeTypeText = "Ngày";
                $fromDateStr = $reportTime1Str;
                $toDateStr = $reportTime2Str;
                break;
            case 1:
                $filterOrderTimeTypeText = "Tháng";
                $fromDateStr = "1/" . $reportTime1Str;
                $toDateStr = "1/" . $reportTime2Str;
                break;
            case 2:
                $filterOrderTimeTypeText = "Năm";
                $fromDateStr = "1/1/" . $reportTime1Str;
                $toDateStr = "1/1/" . $reportTime2Str;
                break;
        }
        $fromDate = null;
        $toDate = null;


        $fromDate = Util::safeParseDate($fromDateStr, null);
        $toDate = Util::safeParseDate($toDateStr, null);

        $actives = ["", "", "", "", ""];
        $actives[2] = "active";

        $report = AdminFunctions::reportOrderType($fromDate, $toDate, $filterOrderTimeType);
        return view("admin.admin_order_type_report", [
            "actives" => $actives,
            "report" => $report,
            "time1" => $reportTime1Str,
            "time2" => $reportTime2Str,
            'filter_order_time_type_text' => $filterOrderTimeTypeText,
            'filter_order_time_type' => $filterOrderTimeType
        ]);
    }

    public static function reportOrderEffection(Request $request)
    {
        $listMembers = [];
        $filterMemberId = Util::parseInt($request->get('filter_member_id', -1));
        $filterMemberStr = Auth::user()->alias_name;

        $filterOrderTimeType = Util::parseInt($request->get('filter_order_time_type'), 0);
        $filterOrderTimeTypeText = "";
        $reportTime1Str = $request->get('time1');
        $reportTime2Str = $request->get('time2');
        $fromDateStr = "";
        $toDateStr = "";
        switch ($filterOrderTimeType) {
            case 0:
                $filterOrderTimeTypeText = "Ngày";
                $fromDateStr = $reportTime1Str;
                $toDateStr = $reportTime2Str;
                break;
            case 1:
                $filterOrderTimeTypeText = "Tháng";
                $fromDateStr = "1/" . $reportTime1Str;
                $toDateStr = "1/" . $reportTime2Str;
                break;
            case 2:
                $filterOrderTimeTypeText = "Năm";
                $fromDateStr = "1/1/" . $reportTime1Str;
                $toDateStr = "1/1/" . $reportTime2Str;
                break;
        }
        $fromDate = null;
        $toDate = null;


        $fromDate = Util::safeParseDate($fromDateStr, null);
        $toDate = Util::safeParseDate($toDateStr, null);
        $result = SaleFunctions::findAllSales();
        if (Auth::user()->isAdmin()) {
            array_push($listMembers, Auth::user());
        }
        foreach ($result as $member) {
            array_push($listMembers, $member);
        }

        if ($filterMemberId != -1) {
            foreach ($listMembers as $member) {
                if ($member->id == $filterMemberId) {
                    $filterMemberStr = $member->alias_name;
                    break;
                }
            }
        } else {
            $filterMemberId = -1;
            $filterMemberStr = "_______";
        }

        $listRows = AdminFunctions::reportOrderEffection($filterMemberId, $fromDate, $toDate, $filterOrderTimeType);

        $actives = ["", "", "", "", ""];
        $actives[3] = "active";
        return view("admin.admin_order_effection_report", [
            "actives" => $actives,
            "list_rows" => $listRows,
            'filter_member_id' => strval($filterMemberId),
            'filter_member_str' => $filterMemberStr,
            'list_members' => $listMembers,
            "time1" => $reportTime1Str,
            "time2" => $reportTime2Str,
            'filter_order_time_type_text' => $filterOrderTimeTypeText,
            'filter_order_time_type' => $filterOrderTimeType
        ]);
    }

    public static function reportOverviewWeekly(Request $request)
    {
        $time = Util::parseInt($request->get('time'), -1);
        if ($time == -1) {
            $time = Util::now()->year;
        }
        $weeklyReports = AdminFunctions::reportWeeklyOverviewOrder($time);
        return view("admin.admin_overview_report_weekly", [
            "time" => $time,
            "weekly_reports" => $weeklyReports
        ]);
    }

    public static function reportOverviewDetailOrderState(Request $request)
    {
        $filterOrderTimeType = Util::parseInt($request->get('filter_order_time_type'), 0);
        $filterOrderTimeTypeText = "";
        $reportTime1Str = $request->get('time1');
        $reportTime2Str = $request->get('time2');
        $fromDateStr = "";
        $toDateStr = "";
        switch ($filterOrderTimeType) {
            case 0:
                $filterOrderTimeTypeText = "Ngày";
                $fromDateStr = $reportTime1Str;
                $toDateStr = $reportTime2Str;
                break;
            case 1:
                $filterOrderTimeTypeText = "Tháng";
                $fromDateStr = "1/" . $reportTime1Str;
                $toDateStr = "1/" . $reportTime2Str;
                break;
            case 2:
                $filterOrderTimeTypeText = "Năm";
                $fromDateStr = "1/1/" . $reportTime1Str;
                $toDateStr = "1/1/" . $reportTime2Str;
                break;
        }
        $fromDate = null;
        $toDate = null;


        $fromDate = Util::safeParseDate($fromDateStr, null);
        $toDate = Util::safeParseDate($toDateStr, null);
        $summary = AdminFunctions::reportOverviewOrder($fromDate, $toDate, $filterOrderTimeType);
        return view("admin.admin_overview_report_detail_order_state", [
            "summary" => $summary,
            "time1" => $reportTime1Str,
            "time2" => $reportTime2Str,
            'filter_order_time_type_text' => $filterOrderTimeTypeText,
            'filter_order_time_type' => $filterOrderTimeType
        ]);
    }

    public static function reports(Request $request)
    {
        $actives = ["", "", "", "", ""];
        $actives[0] = "active";
        return view("admin.admin_overview_report", [
            "actives" => $actives,
        ]);
    }

}

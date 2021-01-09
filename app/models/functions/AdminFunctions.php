<?php


namespace App\models\functions;


use App\models\CampaignName;
use App\models\Customer;
use App\models\CustomerState;
use App\models\DetailOrder;
use App\models\DetailProduct;
use App\models\Discount;
use App\models\functions\rows\ProductRow;
use App\models\LandingPage;
use App\models\MarketingProduct;
use App\models\Order;
use App\models\OrderState;
use App\models\Product;
use App\models\ProductCategory;
use App\models\Storage;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use mysql_xdevapi\Exception;

class AdminFunctions
{
    public static function findLandingPage($landingPageName = "")
    {
        $perPage = config('settings.per_page');
        $condition = [];
        if ($landingPageName != "") {
            $condition[] = ['name', 'like', '%' . $landingPageName . '%'];
        }
        $condition['is_active'] = true;

        $listLandingPages = LandingPage::where($condition)->paginate($perPage);

        return $listLandingPages;
    }

    public static function saveLandingPage($landingPageInfo)
    {
        $landingPage = LandingPage::where("id", $landingPageInfo->id)->first();
        if ($landingPage == null) {
            $landingPage = new LandingPage();
            $landingPage->created = Util::now();
        }

        $landingPage->name = $landingPageInfo->name;
        $landingPage->note = $landingPageInfo->note;

        if ($landingPage->save()) {
            return ResultCode::SUCCESS;
        }

        return ResultCode::FAILED_UNKNOWN;
    }

    public static function getLandingPage($id)
    {
        return LandingPage::where("id", $id)->first();
    }

    public static function deleteLandingPage($id)
    {
        $landingPage = LandingPage::where('id', $id)->first();
        if ($landingPage != null) {
            $landingPage->is_active = false;
            if ($landingPage->save()) {
                return ResultCode::SUCCESS;
            }
        }
        return ResultCode::FAILED_UNKNOWN;
    }

    public static function saveUser($userInfo)
    {
        $user = new User();
        if ($userInfo->id != null) {
            $user = User::where("id", $userInfo->id)->first();
            if ($user == null) {
                return ResultCode::FAILED_UNKNOWN;
            }
            if ($user->password != $userInfo->password) {
                $user->password = Hash::make($userInfo->password);
            }
        } else {
            $condition = [];
            $condition['username'] = $userInfo->username;
            if (User::where($condition)->first() != null) {
                return ResultCode::FAILED_USER_DUPLICATE_USERNAME;
            }
            $user->password = Hash::make($userInfo->password);
        }


        $user->username = $userInfo->username;

        $user->alias_name = $userInfo->alias_name;
        $user->department = User::parseDepartmentName($userInfo->department_name);
        $user->is_active = true;
        $user->role = User::parseRoleName($userInfo->role_name);
        if ($user->save()) {
            return ResultCode::SUCCESS;
        }
        return ResultCode::FAILED_UNKNOWN;
    }

    public static function findUsers($user, $searchUsername = "", $excludeUser = [])
    {
        array_push($excludeUser, $user->id);
        $perPage = config('settings.per_page');
        $condition = [];
        $condition[] = ['is_active', true];
        if ($searchUsername != "") {
            $condition[] = ['username', 'like', '%' . $searchUsername . '%'];
        }

        $listUsers = User::where($condition)->whereNotIn('id', $excludeUser)->paginate($perPage);
        foreach ($listUsers as $user) {
            $user->department_name = $user->getDepartmentName();
            $user->role_name = User::getRoleName($user->role);
        }
        return $listUsers;
    }

    public static function getUser($userId)
    {
        $user = User::where('id', $userId)->first();
        if ($user != null) {
            $user->role_name = User::getRoleName($user->role);
            $user->department_name = $user->getDepartmentName();
        }
        return $user;
    }

    public static function deleteUser($id)
    {
        $user = User::where('id', $id)->first();
        if ($user != null) {
            $user->is_active = false;
            if ($user->save()) {
                return ResultCode::SUCCESS;
            }
        }
        return ResultCode::FAILED_UNKNOWN;
    }

    public static function addProduct($productInfo, $listDetailProductInfo)
    {
        DB::beginTransaction();
        try {

            $product = Product::where("code", $productInfo->code)->first();
            if ($product != null) {
                return ResultCode::FAILED_PRODUCT_DUPLICATE_CODE;
            }
            $product = new Product();
            $product->code = $productInfo->code;
            $product->name = $productInfo->name;
            $product->price = $productInfo->price;
            $product->is_test = $productInfo->is_test;
            $product->storage_id = $productInfo->storage_id;
            $product->historical_cost = $productInfo->historical_cost;
            $product->created = Util::now();
            if ($product->save()) {
                foreach ($listDetailProductInfo as $detailProductInfo) {
                    $productCat = ProductCategory::getOrNew($detailProductInfo->size, $detailProductInfo->color);
                    $detailProduct = new DetailProduct();
                    $detailProduct->product_code = $product->code;
                    $detailProduct->product_category_id = $productCat->id;
                    if (!$detailProduct->save()) {
                        throw new \Exception("addProduct -> add detail product failed");
                    }
                }
            } else {
                throw new \Exception("addProduct -> add product failed");
            }
            DB::commit();
            return ResultCode::SUCCESS;
        } catch (\Exception $e) {
            Log::log("error message ", $e->getMessage());
            DB::rollback();
            return ResultCode::FAILED_UNKNOWN;
        }
    }

    private static function inListDetailProduct($listDetailProduct, $size, $color)
    {
        foreach ($listDetailProduct as $detailProduct) {
            if (strcmp($detailProduct->size, $size) == 0 and strcmp($detailProduct->color, $color) == 0) {
                return true;
            }
        }
        return false;
    }

    private static function attachExtraPropertyDetailProduct($detailProduct)
    {
        $productCat = ProductCategory::where("id", $detailProduct->product_category_id)->first();
        if ($productCat != null) {
            $detailProduct->size = $productCat->size;
            $detailProduct->color = $productCat->color;
        }
    }

    public static function updateProduct($productInfo, $listDetailProductInfo)
    {
        $product = Product::where("code", $productInfo->code)->first();
        if ($product == null) {
            return ResultCode::FAILED_PRODUCT_NOT_FOUND;
        }
        DB::beginTransaction();
        try {

            $product->name = $productInfo->name;
            $product->price = $productInfo->price;
            $product->historical_cost = $productInfo->historical_cost;
            $product->is_test = $productInfo->is_test;
            $product->storage_id = $productInfo->storage_id;
            if ($product->save()) {
                $listOldDetailProducts = DetailProduct::where("product_code", $product->code)->get();
                foreach ($listOldDetailProducts as $oldDetailProduct) {
                    self::attachExtraPropertyDetailProduct($oldDetailProduct);
                    if (!self::inListDetailProduct($listDetailProductInfo, $oldDetailProduct->size, $oldDetailProduct->color)) {
                        if (!$oldDetailProduct->delete()) {
                            throw new \Exception("updateProduct -> delete detail product failed");
                        }
                    }
                }
                foreach ($listDetailProductInfo as $detailProductInfo) {
                    if (!self::inListDetailProduct($listOldDetailProducts, $detailProductInfo->size, $detailProductInfo->color)) {
                        $productCat = ProductCategory::getOrNew($detailProductInfo->size, $detailProductInfo->color);
                        $detailProduct = new DetailProduct();
                        $detailProduct->product_code = $product->code;
                        $detailProduct->product_category_id = $productCat->id;
                        if (!$detailProduct->save()) {
                            throw new \Exception("updateProduct -> add detail product failed");
                        }
                    }
                }

            } else {
                throw new \Exception("updateProduct -> add product failed");
            }
            DB::commit();
            return ResultCode::SUCCESS;
        } catch (\Exception $e) {
            Log::log("error message ", $e->getMessage());
            DB::rollback();
            return ResultCode::FAILED_UNKNOWN;
        }
    }

    public static function deleteProduct($productCode)
    {
        try {
            $product = Product::where("code", $productCode)->first();
            if ($product != null) {
                if (DetailOrder::existProduct($productCode)) {
                    return ResultCode::FAILED_DELETE_PRODUCT_EXISTED_IN_ORDER;
                }
                if ($product->delete()) {
                    return ResultCode::SUCCESS;
                }
            }
        } catch (\Exception $e) {
            Log::log("error message ", $e->getMessage());
        }
        return ResultCode::FAILED_UNKNOWN;
    }

    public static function getProduct($productCode)
    {
        $product = Product::where("code", $productCode)->first();
        if ($product != null) {
            $product->storage_address = Storage::get($product->storage_id)->name;
            $listDetailProducts = DetailProduct::where("product_code", $productCode)->get();
            foreach ($listDetailProducts as $detailProduct) {
                $productCategory = ProductCategory::where("id", $detailProduct->product_category_id)->first();
                $detailProduct->color = $productCategory->color;
                $detailProduct->size = $productCategory->size;
            }
            return new ProductRow($product, $listDetailProducts);
        }
        return null;
    }

    public static function findProducts($productCode = null)
    {
        $condition = [];
        if ($productCode != null && $productCode != "") {
            $condition[] = ['code', 'like', '%' . $productCode . '%'];
        }
        $perPage = config('settings.per_page');
        return Product::where($condition)->where("is_active", true)->paginate($perPage);
    }

    public static function saveDiscount($discountRow)
    {
        $discount = Discount::where("id", $discountRow->id)->first();
        if ($discount == null) {
            $discount = new Discount();
            $discount->discount_value = $discountRow->discount_value;
        }
        $discount->name = $discountRow->name;
        $discount->code = "";
        $discount->note = $discountRow->note;
        $discount->start_time = $discountRow->start_time;
        $discount->end_time = $discountRow->end_time;

        if ($discount->save()) {
            $discount->code = "MKM" . Util::formatLeadingZeros($discount->id, 4);
            if ($discount->save()) {
                $discountRow->id = $discount->id;
                return ResultCode::SUCCESS;
            }
        }
        return ResultCode::FAILED_UNKNOWN;

    }

    public static function getDiscount($discountId)
    {
        return Discount::where("id", $discountId)->first();
    }

    public static function findDiscount($discountCode = "")
    {
        $condition = [
            "is_active" => true
        ];
        if ($discountCode != "") {
            $condition[] = ['code', 'like', '%' . $discountCode . '%'];
        }
        $perPage = config('settings.per_page');
        $listDiscounts = Discount::where($condition)->paginate($perPage);
        return $listDiscounts;
    }

    public static function deleteDiscount($discountId)
    {
        try {
            $discount = Discount::where("id", $discountId)->first();
            if ($discount != null) {
                $discount->is_active = false;
                if ($discount->save()) {
                    return ResultCode::SUCCESS;
                }
            }
        } catch (\Exception $e) {
            Log::log("error message ", $e->getMessage());
        }
        return ResultCode::FAILED_UNKNOWN;
    }

    public static function syncCustomerState()
    {
        DB::beginTransaction();
        try {
            $listCustomers = Customer::all();
            foreach ($listCustomers as $customer) {
                if (Order::existCustomer($customer->id)) {
                    $customer->customer_state = CustomerState::STATE_CUSTOMER_ORDER_CREATED;
                    if (!$customer->save()) {
                        throw new \Exception("failed");
                    }
                }
            }

            DB::commit();
            return ResultCode::SUCCESS;
        } catch (\Exception $e) {
            Log::log("error message", $e->getMessage());
            DB::rollBack();
        }
        return ResultCode::FAILED_UNKNOWN;

    }

    public static function syncCustomerSource()
    {
        DB::beginTransaction();
        try {
            $listDetailOrders = DetailOrder::all();
            foreach ($listDetailOrders as $detailOrder) {
                $order = Order::where("id", $detailOrder->order_id)->first();
                $customer = Customer::where("id", $order->customer_id)->first();
                $productCode = "";
                if ($detailOrder->marketing_product_id != null) {

                    $marketingProduct = MarketingProduct::get($detailOrder->marketing_product_id, $order->created);
                    if ($marketingProduct == null) {
                        $productCode = DetailProduct::getProductCode($detailOrder->detail_product_id);
                    } else {
                        $productCode = $marketingProduct->code;
                    }
                } else {
                    $productCode = DetailProduct::getProductCode($detailOrder->detail_product_id);
                }
                if ($productCode == "") {
                    continue;
                }
                $resultCode = SaleFunctions::saveCustomerSource($customer->id, [$productCode], $order->created);
                if ($resultCode != ResultCode::SUCCESS) {
                    throw new \Exception($resultCode);
                }
            }

            DB::commit();
            return ResultCode::SUCCESS;
        } catch (\Exception $e) {
            Log::log("TAIH", $e->getMessage());
            DB::rollBack();
            return $e->getMessage();

        }
        return ResultCode::FAILED_UNKNOWN;
    }

    public static function reportWeeklyOverviewOrder($year)
    {
        $query = DB::table("orders")
            ->select(DB::raw('COUNT(*) as total_order'),
                DB::raw('WEEK(created) as week_num'))
            ->whereYear("orders.created", $year)
            ->groupBy("week_num")
            ->orderBy("week_num", "DESC");
        $weekReports = $query->paginate(6);
        foreach ($weekReports as $report) {
            $report->key = "Tuần " . $report->week_num;
            $report->value = $report->total_order;
        }
        return $weekReports;
    }

    public static function reportOverviewOrder($fromDate, $toDate, $filterOrderTimeType)
    {
        $summary = new \stdClass();
        $summary->total_order = 0;
        $summary->total_pending_order = 0;
        $summary->total_success_order = 0;
        $summary->total_returning_order = 0;
        $summary->total_delivering_order = 0;
        $summary->total_cancel_order = 0;

        $summary->success_order_percent = 0;
        $summary->cancel_order_percent = 0;
        $summary->returning_order_percent = 0;
        $query = DB::table("orders")
            ->select(DB::raw('COUNT(*) as total_order'), "order_state")
            ->groupBy('order_state');
        if ($fromDate != null && $toDate != null) {
            switch ($filterOrderTimeType) {
                case 0:
                    //filter by day
                    $toDate = $toDate->modify('+1 day');
                    $query->whereDate("created", ">=", $fromDate);
                    $query->whereDate("created", "<", $toDate);
                    break;
                case 1:
                    //filter by month
                    $toDate = $toDate->modify('+1 month');
                    $query->whereMonth("created", ">=", $fromDate->month);
                    $query->whereMonth("created", "<", $toDate->month);
                    $query->whereYear("created", ">=", $fromDate->year);
                    $query->whereYear("created", "<=", $toDate->year);
                    break;
                case 2:
                    //filter by year
                    $toDate = $toDate->modify('+1 year');
                    $query->whereYear("created", ">=", $fromDate->year);
                    $query->whereYear("created", "<", $toDate->year);
                    break;
            }
        }
        $listOrderStates = $query->get();


        foreach ($listOrderStates as $orderState) {
            $summary->total_order += $orderState->total_order;
            switch ($orderState->order_state) {
                case OrderState::STATE_ORDER_PENDING:
                    $summary->total_pending_order += $orderState->total_order;
                    break;

                case OrderState::STATE_PAYMENT_SUCCESSFUL:
                case OrderState::STATE_ORDER_DELIVERED:
                    $summary->total_success_order += $orderState->total_order;
                    break;

                case OrderState::STATE_ORDER_FAILED_DELIVERING:
                case OrderState::STATE_PAYMENT_SUCCESSFUL_2:
                case OrderState::STATE_ORDER_IS_RETURNING:
                case OrderState::STATE_ORDER_IS_RETURNED:
                case OrderState::STATE_ORDER_IS_RETURNED_AND_NO_BROKEN:
                case OrderState::STATE_ORDER_IS_RETURNED_AND_BROKEN:
                    $summary->total_returning_order += $orderState->total_order;
                    break;

                case OrderState::STATE_ORDER_CREATED:
                case OrderState::STATE_ORDER_TAKEN:
                case OrderState::STATE_ORDER_TAKING:
                case OrderState::STATE_ORDER_DELIVERING:
                    $summary->total_delivering_order += $orderState->total_order;
                    break;
                case OrderState::STATE_ORDER_FAILED_TAKING:
                case OrderState::STATE_ORDER_CANCEL:
                    $summary->total_cancel_order += $orderState->total_order;
                    break;
            }
            if ($summary->total_order != 0) {
                $summary->success_order_percent = round(($summary->total_success_order * 1.0 / $summary->total_order) * 100, 2);
                $summary->cancel_order_percent = round(($summary->total_cancel_order * 1.0 / $summary->total_order) * 100, 2);
                $summary->returning_order_percent = round(($summary->total_returning_order * 1.0 / $summary->total_order) * 100, 2);
            }

        }


        return $summary;
    }

    public static function reportOrderType($fromDate, $toDate, $filterOrderTimeType)
    {

        $queryRealOrder = DB::table("orders")
            ->select(DB::raw('COUNT(*) as total_order'))
            ->whereNull('orders.replace_order_id')
            ->where("is_test", false);
        $queryTestOrder = DB::table("orders")
            ->select(DB::raw('COUNT(*) as total_order'))
            ->whereNull('orders.replace_order_id')
            ->where("is_test", true);


        $queryRealOrderGroupByUser = DB::table("orders")
            ->select("users.username as username", DB::raw('COUNT(*) as total_order'))
            ->join("users", "orders.user_id", "=", "users.id")
            ->whereNull('orders.replace_order_id')
            ->groupBy("users.username")
            ->where("is_test", false);

        $queryTestOrderGroupByUser = DB::table("orders")
            ->select("users.username as username", DB::raw('COUNT(*) as total_order'))
            ->join("users", "orders.user_id", "=", "users.id")
            ->whereNull('orders.replace_order_id')
            ->groupBy("users.username")
            ->where("is_test", true);

        if ($fromDate != null && $toDate != null) {
            switch ($filterOrderTimeType) {
                case 0:
                    //filter by day
                    $toDate = $toDate->modify('+1 day');
                    $queryRealOrder->whereDate("created", ">=", $fromDate);
                    $queryRealOrder->whereDate("created", "<", $toDate);

                    $queryTestOrder->whereDate("created", ">=", $fromDate);
                    $queryTestOrder->whereDate("created", "<", $toDate);

                    $queryRealOrderGroupByUser->whereDate("created", ">=", $fromDate);
                    $queryRealOrderGroupByUser->whereDate("created", "<", $toDate);

                    $queryTestOrderGroupByUser->whereDate("created", ">=", $fromDate);
                    $queryTestOrderGroupByUser->whereDate("created", "<", $toDate);

                    break;
                case 1:
                    //filter by month
                    $toDate = $toDate->modify('+1 month');
                    $queryRealOrder->whereMonth("created", ">=", $fromDate->month);
                    $queryRealOrder->whereMonth("created", "<", $toDate->month);
                    $queryRealOrder->whereYear("created", ">=", $fromDate->year);
                    $queryRealOrder->whereYear("created", "<=", $toDate->year);

                    $queryTestOrder->whereMonth("created", ">=", $fromDate->month);
                    $queryTestOrder->whereMonth("created", "<", $toDate->month);
                    $queryTestOrder->whereYear("created", ">=", $fromDate->year);
                    $queryTestOrder->whereYear("created", "<=", $toDate->year);

                    $queryRealOrderGroupByUser->whereMonth("created", ">=", $fromDate->month);
                    $queryRealOrderGroupByUser->whereMonth("created", "<", $toDate->month);
                    $queryRealOrderGroupByUser->whereYear("created", ">=", $fromDate->year);
                    $queryRealOrderGroupByUser->whereYear("created", "<=", $toDate->year);

                    $queryTestOrderGroupByUser->whereMonth("created", ">=", $fromDate->month);
                    $queryTestOrderGroupByUser->whereMonth("created", "<", $toDate->month);
                    $queryTestOrderGroupByUser->whereYear("created", ">=", $fromDate->year);
                    $queryTestOrderGroupByUser->whereYear("created", "<=", $toDate->year);
                    break;
                case 2:
                    //filter by year
                    $toDate = $toDate->modify('+1 year');
                    $queryRealOrder->whereYear("created", ">=", $fromDate->year);
                    $queryRealOrder->whereYear("created", "<", $toDate->year);

                    $queryTestOrder->whereYear("created", ">=", $fromDate->year);
                    $queryTestOrder->whereYear("created", "<", $toDate->year);

                    $queryRealOrderGroupByUser->whereYear("created", ">=", $fromDate->year);
                    $queryRealOrderGroupByUser->whereYear("created", "<", $toDate->year);

                    $queryTestOrderGroupByUser->whereYear("created", ">=", $fromDate->year);
                    $queryTestOrderGroupByUser->whereYear("created", "<", $toDate->year);
                    break;
            }
        }
        $reportRealOrder = $queryRealOrder->first();
        $reportTestOrder = $queryTestOrder->first();

        $reportRealOrderGroupByUser = $queryRealOrderGroupByUser->get();
        $reportTestOrderGroupByUser = $queryTestOrderGroupByUser->get();

        $usernameMapRealOrder = [];
        $usernameMapTestOrder = [];


        foreach ($reportRealOrderGroupByUser as $report) {
            $usernameMapRealOrder[$report->username] = $report;
        }

        foreach ($reportTestOrderGroupByUser as $report) {
            $usernameMapTestOrder[$report->username] = $report;
        }
        $listUsers = [];


        foreach ($usernameMapRealOrder as $username => $report) {
            if (!in_array($username, $listUsers)) {
                array_push($listUsers, $username);
            }
        }
        foreach ($usernameMapTestOrder as $username => $report) {
            if (!in_array($username, $listUsers)) {
                array_push($listUsers, $username);
            }
        }
        $listReportBySale = [];
        foreach ($listUsers as $username) {
            $report = new \stdClass();
            $report->username = $username;
            if (array_key_exists($username, $usernameMapRealOrder)) {
                $report->total_real_order = $usernameMapRealOrder[$username]->total_order;
            } else {
                $report->total_real_order = 0;
            }
            if (array_key_exists($username, $usernameMapTestOrder)) {
                $report->total_test_order = $usernameMapTestOrder[$username]->total_order;
            } else {
                $report->total_test_order = 0;
            }
            $report->total_order = $report->total_real_order + $report->total_test_order;
            array_push($listReportBySale, $report);
        }


        $report = new \stdClass();
        $report->total_test_order = $reportTestOrder->total_order;
        $report->total_real_order = $reportRealOrder->total_order;
        $report->total_order = $report->total_test_order + $report->total_real_order;

        $report->list_report_by_sale = $listReportBySale;
        return $report;
    }

    public static function reportOrderEffection($userId, $fromDate, $toDate, $filterOrderTimeType)
    {
        $queryAllOrder = DB::table("customer_sources")
            ->select(
                "customer_sources.product_code as product_code",
                DB::raw('COUNT(orders.id) as total_order'))
            ->join("orders", function ($join) {
                $join->on('customer_sources.customer_id', '=', 'orders.customer_id')
                    ->where('orders.order_state', '!=', OrderState::STATE_ORDER_CANCEL);
            })
            ->whereNull('orders.replace_order_id')
            ->groupBy("customer_sources.product_code");


        $querySuccessOrder = DB::table("customer_sources")
            ->select(
                "customer_sources.product_code as product_code",
                DB::raw('COUNT(orders.id) as total_order'))
            ->join("orders", function ($join) {
                $join->on('customer_sources.customer_id', '=', 'orders.customer_id')
                    ->where('orders.order_state', '!=', OrderState::STATE_ORDER_CANCEL);
            })
            ->whereNull('orders.replace_order_id')
            ->whereIn("orders.order_state", [OrderState::STATE_PAYMENT_SUCCESSFUL, OrderState::STATE_PAYMENT_SUCCESSFUL_2])
            ->groupBy("customer_sources.product_code");

        $queryReturningOrder = DB::table("customer_sources")
            ->select(
                "customer_sources.product_code as product_code",
                DB::raw('COUNT(orders.id) as total_order'))
            ->join("orders", function ($join) {
                $join->on('customer_sources.customer_id', '=', 'orders.customer_id')
                    ->where('orders.order_state', '!=', OrderState::STATE_ORDER_CANCEL);
            })
            ->whereIn("orders.order_state", [OrderState::STATE_ORDER_IS_RETURNING,
                OrderState::STATE_ORDER_IS_RETURNED,
                OrderState::STATE_ORDER_IS_RETURNED_AND_NO_BROKEN,
                OrderState::STATE_ORDER_IS_RETURNED_AND_BROKEN])
            ->whereNull('orders.replace_order_id')
            ->groupBy("customer_sources.product_code");


        if ($userId != -1) {
            $queryAllOrder->where("orders.user_id", $userId);
            $querySuccessOrder->where("orders.user_id", $userId);
            $queryReturningOrder->where("orders.user_id", $userId);
        }
        if ($fromDate != null && $toDate != null) {
            switch ($filterOrderTimeType) {
                case 0:
                    //filter by day
                    $toDate = $toDate->modify('+1 day');
                    $queryAllOrder->whereDate("customer_sources.created", ">=", $fromDate);
                    $queryAllOrder->whereDate("customer_sources.created", "<", $toDate);

                    $querySuccessOrder->whereDate("customer_sources.created", ">=", $fromDate);
                    $querySuccessOrder->whereDate("customer_sources.created", "<", $toDate);

                    $queryReturningOrder->whereDate("customer_sources.created", ">=", $fromDate);
                    $queryReturningOrder->whereDate("customer_sources.created", "<", $toDate);
                    break;
                case 1:
                    //filter by month
                    $toDate = $toDate->modify('+1 month');
                    $queryAllOrder->whereMonth("customer_sources.created", ">=", $fromDate->month);
                    $queryAllOrder->whereMonth("customer_sources.created", "<", $toDate->month);
                    $queryAllOrder->whereYear("customer_sources.created", ">=", $fromDate->year);
                    $queryAllOrder->whereYear("customer_sources.created", "<=", $toDate->year);

                    $querySuccessOrder->whereMonth("customer_sources.created", ">=", $fromDate->month);
                    $querySuccessOrder->whereMonth("customer_sources.created", "<", $toDate->month);
                    $querySuccessOrder->whereYear("customer_sources.created", ">=", $fromDate->year);
                    $querySuccessOrder->whereYear("customer_sources.created", "<=", $toDate->year);

                    $queryReturningOrder->whereMonth("customer_sources.created", ">=", $fromDate->month);
                    $queryReturningOrder->whereMonth("customer_sources.created", "<", $toDate->month);
                    $queryReturningOrder->whereYear("customer_sources.created", ">=", $fromDate->year);
                    $queryReturningOrder->whereYear("customer_sources.created", "<=", $toDate->year);
                    break;
                case 2:
                    //filter by year
                    $toDate = $toDate->modify('+1 year');
                    $queryAllOrder->whereYear("customer_sources.created", ">=", $fromDate->year);
                    $queryAllOrder->whereYear("customer_sources.created", "<", $toDate->year);

                    $querySuccessOrder->whereYear("customer_sources.created", ">=", $fromDate->year);
                    $querySuccessOrder->whereYear("customer_sources.created", "<", $toDate->year);

                    $queryReturningOrder->whereYear("customer_sources.created", ">=", $fromDate->year);
                    $queryReturningOrder->whereYear("customer_sources.created", "<", $toDate->year);
                    break;
            }
        }

        $allOrderReport = $queryAllOrder->get();
        $successOrderReport = $querySuccessOrder->get();
        $returningOrderReport = $queryReturningOrder->get();

        $mapSuccessOrder = [];
        $mapReturningOrder = [];

        foreach ($successOrderReport as $report) {
            $mapSuccessOrder[$report->product_code] = $report;
        }
        foreach ($returningOrderReport as $report) {
            $mapReturningOrder[$report->product_code] = $report;
        }

        $listRows = [];
        $sumSuccessOrder = 0;
        $sumReturningOrder = 0;
        $sumOrder = 0;
        $sumRevenue = 0;
        foreach ($allOrderReport as $report) {
            $productCode = $report->product_code;
            $query = DB::table("detail_orders")
                ->select(DB::raw('SUM(actually_collected) as revenue'))
                ->join("detail_products", "detail_orders.detail_product_id", "=", "detail_products.id")
                ->join("orders", "orders.id", "=", "detail_orders.order_id")
                ->where("orders.is_test", false)
                ->where('detail_products.product_code', $productCode)
                ->whereIn("orders.order_state", [OrderState::STATE_PAYMENT_SUCCESSFUL, OrderState::STATE_PAYMENT_SUCCESSFUL_2]);

            if ($fromDate != null && $toDate != null) {
                $query ->leftJoin("customer_sources", "orders.customer_id", "=", "customer_sources.customer_id");
                switch ($filterOrderTimeType) {
                    case 0:
                        //filter by day
                        $toDate = $toDate->modify('+1 day');
                        $query->whereDate("customer_sources.created", ">=", $fromDate);
                        $query->whereDate("customer_sources.created", "<", $toDate);

                        break;
                    case 1:
                        //filter by month
                        $toDate = $toDate->modify('+1 month');
                        $query->whereMonth("customer_sources.created", ">=", $fromDate->month);
                        $query->whereMonth("customer_sources.created", "<", $toDate->month);
                        $query->whereYear("customer_sources.created", ">=", $fromDate->year);
                        $query->whereYear("customer_sources.created", "<=", $toDate->year);
                        break;
                    case 2:
                        //filter by year
                        $toDate = $toDate->modify('+1 year');
                        $query->whereYear("customer_sources.created", ">=", $fromDate->year);
                        $query->whereYear("customer_sources.created", "<", $toDate->year);
                        break;
                }
            }



            if ($userId != -1) {
                $query->where("orders.user_id", $userId);
            }
            $revenueRow = $query->first();
            $row = new \stdClass();
            $row->product_code = $report->product_code;
            if (array_key_exists($report->product_code, $mapSuccessOrder)) {
                $row->total_success_order = $mapSuccessOrder[$report->product_code]->total_order;
            } else {
                $row->total_success_order = 0;
            }
            if (array_key_exists($report->product_code, $mapReturningOrder)) {
                $row->total_returning_order = $mapReturningOrder[$report->product_code]->total_order;
            } else {
                $row->total_returning_order = 0;
            }
            $row->total_order = $report->total_order;
            $row->revenue = Util::formatMoney($revenueRow->revenue);
            $row->success_order_percent = round($row->total_success_order * 1.0 / $row->total_order * 100, 2);
            $row->returning_order_percent = round($row->total_returning_order * 1.0 / $row->total_order * 100, 2);
            $sumSuccessOrder += $row->total_success_order;
            $sumReturningOrder += $row->total_returning_order;
            $sumRevenue += $revenueRow->revenue;
            $sumOrder += $row->total_order;
            array_push($listRows, $row);
        }
        $row = new \stdClass();
        $row->date_str = "";
        $row->product_code = "TỔNG";
        $row->total_returning_order = $sumReturningOrder;
        $row->total_success_order = $sumSuccessOrder;
        $row->total_order = $sumOrder;
        $row->revenue = Util::formatMoney($sumRevenue);
        if ($sumOrder != 0) {
            $row->success_order_percent = round($sumSuccessOrder * 1.0 / $sumOrder * 100, 2);
            $row->returning_order_percent = round($sumReturningOrder * 1.0 / $sumOrder * 100, 2);
        } else {
            $row->success_order_percent = 0;
            $row->returning_order_percent = 0;
        }
        array_push($listRows, $row);
        return $listRows;
    }

    public static function reportProductRevenue($userId, $fromDate, $toDate, $filterOrderTimeType)
    {
        $query = DB::table("customer_sources")
            ->select(
                "customer_sources.product_code as product_code",
                DB::raw('COUNT(*) as data'),
                DB::raw('COUNT(orders.id) as total_order'))
            ->leftJoin("orders", function ($join) {
                $join->on('customer_sources.customer_id', '=', 'orders.customer_id')
                    ->where('orders.order_state', '!=', OrderState::STATE_ORDER_CANCEL);
            })
            ->whereNull('orders.replace_order_id')
            ->groupBy("customer_sources.product_code");


        if ($fromDate != null && $toDate != null) {
            switch ($filterOrderTimeType) {
                case 0:
                    //filter by day
                    $toDate = $toDate->modify('+1 day');
                    $query->whereDate("customer_sources.created", ">=", $fromDate);
                    $query->whereDate("customer_sources.created", "<", $toDate);
                    break;
                case 1:
                    //filter by month
                    $toDate = $toDate->modify('+1 month');
                    $query->whereMonth("customer_sources.created", ">=", $fromDate->month);
                    $query->whereMonth("customer_sources.created", "<", $toDate->month);
                    $query->whereYear("customer_sources.created", ">=", $fromDate->year);
                    $query->whereYear("customer_sources.created", "<=", $toDate->year);
                    break;
                case 2:
                    //filter by year
                    $toDate = $toDate->modify('+1 year');
                    $query->whereYear("customer_sources.created", ">=", $fromDate->year);
                    $query->whereYear("customer_sources.created", "<", $toDate->year);
                    break;
            }
        }


        if ($userId != -1) {
            $query->join("customers", "customer_sources.customer_id", "=", "customers.id");
            $query->where('customers.user_id', $userId);
        }
        $reportsData = $query->get();
        $listRows = [];
        $sumData = 0;
        $sumOrder = 0;
        $sumRevenue = 0;
        foreach ($reportsData as $report) {

            $productCode = $report->product_code;
            $query = DB::table("detail_orders")
                ->select(DB::raw('SUM(actually_collected) as revenue'))
                ->join("detail_products", "detail_orders.detail_product_id", "=", "detail_products.id")
                ->join("orders", "orders.id", "=", "detail_orders.order_id")
                ->where("orders.is_test", false)
                ->where('detail_products.product_code', $productCode);

            if ($fromDate != null && $toDate != null) {
                $query ->leftJoin("customer_sources", "orders.customer_id", "=", "customer_sources.customer_id");
                switch ($filterOrderTimeType) {
                    case 0:
                        //filter by day
                        $toDate = $toDate->modify('+1 day');
                        $query->whereDate("customer_sources.created", ">=", $fromDate);
                        $query->whereDate("customer_sources.created", "<", $toDate);

                        break;
                    case 1:
                        //filter by month
                        $toDate = $toDate->modify('+1 month');
                        $query->whereMonth("customer_sources.created", ">=", $fromDate->month);
                        $query->whereMonth("customer_sources.created", "<", $toDate->month);
                        $query->whereYear("customer_sources.created", ">=", $fromDate->year);
                        $query->whereYear("customer_sources.created", "<=", $toDate->year);
                        break;
                    case 2:
                        //filter by year
                        $toDate = $toDate->modify('+1 year');
                        $query->whereYear("customer_sources.created", ">=", $fromDate->year);
                        $query->whereYear("customer_sources.created", "<", $toDate->year);
                        break;
                }
            }



            if ($userId != -1) {
                $query->where("orders.user_id", $userId);
            }
            $revenueRow = $query->first();


            $row = new \stdClass();
            $row->product_code = $report->product_code;
            $row->data = $report->data;
            $row->total_order = $report->total_order;
            $row->revenue = Util::formatMoney($revenueRow->revenue);
            $row->cr2 = round($row->total_order * 1.0 / $row->data * 100, 2);

            $sumData += $row->data;
            $sumOrder += $report->total_order;
            $sumRevenue += $revenueRow->revenue;
            array_push($listRows, $row);
        }
        $row = new \stdClass();
        $row->product_code = "TỔNG";
        $row->total_order = $sumOrder;
        $row->data = $sumData;
        $row->cr2 = "";
        $row->revenue = Util::formatMoney($sumRevenue);
        array_push($listRows, $row);
        return $listRows;
    }


}

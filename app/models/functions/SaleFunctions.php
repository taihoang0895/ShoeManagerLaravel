<?php


namespace App\models\functions;


use App\models\Customer;
use App\models\CustomerState;
use App\models\DetailOrder;
use App\models\DetailProduct;
use App\models\Discount;
use App\models\HistoryExportingProduct;
use App\models\HistoryOrder;
use App\models\Inventory;
use App\models\LandingPage;
use App\models\MarketingProduct;
use App\models\Order;
use App\models\OrderFailReason;
use App\models\OrderState;
use App\models\Product;
use App\models\ProductCategory;
use App\models\Remind;
use App\User;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\DB;

class SaleFunctions
{
    public static function findSchedules($user, $startTime = null, $endTime = null)
    {
        $filterOptions = [];
        $filterOptions["user_id"] = $user->id;
        if ($startTime != null && $endTime != null) {
            $filterOptions[] = ['time', ">=", $startTime];
            $filterOptions[] = ['time', "<=", $endTime];
        }
        $perPage = config('settings.per_page');
        $listReminds = Remind::where($filterOptions)->orderBy('time', 'DESC')->paginate($perPage);
        return $listReminds;
    }

    public static function getSchedule($id)
    {
        $schedule = Remind::where("id", $id)->first();
        if ($schedule != null) {
            $schedule->time_str = $schedule->timeStr();
        }
        return $schedule;
    }

    public static function saveSchedule($user, $scheduleInfo)
    {
        try {
            $filterOptions = [];
            $filterOptions["user_id"] = $user->id;
            $filterOptions["id"] = $scheduleInfo->id;
            $schedule = Remind::where($filterOptions)->first();
            if ($schedule == null) {
                $schedule = new Remind();
                $schedule->user_id = $user->id;
                $schedule->created = Util::now();
            }
            if ($scheduleInfo->note == null) {
                $scheduleInfo->note = "";
            }
            $schedule->note = $scheduleInfo->note;
            $schedule->time = $scheduleInfo->time;
            if ($schedule->save()) {
                return ResultCode::SUCCESS;
            }
            return ResultCode::FAILED_UNKNOWN;
        } catch (\Exception $e) {
            Log::log("error message ", $e->getMessage());
            return ResultCode::FAILED_UNKNOWN;
        }

    }

    public static function deleteSchedule($user, $id)
    {
        $filterOptions = [];
        $filterOptions["user_id"] = $user->id;
        $filterOptions["id"] = $id;
        $schedule = Remind::where($filterOptions)->first();
        if ($schedule != null) {
            if (Remind::where($filterOptions)->delete()) {
                return ResultCode::SUCCESS;
            }
        }
        return ResultCode::FAILED_UNKNOWN;
    }

    public static function findListProducts($productCode)
    {
        $query = Product::where([]);
        if ($productCode != "") {
            $listMarketingProducts = MarketingProduct::where("code", "like", '%' . $productCode . '%')->get();
            $productCodeList = [];
            array_push($productCodeList, $productCode);
            foreach ($listMarketingProducts as $marketingProduct) {
                array_push($productCodeList, $marketingProduct->product_code);
            }
            $query->whereIn('code', $productCodeList);
        }


        $perPage = config('settings.per_page');
        return $query->paginate($perPage);
    }

    public static function searchProductCode($productCode)
    {
        $listProductCode = [];
        $listProducts = Product::where("code", 'like', '%' . $productCode . '%')->limit(5)->get();
        foreach ($listProducts as $product) {
            array_push($listProductCode, $product->code);
        }
        if (count($listProducts) < 5) {
            $listMarketingProducts = MarketingProduct::where("code", "like", '%' . $productCode . '%')->limit(5 - count($listProducts))->get();
            foreach ($listMarketingProducts as $marketingProduct) {
                array_push($listProductCode, $marketingProduct->code);
            }

        }
        return $listProductCode;
    }

    public static function findDiscounts($discountCode)
    {
        $filterOptions = [];
        if ($discountCode != "") {
            $filterOptions[] = ["code", "like", "%" . $discountCode . "%"];
        }
        $perPage = config('settings.per_page');
        return Discount::where($filterOptions)->paginate($perPage);
    }

    public static function findOrderFailReasons()
    {
        $filterOptions = [];
        $perPage = config('settings.per_page');
        return OrderFailReason::where($filterOptions)->paginate($perPage);
    }

    public static function getOrderFailedReason($id)
    {
        return OrderFailReason::where("id", $id)->first();
    }

    public static function saveOrderFailReason($orderFailReasonInfo)
    {
        try {

            $orderFailReason = OrderFailReason::where("id", $orderFailReasonInfo->id)->first();
            if ($orderFailReason == null) {
                $orderFailReason = new OrderFailReason();

            }
            $orderFailReason->cause = $orderFailReasonInfo->cause;
            $orderFailReason->note = $orderFailReasonInfo->note;

            if ($orderFailReason->save()) {
                return ResultCode::SUCCESS;
            }
            return ResultCode::FAILED_UNKNOWN;
        } catch (\Exception $e) {
            Log::log("error message ", $e->getMessage());
            return ResultCode::FAILED_UNKNOWN;
        }
    }

    public static function deleteOrderFailReason($id)
    {
        $filterOptions = [];
        $filterOptions["id"] = $id;
        $orderFailReason = OrderFailReason::where($filterOptions)->first();
        if ($orderFailReason != null) {
            if (OrderFailReason::where($filterOptions)->delete()) {
                return ResultCode::SUCCESS;
            }
        }
        return ResultCode::FAILED_UNKNOWN;
    }

    private static function attachExtraCustomerProperty($customer)
    {
        if ($customer->birthday != null) {
            $customer->birthday_str = Util::formatDate($customer->birthday);
        } else {
            $customer->birthday_str = "";
        }
        $customer->state_name = CustomerState::getName($customer->customer_state);
        $landingPage = LandingPage::where("id", $customer->landing_page_id)->first();
        if ($landingPage != null) {
            $customer->landing_page_name = $landingPage->name;
        } else {
            $customer->landing_page_name = "____";
        }
        $customer->customer_state_name = CustomerState::getName($customer->customer_state);
        $customer->is_public_phone_number = $customer->public_phone_number;
        $query = DB::table("streets");
        $query->select("streets.name as street_name", "provinces.name as province_name", "districts.name as district_name");
        $query->join("districts", "districts.id", "=", "streets.district_id");
        $query->join("provinces", "provinces.id", "=", "districts.province_id");

        $query->where("streets.id", $customer->street_id);
        $street = $query->first();
        $customer->province_name = "";
        $customer->district_name = "";
        $customer->street_name = "";
        if ($street != null) {
            $customer->province_name = $street->province_name;
            $customer->district_name = $street->district_name;
            $customer->street_name = $street->street_name;
        }
    }

    public static function findCustomers($user, $searchPhoneNumber = "")
    {
        $filterOptions = [];
        $filterOptions['user_id'] = $user->id;
        if ($searchPhoneNumber != "") {
            $filterOptions[] = ["phone_number", "like", "%" . $searchPhoneNumber . "%"];
        }
        $perPage = config('settings.per_page');
        $listCustomers = Customer::where($filterOptions)->paginate($perPage);
        foreach ($listCustomers as $customer) {
            self::attachExtraCustomerProperty($customer);

        }
        return $listCustomers;
    }


    public static function listCustomerState()
    {
        $listCustomerState = [];
        foreach (CustomerState::listIds() as $stateId) {
            $customerState = new \stdClass();
            $customerState->id = $stateId;
            $customerState->name = CustomerState::getName($stateId);
            array_push($listCustomerState, $customerState);
        }
        return $listCustomerState;
    }

    public static function listLandingPages()
    {
        return LandingPage::all();
    }

    public static function saveCustomer($user, $customerInfo)
    {
        try {
            $customer = Customer::where("id", $customerInfo->id)->first();

            if ($customer == null) {
                $customer = new Customer();
                $customer->created = Util::now();
                $customer->user_id = $user->id;
                $customer->code = "";
            } else {
                if ($customer->user_id != $user->id) {
                    return ResultCode::FAILED_PERMISSION_DENY;
                }
            }

            $customer->name = $customerInfo->name;
            $customer->address = $customerInfo->address;
            $customer->customer_state = $customerInfo->state_id;
            $customer->landing_page_id = $customerInfo->landing_page_id;
            $customer->phone_number = $customerInfo->phone_number;
            $customer->birthday = $customerInfo->birthday;

            $customer->public_phone_number = $customerInfo->is_public_phone_number;
            $customer->street_id = $customerInfo->street_id;
            if ($customer->save()) {
                if ($customer->code == "") {
                    $customer->code = 'MKH_' . Util::formatLeadingZeros($customer->id, 4);
                    if ($customer->save()) {
                        return ResultCode::SUCCESS;
                    }
                } else {
                    return ResultCode::SUCCESS;
                }

            }

            return ResultCode::FAILED_UNKNOWN;
        } catch (\Exception $e) {
            Log::log("error message ", $e->getMessage());
            return ResultCode::FAILED_UNKNOWN;
        }
    }

    public static function getCustomer($id)
    {
        $customer = Customer::where("id", $id)->first();
        if ($customer != null) {
            self::attachExtraCustomerProperty($customer);
        }
        return $customer;
    }

    public static function deleteCustomer($user, $id)
    {
        $customer = Customer::where("id", $id)->first();
        if ($customer != null) {
            if ($customer->user_id != $user->id) {
                return ResultCode::FAILED_PERMISSION_DENY;
            }
            if (Customer::where("id", $id)->delete()) {
                return ResultCode::SUCCESS;
            }
        }
        return ResultCode::FAILED_UNKNOWN;
    }

    private static function attachExtraDetailOrderProperty($detailOrder)
    {
        $detailOrder->marketing_product_code = "";
        $detailOrder->product_size = "";
        $detailOrder->marketing_product_code = "";
        $detailOrder->discount_name = "";
        $detailOrder->product_color = "";
        $detailOrder->price_str = "";
        $detailOrder->pick_money_str = "";
        $detailOrder->actually_collected_str = "";
        if ($detailOrder->marketing_product_id != null) {
            $marketingProduct = MarketingProduct::where("id", $detailOrder->marketing_product_id)->first();
            if ($marketingProduct != null) {
                $detailOrder->marketing_product_code = $marketingProduct->code;
            }
        }
        if ($detailOrder->discount_id != null) {
            $discount = Discount::where("id", $detailOrder->discount_id)->first();
            if ($discount != null) {
                $detailOrder->discount_name = $discount->name;
            }
        }


        $productCat = ProductCategory::where("id", $detailOrder->product_category_id)->first();
        $detailProduct = DetailProduct::where("id", $detailOrder->detail_product_id)->first();
        $product = null;
        if ($detailProduct != null) {
            $product = Product::where("code", $detailProduct->product_code)->first();
        }
        if ($productCat != null) {
            $detailOrder->product_size = $productCat->size;
            $detailOrder->product_color = $productCat->color;
        }
        if ($product != null) {
            if ($detailOrder->marketing_product_code == "") {
                $detailOrder->marketing_product_code = $product->code;
            }
            $detailOrder->product_code = $product->code;
            $detailOrder->price_str = Util::formatMoney($product->price);
        }

        $detailOrder->pick_money_str = Util::formatMoney($detailOrder->pick_money);
        $detailOrder->actually_collected_str = Util::formatMoney($detailOrder->actually_collected);

    }

    private static function attachExtraOrderProperty($order)
    {
        $order->sale_name = "";
        $order->customer_phone = "";
        $order->customer_code = "";
        $order->customer_name = "";
        $order->order_fail_cause = "";
        $order->replace_order_code = "";
        $order->delivery_time_str = "";
        $user = User::where("id", $order->user_id)->first();
        $customer = Customer::where("id", $order->customer_id)->first();
        if ($user != null) {
            $order->sale_name = $user->username;
        }
        if ($customer != null) {
            $order->customer_phone = $customer->phone_number;
            $order->customer_code = $customer->code;
            $order->customer_name = $customer->name;
        }
        if ($order->order_fail_reason_id != null) {
            $orderFailReason = OrderFailReason::where("id", $order->order_fail_reason_id)->first();
            if ($orderFailReason != null) {
                $order->order_fail_cause = $orderFailReason->cause;
            }
        }
        if ($order->replace_order_id != null) {
            $replaceOrder = Order::where("id", $order->replace_order_id)->first();
            if ($replaceOrder != null) {
                $order->replace_order_code = $replaceOrder->code;
            }
        }
        if ($order->delivery_time != null) {
            $order->delivery_time_str = Util::formatDateTime($order->delivery_time);
        }
        $order->created_str = Util::formatDateTime($order->created);

        $order->order_state_name = OrderState::getName($order->order_state);

    }

    public static function findOrders($listUserIds, $startTime = null, $endTime = null, $orderStateId = -1)
    {
        $filterOptions = [];
        if ($orderStateId != -1) {
            $filterOptions['order_state'] = $orderStateId;
        }
        if ($startTime != null && $endTime != null) {
            $filterOptions[] = ["created", ">=", $startTime];
            $filterOptions[] = ["created", "<=", $endTime];
        }

        $perPage = config('settings.per_page');
        $orders = Order::where($filterOptions)->whereIn("user_id", $listUserIds)->paginate($perPage);

        foreach ($orders as $order) {
            self::attachExtraOrderProperty($order);
        }

        return $orders;
    }

    public static function findAllSales()
    {
        return User::where("department", User::$DEPARTMENT_SALE)->where("is_active", true)->get();
    }

    public static function getListOrderStates()
    {
        $listOrderState = [];
        foreach (OrderState::listIds() as $stateId) {
            $orderState = new \stdClass();
            $orderState->id = $stateId;
            $orderState->name = OrderState::getName($stateId);
            array_push($listOrderState, $orderState);
        }
        return $listOrderState;
    }

    public static function getListFailReasons()
    {
        return OrderFailReason::all();
    }

    public static function listSuggestionProductCodes()
    {
        $productCodeList = [];
        $listProduct = Product::all();
        foreach ($listProduct as $product) {
            if (!in_array($product->code, $productCodeList)) {
                array_push($productCodeList, $product->code);
            }

        }
        $listMarketingProduct = MarketingProduct::all();
        foreach ($listMarketingProduct as $marketingProduct) {
            if (!in_array($marketingProduct->code, $productCodeList)) {
                array_push($productCodeList, $marketingProduct->code);
            }
        }
        return $productCodeList;
    }

    public static function getOrder($id)
    {
        $order = Order::where("id", $id)->first();
        if ($order != null) {
            self::attachExtraOrderProperty($order);
            $listDetailOrders = DetailOrder::where("order_id", $order->id)->get();
            foreach ($listDetailOrders as $detailOrder) {
                self::attachExtraDetailOrderProperty($detailOrder);
            }
            $order->list_detail_orders = $listDetailOrders;
        }
        return $order;
    }

    private static function saveOnlyOrder($user, $orderInfo)
    {
        $isInsertAction = false;
        $order = Order::where("id", $orderInfo->id)->first();
        $customer = null;
        if ($order == null) {
            $order = new Order();
            $order->user_id = $user->id;
            $order->created = Util::now();
            $order->code = "";
            $isInsertAction = true;
            $customer = Customer::where("code", $orderInfo->customer_code)->first();
        } else {
            $customer = Customer::where("id", $order->customer_id)->first();
        }
        if ($customer == null) {
            return ResultCode::FAILED_SAVE_ORDER_CUSTOMER_NOT_FOUND;
        }
        if (OrderState::getName($orderInfo->order_state_id) == "") {
            return ResultCode::FAILED_SAVE_ORDER_UNKNOWN_ORDER_STATE;
        }
        $replaceOrder = Order::where("code", $orderInfo->replace_order_code)->first();
        if ($replaceOrder != null) {
            $order->replace_order_id = $replaceOrder->id;
        } else {
            $order->replace_order_id = null;
        }
        if ($orderInfo->note == null) {
            $orderInfo->note = "";
        }
        $order->is_test = $orderInfo->is_test;
        $order->note = $orderInfo->note;
        $order->delivery_time = $orderInfo->delivery_time;
        $order->customer_id = $customer->id;
        $order->order_fail_reason_id = $orderInfo->order_fail_reason_id;
        $order->order_state = $orderInfo->order_state_id;
        if (!$order->save()) {
            return ResultCode::FAILED_UNKNOWN;
        }
        if ($order->code == "") {
            $order->code = "MHD_" . Util::formatLeadingZeros($order->id, 4);
            if (!$order->save()) {
                return ResultCode::FAILED_UNKNOWN;
            }
        }

        $historyOrder = new HistoryOrder();
        $historyOrder->user_id = $order->user_id;
        $historyOrder->created = Util::now();
        $historyOrder->order_info = $order->encode();

        if ($isInsertAction) {
            $historyOrder->action = ActionCode::INSERT;
        } else {
            $historyOrder->action = ActionCode::UPDATE;
        }
        if (!$historyOrder->save()) {
            return null;
        }
        $orderInfo->id = $order->id;
        return ResultCode::SUCCESS;


    }

    private static function addDetailOrder($user, $detailOrderInfo, $order)
    {
        $detailOrder = new DetailOrder();
        $marketingProduct = MarketingProduct::where("code", $detailOrderInfo->marketing_product_code)->first();
        $productCode = $detailOrderInfo->marketing_product_code;
        if ($marketingProduct != null) {
            $productCode = $marketingProduct->product_code;
        }

        $product = Product::where("code", $productCode)->first();
        if ($marketingProduct == null && $product == null) {
            return ResultCode::FAILED_SAVE_DETAIL_ORDER_NOT_FOUND_PRODUCT;
        }
        $productCat = ProductCategory::get($detailOrderInfo->product_size, $detailOrderInfo->product_color);
        $detailProduct = DetailProduct::where("product_code", $product->code)->where("product_category_id", $productCat->id)->first();

        if (CommonFunctions::getRemainingQuantity($detailProduct->id) < $detailOrderInfo->quantity) {
            return ResultCode::FAILED_SAVE_DETAIL_ORDER_OUT_OF_PRODUCT;
        }

        $detailOrder->order_id = $order->id;
        $detailOrder->product_category_id = $productCat->id;
        if ($marketingProduct == null) {
            $detailOrder->marketing_product_id = null;
        } else {
            $detailOrder->marketing_product_id = $marketingProduct->id;
        }

        $detailOrder->detail_product_id = $detailProduct->id;
        $detailOrder->quantity = $detailOrderInfo->quantity;
        $detailOrder->actually_collected = $detailOrderInfo->actually_collected;
        $detailOrder->pick_money = $detailOrderInfo->pick_money;
        $detailOrder->kg = 0.5;
        $detailOrder->discount_id = $detailOrderInfo->discount_id;

        if (!$detailOrder->save()) {
            return ResultCode::FAILED_UNKNOWN;
        }
        $inventory = Inventory::getOrNew($detailProduct->id);
        $inventory->exporting_quantity += $detailOrder->quantity;
        if (!$inventory->save()) {
            return ResultCode::FAILED_UNKNOWN;
        }
        $historyExportingProduct = new HistoryExportingProduct();
        $historyExportingProduct->user_id = $user->id;
        $historyExportingProduct->action = ActionCode::INSERT;
        $historyExportingProduct->created = Util::now();
        $historyExportingProduct->exporting_product = $detailOrder->encode();
        if (!$historyExportingProduct->save()) {
            return ResultCode::FAILED_UNKNOWN;
        }
        return ResultCode::SUCCESS;
    }

    private static function deleteDetailOrder($user, $detailOrder)
    {
        if (!DetailOrder::where("id", $detailOrder->id)->delete()) {
            return ResultCode::FAILED_UNKNOWN;
        }
        $detailProduct = DetailProduct::where("id", $detailOrder->detail_product_id)->first();
        $inventory = Inventory::getOrNew($detailProduct->id);
        $inventory->exporting_quantity -= $detailOrder->quantity;
        if (!$inventory->save()) {
            return ResultCode::FAILED_UNKNOWN;
        }
        $historyExportingProduct = new HistoryExportingProduct();
        $historyExportingProduct->user_id = $user->id;
        $historyExportingProduct->action = ActionCode::DELETE;
        $historyExportingProduct->created = Util::now();
        $historyExportingProduct->exporting_product = $detailOrder->encode();
        if (!$historyExportingProduct->save()) {
            return ResultCode::FAILED_UNKNOWN;
        }


        return ResultCode::SUCCESS;
    }


    public static function addOrder($user, $orderInfo)
    {
        $resultCode = ResultCode::FAILED_UNKNOWN;
        DB::beginTransaction();
        try {
            $orderInfo->id = null;
            $resultCode = self::saveOnlyOrder($user, $orderInfo);
            if ($resultCode == ResultCode::SUCCESS) {

                $order = Order::where("id", $orderInfo->id)->first();

                foreach ($orderInfo->detail_orders as $detailOrder) {
                    $resultCode = self::addDetailOrder($user, $detailOrder, $order);
                    if ($resultCode != ResultCode::SUCCESS) {
                        throw new \Exception("save detail order failed");
                    }
                }
                DB::commit();
                return ResultCode::SUCCESS;
            }


        } catch (\Exception $e) {
            Log::log("error message ", $e->getMessage());
            DB::rollBack();
            return ResultCode::FAILED_UNKNOWN;
        }
        DB::rollBack();
        return $resultCode;
    }

    public static function updateOrder($user, $orderInfo)
    {
        $resultCode = ResultCode::FAILED_UNKNOWN;
        DB::beginTransaction();
        try {
            $order = Order::where("id", $orderInfo->id)->first();
            if ($order != null) {
                if ($order->user_id == $user->id || $user->isLeader()) {
                    $resultCode = self::saveOnlyOrder($user, $orderInfo);
                    if ($resultCode == ResultCode::SUCCESS) {
                        DB::commit();
                        return ResultCode::SUCCESS;
                    }
                }
            }
        } catch (\Exception $e) {
            Log::log("error message ", $e->getMessage());
            DB::rollBack();
            return ResultCode::FAILED_UNKNOWN;
        }
        DB::rollBack();
        return $resultCode;
    }

    public static function deleteOrder($user, $orderId)
    {
        $resultCode = ResultCode::FAILED_UNKNOWN;
        DB::beginTransaction();
        try {
            $order = Order::where("id", $orderId)->first();
            if ($order != null) {
                $listDetailOrders = DetailOrder::where("order_id", $order->id)->get();
                foreach ($listDetailOrders as $detailOrder) {
                    $resultCode = self::deleteDetailOrder($user, $detailOrder);
                    if ($resultCode != ResultCode::SUCCESS) {
                        throw new \Exception("delete detail order failed");
                    }
                }
                $historyOrder = new HistoryOrder();
                $historyOrder->user_id = $order->user_id;
                $historyOrder->created = Util::now();
                $historyOrder->order_info = $order->encode();
                $historyOrder->action = ActionCode::DELETE;
                if ($historyOrder->save()) {
                    if ($order->delete()) {
                        $resultCode = ResultCode::SUCCESS;
                        DB::commit();
                    }

                }


            }

        } catch (\Exception $e) {
            Log::log("error message ", $e->getMessage());
            DB::rollBack();
            return ResultCode::FAILED_UNKNOWN;
        }
        DB::rollBack();
        return $resultCode;
    }


    public static function getPrice($marketingProductCode)
    {

        $productCode = $marketingProductCode;
        $marketingProduct = MarketingProduct::where("code", $marketingProductCode)->first();
        if ($marketingProduct != null) {
            $productCode = $marketingProduct->product_code;
        }
        $product = Product::where("code", $productCode)->first();
        if ($product != null) {
            return $product->price;
        }
        return -1;


    }

    public static function availableQuantity($marketingProductCode, $productSize, $productColor, $quantity)
    {
        $productCode = $marketingProductCode;
        $marketingProduct = MarketingProduct::where("code", $marketingProductCode)->first();
        if ($marketingProduct != null) {
            $productCode = $marketingProduct->product_code;
        }
        $product = Product::where("code", $productCode)->first();
        $productCat = ProductCategory::get($productSize, $productColor);
        if ($product != null && $productCat != null) {
            $detailProduct = DetailProduct::where("product_code", $product->code)->where("product_category_id", $productCat->id)->first();

            if ($detailProduct != null) {
                if (CommonFunctions::getRemainingQuantity($detailProduct->id) >= $quantity) {
                    return true;
                }
            }
        }
        return false;
    }
    public static function listOrderHistories($startTime=null, $endTime=null){
        $filterOptions = [];
        if($startTime != null && $endTime != null){
            $filterOptions[] = ['created', ">=", $startTime];
            $filterOptions[] = ['created', "<=", $endTime];
        }
        $perPage = config('settings.per_page');
        $listHistories = HistoryOrder::where($filterOptions)->paginate($perPage);
        foreach ($listHistories as $history){
            $user = User::where("id", $history->user_id)->first();
            if($user != null){
                $history->username = $user->username;
            }
            $history->action = ActionCode::getName($history->action);
            $history->created_str = Util::formatDateTime($history->created);
            $order = json_decode($history->order_info);
            try {
                $history->order_state_name = $order->order_state;
                $history->note = $order->note;
                $history->code = $order->code;
                $history->sale_name = $order->sale_name;
            }catch (\Exception $e){
                Log::log("error message", $e->getMessage());
            }
        }
        return $listHistories;
    }
}

<?php


namespace App\models\functions;


use App\models\Config;
use App\models\Customer;
use App\models\CustomerSource;
use App\models\CustomerState;
use App\models\DetailOrder;
use App\models\DetailProduct;
use App\models\Discount;
use App\models\District;
use App\models\HistoryExportingProduct;
use App\models\HistoryOrder;
use App\models\Inventory;
use App\models\LandingPage;
use App\models\MarketingProduct;
use App\models\MarketingSource;
use App\models\Order;
use App\models\OrderFailReason;
use App\models\OrderState;
use App\models\Product;
use App\models\ProductCategory;
use App\models\Province;
use App\models\Remind;
use App\models\Storage;
use App\models\Street;
use App\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use mysql_xdevapi\Exception;

class SaleFunctions
{

    public static function searchPhoneNumber($phoneNumber)
    {
        return Customer::where("phone_number", 'like', '%' . $phoneNumber . '%')->select('phone_number')->distinct()->limit(5)->get();
    }

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
        $query = Product::where("is_active", true)->where("is_test", false);
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
        $listProducts = Product::where("code", 'like', '%' . $productCode . '%')->where("is_active", true)->where("is_test", false)->limit(5)->get();
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
        $filterOptions = [
            "is_active" => true
        ];
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
        if ($customer->created != null) {
            $customer->created_str = Util::formatDate($customer->created);
        } else {
            $customer->created_str = "";
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

    public static function countCustomer($listUserIds, $searchPhoneNumber = "", $customerState = -1)
    {
        $filterOptions = [];
        if ($searchPhoneNumber != "") {
            $filterOptions[] = ["phone_number", "like", "%" . $searchPhoneNumber . "%"];
        }
        if ($customerState != -1) {
            $filterOptions[] = ["customer_state", $customerState];
        }
        return Customer::where($filterOptions)->whereIn("user_id", $listUserIds)->count();
    }

    public static function findCustomers($listUserIds, $searchPhoneNumber = "", $customerState = -1)
    {
        $filterOptions = [];
        if ($searchPhoneNumber != "") {
            $filterOptions[] = ["phone_number", "like", "%" . $searchPhoneNumber . "%"];
        }
        if ($customerState != -1) {
            $filterOptions[] = ["customer_state", $customerState];
        }
        $perPage = config('settings.per_page');
        $listCustomers = Customer::where($filterOptions)->whereIn("user_id", $listUserIds)->orderBy("created", "DESC")->paginate($perPage);
        foreach ($listCustomers as $customer) {
            self::attachExtraCustomerProperty($customer);
            $customer->customer_state_color = "";
            if ($customer->customer_state == CustomerState::STATE_CUSTOMER_WAITING_FOR_CONFIRMING_CUSTOMER) {
                $customer->customer_state_color = "#F7DC6F";
            }
            if ($customer->customer_state == CustomerState::STATE_CUSTOMER_CUSTOMER_AGREED) {
                $customer->customer_state_color = "#2ECC71";
            }

        }
        return $listCustomers;
    }


    public static function listCustomerState($exculde = [])
    {
        $listCustomerState = [];
        foreach (CustomerState::listIds() as $stateId) {
            if (!in_array($stateId, $exculde)) {
                $customerState = new \stdClass();
                $customerState->id = $stateId;
                $customerState->name = CustomerState::getName($stateId);
                array_push($listCustomerState, $customerState);
            }

        }
        return $listCustomerState;
    }

    public static function listLandingPages()
    {
        return LandingPage::all();
    }

    public static function saveCustomerSource($customerId, $listProductCodes, $created = null)
    {
        if ($created == null) {
            $created = Util::now();
        }
        $listOldProductCodes = [];
        $listCustomerSources = CustomerSource::where("customer_id", $customerId)->get();
        foreach ($listCustomerSources as $customerSources) {
            if ($customerSources->marketing_product_id == null) {
                array_push($listOldProductCodes, $customerSources->product_code);
            } else {
                array_push($listOldProductCodes, $customerSources->marketing_product_id);
            }
        }
        $listNewProductCodes = [];
        foreach ($listProductCodes as $code) {
            if (self::checkProductCodeForAddCustomer($code, $created) != ResultCode::SUCCESS) {
                return ResultCode::FAILED_UNKNOWN;
            }
            $marketingProduct = MarketingProduct::find($code, $created);
            if ($marketingProduct != null) {
                array_push($listNewProductCodes, $marketingProduct->id);
            } else {
                array_push($listNewProductCodes, $code);
            }
        }

        $changed = false;
        if (count($listNewProductCodes) != count($listOldProductCodes)) {
            $changed = true;
        } else {
            foreach ($listOldProductCodes as $code) {
                if (!in_array($code, $listNewProductCodes)) {
                    $changed = true;
                    break;
                }
            }
        }

        if ($changed) {

            CustomerSource::where("customer_id", $customerId)->delete();
            foreach ($listNewProductCodes as $code) {
                $customerSource = new CustomerSource();
                $customerSource->customer_id = $customerId;
                $customerSource->marketing_product_id = null;
                $customerSource->created = $created;

                $product = Product::getProduct($code);
                if ($product == null) {
                    $marketingProduct = MarketingProduct::get($code);
                    if ($marketingProduct != null) {
                        $customerSource->marketing_product_id = $marketingProduct->id;
                        $customerSource->product_code = $marketingProduct->product_code;
                    } else {
                        return ResultCode::FAILED_UNKNOWN;
                    }
                } else {
                    $customerSource->product_code = $product->code;
                }
                if (CustomerSource::exist($customerSource)) {
                    continue;
                }
                if (!$customerSource->save()) {
                    return ResultCode::FAILED_UNKNOWN;
                }
            }
        }
        return ResultCode::SUCCESS;
    }

    public static function saveCustomer($user, $customerInfo)
    {
        if ($customerInfo->state_id == CustomerState::STATE_CUSTOMER_ORDER_CREATED) {
            return ResultCode::UPDATE_FAILED_CUSTOMER_IN_STATE_ORDER_CREATED;
        }

        DB::beginTransaction();
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
                if (!Util::equalDate($customer->created, Util::now())) {
                    return ResultCode::UPDATE_FAILED_CUSTOMER_NOT_SAME_DATE;
                }

                if ($customer->customer_state == CustomerState::STATE_CUSTOMER_ORDER_CREATED) {

                    return ResultCode::UPDATE_FAILED_CUSTOMER_IN_STATE_ORDER_CREATED;
                }

            }
            $customer->name = $customerInfo->name;
            $customer->address = $customerInfo->address;
            $customer->customer_state = $customerInfo->state_id;
            $customer->landing_page_id = $customerInfo->landing_page_id;
            $customer->phone_number = $customerInfo->phone_number;
            $customer->birthday = $customerInfo->birthday;
            $customer->note = $customerInfo->note;

            $customer->public_phone_number = $customerInfo->is_public_phone_number;
            $customer->street_id = $customerInfo->street_id;
            $success = false;
            if ($customer->save()) {
                if ($customer->code == "") {
                    $customer->code = 'MKH_' . Util::formatLeadingZeros($customer->id, 4);
                    if ($customer->save()) {
                        $customerInfo->code = $customer->code;
                        $success = true;
                    }
                } else {
                    $customerInfo->code = $customer->code;
                    $success = true;

                }

            }
            if ($success) {
                $success = self::saveCustomerSource($customer->id, $customerInfo->listMarketingProducts) == ResultCode::SUCCESS;
            }

            if ($success) {

                DB::commit();
                return ResultCode::SUCCESS;
            }
            DB::rollBack();
            return ResultCode::FAILED_UNKNOWN;
        } catch (\Exception $e) {
            Log::log("error message ", $e->getMessage());
            DB::rollBack();
            return ResultCode::FAILED_UNKNOWN;
        }
    }

    public static function findCustomerByPhoneNumber($phoneNumber)
    {
        $customer = Customer::where("phone_number", $phoneNumber)->orderBy("created", "DESC")->first();
        if ($customer != null) {
            self::attachExtraCustomerProperty($customer);
        }
        return $customer;
    }


    public static function getCustomer($id)
    {
        try {
            $customer = Customer::where("id", $id)->first();
            if ($customer != null) {
                self::attachExtraCustomerProperty($customer);
                $listMarketingCode = CustomerSource::getProductCode($customer->id);
                $customer->list_marketing_code = $listMarketingCode;
            }

            return $customer;
        } catch (\Exception $e) {
            Log::log("error message", $e->getMessage());
        }
        return null;
    }

    public static function deleteCustomer($user, $id)
    {
        $customer = Customer::where("id", $id)->first();
        if ($customer != null) {
            if ($customer->user_id != $user->id) {
                return ResultCode::FAILED_PERMISSION_DENY;
            }
            DB::beginTransaction();
            try {
                if (Order::existCustomer($id)) {
                    throw new \Exception("customer is existed");
                }
                CustomerSource::where("customer_id", $id)->delete();
                if (Customer::where("id", $id)->delete()) {
                    DB::commit();
                    return ResultCode::SUCCESS;
                }
            } catch (\Exception $e) {
                DB::rollBack();
                Log::log("error message ", $e->getMessage());
                return ResultCode::FAILED_DELETE_CUSTOMER_EXISTED_IN_ORDER;
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
        $detailOrder->product_code = "";

        if ($detailOrder->marketing_product_id != null) {
            $marketingProduct = MarketingProduct::where("id", $detailOrder->marketing_product_id)->first();
            if ($marketingProduct != null) {

                $detailOrder->marketing_product_code = $marketingProduct->code;
                $detailOrder->product_code = $marketingProduct->product_code;
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
            $detailOrder->product_code = $detailProduct->product_code;
            $product = Product::where("code", $detailProduct->product_code)->first();
        }
        if ($productCat != null) {
            $detailOrder->product_size = $productCat->size;
            $detailOrder->product_color = $productCat->color;
        }
        if ($product != null) {

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
        $order->customer_landing_page_name = "";

        $order->product_name = "";
        $order->customer_province_name = "";
        $order->customer_district_name = "";
        $order->customer_street_name = "";
        $order->customer_address = "";

        $user = User::where("id", $order->user_id)->first();
        $customer = Customer::where("id", $order->customer_id)->first();
        if ($user != null) {
            $order->sale_name = $user->username;
        }
        if ($customer != null) {
            $order->customer_phone = $customer->phone_number;
            $order->customer_code = $customer->code;
            $order->customer_name = $customer->name;
            $order->customer_address = $customer->address;
            $landingPage = LandingPage::where("id", $customer->landing_page_id)->first();
            if ($landingPage != null) {
                $order->customer_landing_page_name = $landingPage->name;
            }

            $street = Street::where("id", $customer->street_id)->first();
            if ($street != null) {
                $order->customer_street_name = $street->name;
                $district = District::where("id", $street->district_id)->first();
                if ($district != null) {
                    $order->customer_district_name = $district->name;
                    $province = Province::where("id", $district->province_id)->first();
                    if ($province != null) {
                        $order->customer_province_name = $province->name;
                    }
                }
            }


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

    public static function countOrder($listUserIds, $startTime = null, $endTime = null, $orderStateId = -1, $filterOrderType = -1, $search_phone_number = "", $searchGHTKCode = "")
    {
        $filterOptions = [];
        if ($orderStateId != -1) {
            $filterOptions['order_state'] = $orderStateId;
        }
        if ($startTime != null && $endTime != null) {
            $filterOptions[] = ["created", ">=", $startTime];
            $filterOptions[] = ["created", "<=", $endTime];
        }
        if ($searchGHTKCode != "") {
            $filterOptions[] = ["ghtk_label", $searchGHTKCode];
        }

        if ($filterOrderType != -1) {
            if ($filterOrderType == 1) {
                $filterOptions['is_test'] = true;
            } else {
                $filterOptions['is_test'] = false;
            }
        }

        $query = Order::where($filterOptions)
            ->whereIn("orders.user_id", $listUserIds);
        if ($search_phone_number != '') {
            $query->join("customers", "orders.customer_id", "=", "customers.id")
                ->where("customers.phone_number", "like", "%" . $search_phone_number . "%");
        }
        return $query->count();
    }

    public static function findOrders($listUserIds, $startTime = null, $endTime = null, $orderStateId = -1, $filterOrderType = -1, $search_phone_number = "", $searchGHTKCode = "")
    {
        $filterOptions = [];
        if ($orderStateId != -1) {
            $filterOptions['order_state'] = $orderStateId;
        }
        if ($startTime != null && $endTime != null) {
            $filterOptions[] = ["created", ">=", $startTime];
            $filterOptions[] = ["created", "<=", $endTime];
        }
        if ($filterOrderType != -1) {
            if ($filterOrderType == 1) {
                $filterOptions['is_test'] = true;
            } else {
                $filterOptions['is_test'] = false;
            }
        }
        if ($searchGHTKCode != "") {
            $filterOptions[] = ["ghtk_label", $searchGHTKCode];
        }

        $perPage = config('settings.per_page');

        $query = Order::where($filterOptions)
            ->whereIn("orders.user_id", $listUserIds)
            ->orderBy('orders.created', 'DESC');
        if ($search_phone_number != '') {
            $query->join("customers", "orders.customer_id", "=", "customers.id")
                ->where("customers.phone_number", "like", "%" . $search_phone_number . "%");
        }
        $query->select("orders.*");
        $orders = $query->paginate($perPage);

        foreach ($orders as $order) {
            self::attachExtraOrderProperty($order);
            $order->list_order_codes = "";
            $listDetailOrders = DetailOrder::where("order_id", $order->id)->get();
            if ($listDetailOrders != null) {
                foreach ($listDetailOrders as $detailOrder) {
                    $detailProduct = DetailProduct::where("id", $detailOrder->detail_product_id)->first();
                    if ($detailProduct != null) {
                        $productCat = ProductCategory::where("id", $detailProduct->product_category_id)->first();
                        $order->list_order_codes = $order->list_order_codes . '<br>' . $detailProduct->product_code . '-' . $productCat->color . '-' . $productCat->size;
                    } else {
                        $order->list_order_codes = "";
                    }
                }
            }
            if ($order->list_order_codes != "") {
                $order->list_order_codes = substr($order->list_order_codes, 4);
            }
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
        $listProduct = Product::where("is_active", true)->get();
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
            $order->storage_address = Storage::get($order->storage_id)->name;
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
            $order->order_state_updated = Util::now();
            $order->code = "";

            $isInsertAction = true;
            $customer = Customer::where("code", $orderInfo->customer_code)->first();
        } else {
            $customer = Customer::where("id", $order->customer_id)->first();
            if ($order->order_state != $orderInfo->order_state_id) {
                $order->order_state_updated = Util::now();
            }
        }
        $order->is_test = $orderInfo->is_test;
        $order->storage_id = $orderInfo->storage_id;
        if ($customer == null) {
            return ResultCode::FAILED_SAVE_ORDER_CUSTOMER_NOT_FOUND;
        } else {
            if ($order->is_test) {
                if ($customer->customer_state != CustomerState::STATE_CUSTOMER_WAITING_FOR_PRODUCT_AVAILABLE) {
                    $customer->customer_state = CustomerState::STATE_CUSTOMER_WAITING_FOR_PRODUCT_AVAILABLE;
                    if (!$customer->save()) {
                        return ResultCode::FAILED_UNKNOWN;
                    }
                }
            } else {
                if ($customer->customer_state != CustomerState::STATE_CUSTOMER_ORDER_CREATED) {
                    $customer->customer_state = CustomerState::STATE_CUSTOMER_ORDER_CREATED;
                    if (!$customer->save()) {
                        return ResultCode::FAILED_UNKNOWN;
                    }
                }
            }
        }

        if (OrderState::getName($orderInfo->order_state_id) == "") {
            return ResultCode::FAILED_SAVE_ORDER_UNKNOWN_ORDER_STATE;
        }

        $order->replace_order_id = null;

        if ($orderInfo->replace_order_code != null && $orderInfo->replace_order_code != "") {
            $replaceOrder = Order::where("ghtk_label", $orderInfo->replace_order_code)->first();
            if ($replaceOrder != null) {
                if ($replaceOrder->order_state < OrderState::STATE_ORDER_CREATED) {
                    return ResultCode::FAILED_SAVE_ORDER_REPLACE_ORDER_LEAK_STATE;
                }
                $order->replace_order_id = $replaceOrder->id;
            } else {
                return ResultCode::FAILED_SAVE_ORDER_NOT_FOUND_REPLACE_ORDER;
            }
        }


        if ($orderInfo->note == null) {
            $orderInfo->note = "";
        }

        $order->note = $orderInfo->note;
        $order->delivery_time = $orderInfo->delivery_time;
        $order->customer_id = $customer->id;
        $order->order_fail_reason_id = $orderInfo->order_fail_reason_id;

        if ($order->id == null) {
            $sumActuallyCollected = 0;
            foreach ($orderInfo->detail_orders as $detailOrder) {
                $sumActuallyCollected += $detailOrder->actually_collected;
            }
            $order->sum_actually_collected = $sumActuallyCollected;
        }


        if ($orderInfo->order_state_id != null) {
            $order->order_state = $orderInfo->order_state_id;
        }
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
        $marketingProduct = MarketingProduct::find($detailOrderInfo->marketing_product_code, Util::now());
        if ($marketingProduct == null) {
            $productCode = $detailOrderInfo->marketing_product_code;
        } else {
            $productCode = $marketingProduct->product_code;
        }

        $product = Product::where("code", $productCode)->first();


        if ($marketingProduct == null && $product == null) {
            return ResultCode::FAILED_SAVE_DETAIL_ORDER_NOT_FOUND_PRODUCT;
        }
        $productCodeIsValid = false;
        $listProductCodes = CustomerSource::getProductCode($order->customer_id);
        if ($marketingProduct != null) {
            if (in_array($marketingProduct->code, $listProductCodes)) {
                $productCodeIsValid = true;
            }
        }
        if (!$productCodeIsValid && $product != null) {
            if (in_array($product->code, $listProductCodes)) {
                $productCodeIsValid = true;
            }
        }

        if (!$productCodeIsValid) {
            return ResultCode::FAILED_SAVE_ORDER_NOT_FOUND_PRODUCT_CODE_IN_CUSTOMER_SOURCE;
        }

        if (!$order->is_test) {

            $productCat = ProductCategory::get($detailOrderInfo->product_size, $detailOrderInfo->product_color);
            if ($productCat == null) {
                return ResultCode::FAILED_SAVE_DETAIL_ORDER_OUT_OF_PRODUCT;
            }
            $detailProduct = DetailProduct::where("product_code", $product->code)->where("product_category_id", $productCat->id)->first();
            if ($detailProduct == null) {
                return ResultCode::FAILED_SAVE_DETAIL_ORDER_OUT_OF_PRODUCT;
            }
            if (CommonFunctions::getRemainingQuantity($detailProduct->id) < $detailOrderInfo->quantity) {
                return ResultCode::FAILED_SAVE_DETAIL_ORDER_OUT_OF_PRODUCT;
            }
            $detailOrder->product_category_id = $productCat->id;
            $detailOrder->detail_product_id = $detailProduct->id;

        } else {
            $detailOrder->product_category_id = ProductCategory::getOrNew($detailOrderInfo->product_size, $detailOrderInfo->product_color)->id;
            $detailProduct = DetailProduct::where("product_code", $product->code)->where("product_category_id", $detailOrder->product_category_id)->first();;

            if ($detailProduct != null) {
                $detailOrder->detail_product_id = $detailProduct->id;
            } else {
                $detailOrder->detail_product_id = null;
            }

        }

        $detailOrder->order_id = $order->id;

        if ($marketingProduct == null) {
            $detailOrder->marketing_product_id = null;
        } else {
            $detailOrder->marketing_product_id = $marketingProduct->id;
        }


        $detailOrder->quantity = $detailOrderInfo->quantity;
        $detailOrder->actually_collected = $detailOrderInfo->actually_collected;
        $detailOrder->pick_money = $detailOrderInfo->pick_money;
        $detailOrder->kg = 0.5;
        $detailOrder->discount_id = $detailOrderInfo->discount_id;

        if (!$detailOrder->save()) {
            return ResultCode::FAILED_UNKNOWN;
        }

        return self::updateInventoryForAddingDetailProduct($user, $detailOrder, $order->is_test);
    }

    private static function updateInventoryForAddingDetailProduct($user, $detailOrder, $isOrderTest)
    {
        if (!$isOrderTest) {
            $inventory = Inventory::getOrNew($detailOrder->detail_product_id);
            $inventory->exporting_quantity += $detailOrder->quantity;
            if (!$inventory->save()) {
                return ResultCode::FAILED_UNKNOWN;
            }
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

    private static function updateInventoryForRemovingDetailProduct($user, $detailOrder, $isOrderTest)
    {
        if (!$isOrderTest) {
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

        }
        return ResultCode::SUCCESS;
    }

    private static function deleteDetailOrder($user, $order, $detailOrder)
    {
        if (!DetailOrder::where("id", $detailOrder->id)->delete()) {
            return ResultCode::FAILED_UNKNOWN;
        }
        return self::updateInventoryForRemovingDetailProduct($user, $detailOrder, $order->is_test);
    }

    public static function addOrder($user, $orderInfo)
    {
        $resultCode = ResultCode::FAILED_UNKNOWN;
        if (Customer::isDuplicate($orderInfo->customer_code)) {
            return ResultCode::FAILED_SAVE_ORDER_DUPLICATE_CUSTOMER;
        }


        DB::beginTransaction();
        try {
            $orderInfo->id = null;
            $resultCode = self::saveOnlyOrder($user, $orderInfo);
            if ($resultCode == ResultCode::SUCCESS) {

                $order = Order::where("id", $orderInfo->id)->first();

                foreach ($orderInfo->detail_orders as $detailOrder) {
                    $resultCode = self::addDetailOrder($user, $detailOrder, $order);
                    if ($resultCode != ResultCode::SUCCESS) {
                        throw new \Exception("save detail order failed " . $resultCode);
                    }
                }
                DB::commit();
                return ResultCode::SUCCESS;
            }


        } catch (\Exception $e) {
            Log::log("error message ", $e->getMessage());
            if ($resultCode == ResultCode::SUCCESS) {
                $resultCode = ResultCode::FAILED_UNKNOWN;
            }
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
                $orderTypeChanged = $order->is_test != $orderInfo->is_test;
                if ($orderTypeChanged) {
                    if ($order->order_state != OrderState::STATE_ORDER_PENDING) {
                        return ResultCode::FAILED_DELETE_ORDER_STATE_MORE_THAN_STATE_ORDER_CREATED;
                    }
                }
                if ($order->user_id == $user->id || $user->isLeader()) {
                    $resultCode = self::saveOnlyOrder($user, $orderInfo);
                    if ($resultCode == ResultCode::SUCCESS) {
                        if ($orderTypeChanged) {
                            $listDetailOrders = DetailOrder::where("order_id", $order->id)->get();
                            Log::log("taih", "sdnsjkndsnd");
                            foreach ($listDetailOrders as $detailOrder) {
                                if ($orderInfo->is_test) {
                                    if (self::updateInventoryForRemovingDetailProduct($user, $detailOrder, false) != ResultCode::SUCCESS) {
                                        return ResultCode::FAILED_UNKNOWN;
                                    }
                                } else {
                                    if (self::updateInventoryForAddingDetailProduct($user, $detailOrder, false) != ResultCode::SUCCESS) {
                                        return ResultCode::FAILED_UNKNOWN;
                                    }

                                }
                            }

                        }
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
                if ($order->order_state != OrderState::STATE_ORDER_PENDING) {
                    return ResultCode::FAILED_DELETE_ORDER_STATE_MORE_THAN_STATE_ORDER_CREATED;
                }
                $listDetailOrders = DetailOrder::where("order_id", $order->id)->get();
                foreach ($listDetailOrders as $detailOrder) {
                    $resultCode = self::deleteDetailOrder($user, $order, $detailOrder);
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
                        $customer = Customer::get($order->customer_id);
                        $customer->customer_state = CustomerState::STATE_CUSTOMER_CUSTOMER_DISAGREED;
                        if ($customer->save()) {
                            DB::commit();
                        }

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
        if ($product->is_test) {
            return true;
        }
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

    public static function listOrderHistories($startTime = null, $endTime = null)
    {
        $filterOptions = [];
        if ($startTime != null && $endTime != null) {
            $filterOptions[] = ['created', ">=", $startTime];
            $filterOptions[] = ['created', "<=", $endTime];
        }
        $perPage = config('settings.per_page');
        $listHistories = HistoryOrder::where($filterOptions)->paginate($perPage);
        foreach ($listHistories as $history) {
            $user = User::where("id", $history->user_id)->first();
            if ($user != null) {
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
            } catch (\Exception $e) {
                Log::log("error message", $e->getMessage());
            }
        }
        return $listHistories;
    }

    private static function attachExtraDeliverOrder($order)
    {
        $order->product_name = "";
        $order->total_quantity = "";
        $order->kg = "";
        $order->actually_collected = "";
        $order->pick_money = "";
        $listDetailOrders = DetailOrder::where("order_id", $order->id)->get();
        if ($listDetailOrders != null) {
            foreach ($listDetailOrders as $detailOrder) {
                $detailProduct = DetailProduct::where("id", $detailOrder->detail_product_id)->first();
                $productCat = ProductCategory::where("id", $detailProduct->product_category_id)->first();
                $order->product_name = $order->product_name . '<br>' . $detailProduct->product_code . '-' . $productCat->color . '-' . $productCat->size;
                $order->total_quantity = $order->total_quantity . '<br>' . strval($detailOrder->quantity);
                $order->kg = $order->kg . '<br>' . strval($detailOrder->kg);
                $order->actually_collected = $order->actually_collected . '<br>' . Util::formatMoney($detailOrder->actually_collected) . "&nbsp;&#8363;";
                $order->pick_money = $order->pick_money . '<br>' . Util::formatMoney($detailOrder->pick_money) . "&nbsp;&#8363;";
            }
        }
        if ($order->product_name != "") {
            $order->product_name = substr($order->product_name, 4);
        }
        if ($order->total_quantity != "") {
            $order->total_quantity = substr($order->total_quantity, 4);
        }
        if ($order->kg != "") {
            $order->kg = substr($order->kg, 4);
        }
        if ($order->actually_collected != "") {
            $order->actually_collected = substr($order->actually_collected, 4);
        }
        if ($order->pick_money != "") {
            $order->pick_money = substr($order->pick_money, 4);
        }

    }

    public static function listDeliverOrders()
    {
        $listOrders = Order::where("order_state", OrderState::STATE_ORDER_PENDING)
            ->where("is_test", false)
            ->where(function ($query) {
                $query->whereNull('delivery_time');
                $query->orWhere('delivery_time', '<=', Util::now());
            })->get();
        $result = [];
        foreach ($listOrders as $order) {
            self::attachExtraOrderProperty($order);
            self::attachExtraDeliverOrder($order);
            if (!LandingPage::isShopeeSource($order->customer_landing_page_name)) {
                array_push($result, $order);
            }
        }
        return $result;
    }

    public static function countOrderStateManager($listUserIds, $startTime = null, $endTime = null, $orderStateId = -1, $searchGHTKCode = "", $search_phone_number = "")
    {
        $filterOptions = [
            "is_test" => false
        ];
        if ($orderStateId != -1) {
            $filterOptions['order_state'] = $orderStateId;
        }
        if ($searchGHTKCode != '') {
            $filterOptions[] = ['orders.ghtk_label', "like", "%" . $searchGHTKCode . "%"];
        }
        if ($startTime != null && $endTime != null) {
            $filterOptions[] = ["orders.created", ">=", $startTime];
            $filterOptions[] = ["orders.created", "<=", $endTime];
        }
        $query = Order::where($filterOptions);
            //->whereIn("orders.user_id", $listUserIds);
        if ($search_phone_number != '') {
            $query->join("customers", "orders.customer_id", "=", "customers.id")
                ->where("customers.phone_number", "like", "%" . $search_phone_number . "%");
        }

        return $query->count();
    }
    public static function getListOrderStatesAndCountOrder($startTime = null, $endTime = null)
    {
        $listOrderState = [];
        foreach (OrderState::listIds() as $stateId) {
            $orderState = new \stdClass();
            $orderState->id = $stateId;
            $orderState->name = OrderState::getName($stateId);

            $filterOptions = [
                "is_test" => false
            ];
            $filterOptions['order_state'] = $stateId;
            if ($startTime != null && $endTime != null) {
                $filterOptions[] = ["orders.created", ">=", $startTime];
                $filterOptions[] = ["orders.created", "<=", $endTime];
            }

            $orderState->total_order = Order::where($filterOptions)->count();
            array_push($listOrderState, $orderState);
        }
        return $listOrderState;
    }

    public static function listOrderStateManager($listUserIds, $startTime = null, $endTime = null, $orderStateId = -1, $searchGHTKCode = "", $search_phone_number = "")
    {
        $perPage = config('settings.per_page');
        $filterOptions = [
            "is_test" => false
        ];
        if ($orderStateId != -1) {
            $filterOptions['order_state'] = $orderStateId;
        }
        if ($searchGHTKCode != '') {
            $filterOptions[] = ['orders.ghtk_label', "like", "%" . $searchGHTKCode . "%"];
        }
        if ($startTime != null && $endTime != null) {
            $filterOptions[] = ["orders.created", ">=", $startTime];
            $filterOptions[] = ["orders.created", "<=", $endTime];
            $perPage = 10e6;
        }


        $query = Order::where($filterOptions)
            //->whereIn("orders.user_id", $listUserIds)
            ->orderBy('orders.created', 'DESC');
        if ($search_phone_number != '') {
            $query->join("customers", "orders.customer_id", "=", "customers.id")
                ->where("customers.phone_number", "like", "%" . $search_phone_number . "%");
        }

        $listOrders = $query->paginate($perPage);
        foreach ($listOrders as $order) {
            self::attachExtraOrderProperty($order);
            self::attachExtraDeliverOrder($order);
        }
        return $listOrders;
    }

    public static function listOrderDelivering($listOrderIds)
    {
        $listOrders = Order::whereIn("id", $listOrderIds)->get();
        foreach ($listOrders as $order) {
            self::attachExtraOrderProperty($order);
            self::attachExtraDeliverOrder($order);
        }
        return $listOrders;
    }

    public static function filterOrderByIds($listOrderIds)
    {
        $listOrders = Order::whereIn("id", $listOrderIds)->get();
        foreach ($listOrders as $order) {
            self::attachExtraOrderProperty($order);
        }
        return $listOrders;
    }

    private static function cancelOrderFromGHTK($ghtkLabel)
    {
        $url = 'https://services.giaohangtietkiem.vn/services/shipment/cancel/' . $ghtkLabel;
        $ch = null;
        try {
            $config = Config::getOrNew();
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Token:' . $config->ghtk_token)
            );

            curl_setopt($ch, CURLOPT_POSTFIELDS, []);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            // Execute the POST request
            $result = curl_exec($ch);
            $result = json_decode($result, true);
            if ($result['success']) {
                return ResultCode::SUCCESS;
            }

        } catch (\Exception $e) {
            Log::log("error message", $e->getMessage());
        } finally {
            if ($ch != null) {
                curl_close($ch);
            }
        }
        return ResultCode::FAILED_UNKNOWN;
    }

    public static function pushOrderToGHTK($orderId)
    {
        $order = self::getOrder($orderId);
        $ghtkLabel = null;
        if ($order != null) {
            if ($order->is_test) {
                return ResultCode::FAILED_UNKNOWN;
            }
            if (LandingPage::isShopeeSource($order->customer_landing_page_name)) {
                return ResultCode::FAILED_UNKNOWN;
            }
            $sumPickMoney = 0;
            $sumActuallyCollected = 0;
            foreach ($order->list_detail_orders as $detailOrder) {
                $extraText = "";
                if ($detailOrder->quantity > 1) {
                    $extraText = " x" . $detailOrder->quantity;
                }

                $detailOrder->code_color_size_str = $detailOrder->product_code . '-' . $detailOrder->product_color . '-' . $detailOrder->product_size . $extraText;
                $sumPickMoney += $detailOrder->pick_money;
                $sumActuallyCollected += $detailOrder->actually_collected;
            }
            $order->sum_pick_money = $sumPickMoney;
            $order->sum_actually_collected = $sumActuallyCollected;
            if ($order->order_state = OrderState::STATE_ORDER_PENDING) {
                $url = 'https://services.giaohangtietkiem.vn/services/shipment/order';
                $ch = null;
                try {
                    $config = Config::getOrNew();
                    $listProducts = [];
                    foreach ($order->list_detail_orders as $detailOrder) {
                        $product = [
                            "name" => $detailOrder->code_color_size_str,
                            "weight" => $detailOrder->kg * $detailOrder->quantity,
                            "quantity" => $detailOrder->quantity
                        ];
                        array_push($listProducts, $product);
                    }
                    $storage = Storage::get($order->storage_id);
                    $pickAddress = $storage->address;
                    $time = Util::currentTime();
                    $pick_tel = $storage->phone;

                    $data = [
                        "products" => $listProducts,
                        "order" => [
                            "id" => "ms." . $time,
                            "pick_name" => $config->pick_name,
                            "pick_tel" => $pick_tel,
                            "pick_province" => $config->pick_province,
                            "pick_district" => $config->pick_district,
                            "pick_address" => $pickAddress,
                            "pick_money" => $order->sum_pick_money,
                            "tel" => $order->customer_phone,
                            "name" => $order->customer_name,
                            "address" => $order->customer_address,
                            "province" => $order->customer_province_name,
                            "district" => $order->customer_district_name,
                            "ward" => $order->customer_street_name,
                            "street" => $order->customer_street_name,
                            "hamlet" => "Khác",
                            "is_freeship" => "1",
                            "note" => $order->note,
                            "value" => $order->sum_actually_collected,
                            "transport" => $config->transport
                        ]
                    ];

                    $data = json_encode($data);

                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_POST, 1);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                            'Content-Type: application/json',
                            'Token:' . $config->ghtk_token)
                    );

                    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

                    // Execute the POST request
                    $result = curl_exec($ch);
                    Log::log("result", $result);
                    $result = json_decode($result, true);

                    if ($result['success']) {
                        $ghtkLabel = $result['order']['label'];
                        $order = Order::where("id", $orderId)->first();
                        $order->ghtk_label = $ghtkLabel;
                        $order->order_state = OrderState::STATE_ORDER_CREATED;
                        if ($order->save()) {
                            return ResultCode::SUCCESS;
                        } else {
                            Log::log("pushOrderToGHTK", "save order failed");
                            self::cancelOrderFromGHTK($ghtkLabel);
                        }
                    } else {
                        Log::log("pushOrderToGHTK",
                            " address " . $order->customer_address
                            . " province " . $order->customer_province_name
                            . " district " . $order->customer_district_name
                            . " ward " . $order->customer_street_name);
                        Log::log("pushOrderToGHTK", " failed " . $result['message']);
                    }

                } catch (\Exception $e) {
                    Log::log("error message", $e->getMessage());
                    if ($ghtkLabel != null) {
                        self::cancelOrderFromGHTK($ghtkLabel);
                    }
                } finally {
                    if ($ch != null) {
                        curl_close($ch);
                    }
                }
            }
        }
        return ResultCode::FAILED_UNKNOWN;
    }

    private static function mapOrderStateOfGHTK($ghtk_order_state)
    {
        switch ($ghtk_order_state) {
            case -1:
                return OrderState::STATE_ORDER_CANCEL;
            case 2:
                return OrderState::STATE_ORDER_CREATED;
            case 12:
            case 128:
            case 8:
                return OrderState::STATE_ORDER_TAKING;
            case 3:
            case 123:
                return OrderState::STATE_ORDER_TAKEN;
            case 127:
            case 7:
                return OrderState::STATE_ORDER_FAILED_TAKING;
            case 4:
            case 45:
            case 410:
            case 10:
                return OrderState::STATE_ORDER_DELIVERING;
            case 5:
                return OrderState::STATE_ORDER_DELIVERED;
            case 49:
            case 9:
                return OrderState::STATE_ORDER_FAILED_DELIVERING;
            case 20:
                return OrderState::STATE_ORDER_IS_RETURNING;
            case 21:
                return OrderState::STATE_ORDER_IS_RETURNED;
            case 6:
                return OrderState::STATE_PAYMENT_SUCCESSFUL;
            case 11:
                return OrderState::STATE_PAYMENT_SUCCESSFUL_2;
        }
        return -1;
    }

    public static function syncOrderState($user, $orderId)
    {
        $result = new \stdClass();
        $result->result_code = ResultCode::FAILED_UNKNOWN;
        $result->new_order_state = "";
        $result->is_change = false;
        $order = Order::where("id", $orderId)->first();
        if ($order != null) {
            if ($order->order_state != OrderState::STATE_ORDER_IS_RETURNED_AND_NO_BROKEN && $order->order_state != OrderState::STATE_ORDER_IS_RETURNED_AND_BROKEN) {
                if ($order->ghtk_label != null) {
                    $url = 'https://services.giaohangtietkiem.vn/services/shipment/v2/' . $order->ghtk_label;
                    $ch = null;
                    try {
                        $config = Config::getOrNew();
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $url);
                        curl_setopt($ch, CURLOPT_POST, 1);
                        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                                'Content-Type: application/json',
                                'Token:' . $config->ghtk_token)
                        );

                        curl_setopt($ch, CURLOPT_POSTFIELDS, []);
                        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

                        // Execute the POST request
                        $response = curl_exec($ch);
                        $response = json_decode($response, true);
                        if ($response['success']) {
                            $ghtk_order_state = Util::parseInt($response['order']['status']);
                            $order_state = self::mapOrderStateOfGHTK($ghtk_order_state);
                            Log::log("order_satte", $order_state);
                            if ($order_state != -1) {
                                if ($order->order_state != $order_state) {
                                    $result->is_change = true;
                                    $result->new_order_state = OrderState::getName($order_state);
                                    if (self::changeOrderState($user, $order, $order_state) == ResultCode::SUCCESS) {

                                        $result->result_code = ResultCode::SUCCESS;
                                    }
                                } else {
                                    $result->new_order_state = OrderState::getName($order_state);
                                    $result->result_code = ResultCode::SUCCESS;
                                }
                            }
                        }

                    } catch (\Exception $e) {
                        Log::log("error message", $e->getMessage());
                        $result->result_code = ResultCode::FAILED_UNKNOWN;
                    } finally {
                        if ($ch != null) {
                            curl_close($ch);
                        }
                    }
                } else {
                    $result->result_code = ResultCode::SUCCESS;
                    $result->new_order_state = OrderState::getName($order->order_state);
                    $result->is_change = false;
                }
            }

        }
        return $result;
    }

    private static function changeOrderState($user, $order, $newState)
    {
        if ($order->order_state == $newState) {
            return ResultCode::SUCCESS;
        }
        if ($order->is_test) {
            return ResultCode::FAILED_UNKNOWN;
        }
        switch ($newState) {
            case OrderState::STATE_ORDER_CANCEL:
                $order->order_state = OrderState::STATE_ORDER_CANCEL;
                if ($order->save()) {
                    $order = self::getOrder($order->id);
                    foreach ($order->list_detail_orders as $detailOrder) {
                        $importingProductInfo = new \stdClass();
                        $importingProductInfo->id = -1;
                        $importingProductInfo->size = $detailOrder->product_size;
                        $importingProductInfo->color = $detailOrder->product_color;
                        $importingProductInfo->product_code = $detailOrder->product_code;
                        $importingProductInfo->note = "sale cancel order automatically";
                        $importingProductInfo->quantity = $detailOrder->quantity;
                        if (StoreKeeperFunctions::saveImportingProduct($user, $importingProductInfo, false) != ResultCode::SUCCESS) {
                            return ResultCode::FAILED_UNKNOWN;
                        }
                    }
                    return ResultCode::SUCCESS;
                }
                break;
            case OrderState::STATE_ORDER_IS_RETURNED_AND_BROKEN:
                $order->order_state = OrderState::STATE_ORDER_IS_RETURNED_AND_BROKEN;
                if ($order->save()) {
                    $order = self::getOrder($order->id);
                    foreach ($order->list_detail_orders as $detailOrder) {
                        $importingProductInfo = new \stdClass();
                        $importingProductInfo->id = -1;
                        $importingProductInfo->size = $detailOrder->product_size;
                        $importingProductInfo->color = $detailOrder->product_color;
                        $importingProductInfo->product_code = $detailOrder->product_code;
                        $importingProductInfo->note = "sale cancel order automatically";
                        $importingProductInfo->quantity = $detailOrder->quantity;
                        if (StoreKeeperFunctions::saveFailedProduct($user, $importingProductInfo, false) != ResultCode::SUCCESS) {
                            return ResultCode::FAILED_UNKNOWN;
                        }
                    }
                    return ResultCode::SUCCESS;
                }
                break;
            case OrderState::STATE_ORDER_IS_RETURNED_AND_NO_BROKEN:
                $order->order_state = OrderState::STATE_ORDER_IS_RETURNED_AND_NO_BROKEN;
                if ($order->save()) {
                    $order = self::getOrder($order->id);
                    foreach ($order->list_detail_orders as $detailOrder) {
                        $importingProductInfo = new \stdClass();
                        $importingProductInfo->id = -1;
                        $importingProductInfo->size = $detailOrder->product_size;
                        $importingProductInfo->color = $detailOrder->product_color;
                        $importingProductInfo->product_code = $detailOrder->product_code;
                        $importingProductInfo->note = "sale cancel order automatically";
                        $importingProductInfo->quantity = $detailOrder->quantity;
                        if (StoreKeeperFunctions::saveReturningProduct($user, $importingProductInfo, false) != ResultCode::SUCCESS) {
                            return ResultCode::FAILED_UNKNOWN;
                        }
                    }
                    return ResultCode::SUCCESS;
                }
                break;
            default:
                $order->order_state = $newState;
                if ($order->save()) {
                    return ResultCode::SUCCESS;
                }
        }
        return ResultCode::FAILED_UNKNOWN;
    }

    public static function cancelOrder($user, $orderId)
    {

        $result = new \stdClass();
        $result->result_code = ResultCode::FAILED_UNKNOWN;
        $result->new_order_state = "";
        $result->is_change = false;
        $order = Order::where("id", $orderId)->first();
        if ($order != null) {
            if ($order->ghtk_label != null) {
                DB::beginTransaction();
                try {
                    if ($order->order_state != OrderState::STATE_ORDER_CANCEL) {
                        $result->result_code = self::changeOrderState($user, $order, OrderState::STATE_ORDER_CANCEL);
                        if ($result->result_code == ResultCode::SUCCESS) {
                            if (self::cancelOrderFromGHTK($order->ghtk_label)) {
                                $result->new_order_state = OrderState::getName(OrderState::STATE_ORDER_CANCEL);
                                $result->is_change = true;
                                DB::commit();
                                return $result;
                            } else {
                                $result->result_code = ResultCode::FAILED_UNKNOWN;
                            }
                        }
                    } else {
                        $result->result_code = ResultCode::SUCCESS;
                        $result->new_order_state = OrderState::getName($order->order_state);
                        $result->is_change = false;
                    }
                } catch (\Exception $e) {
                    $result->result_code = ResultCode::FAILED_UNKNOWN;
                }
                DB::rollBack();
            } else {
                $result->result_code = ResultCode::SUCCESS;
                $result->new_order_state = OrderState::getName($order->order_state);
                $result->is_change = false;
            }
        }

        return $result;
    }

    public static function orderStateManagerUpdateState($user, $orderId, $newOrderState)
    {
        $result = new \stdClass();
        $result->result_code = ResultCode::FAILED_UNKNOWN;
        $order = Order::where("id", $orderId)->first();
        if ($order != null &&
            in_array($newOrderState, [OrderState::STATE_ORDER_IS_RETURNED_AND_NO_BROKEN, OrderState::STATE_ORDER_IS_RETURNED_AND_BROKEN]) &&
            $order->order_state >= OrderState::STATE_ORDER_CREATED) {
            DB::beginTransaction();
            try {
                $result->result_code = self::changeOrderState($user, $order, $newOrderState);
                if ($result->result_code == ResultCode::SUCCESS) {

                    DB::commit();
                } else {
                    DB::rollBack();
                }
            } catch (\Exception $e) {
                DB::rollBack();
            }

        }
        return $result;
    }

    public static function reportOrder($userId, $fromDate = null, $toDate = null)
    {
        $query = DB::table("customer_sources");
        $query->select(DB::raw('DATE(customer_sources.created) as day'))
            ->groupBy("day")
            ->orderBy('day', 'DESC');
        if ($fromDate != null && $toDate != null) {
            $toDate = $toDate->modify('+1 day');
            $query->whereDate("customer_sources.created", ">=", $fromDate);
            $query->whereDate("customer_sources.created", "<", $toDate);
        }


        $perPage = config('settings.per_page');
        $result = $query->paginate($perPage);
        $listDate = [];
        foreach ($result as $date) {
            array_push($listDate, $date->day);
        }


        $queryData = DB::table("customer_sources")
            ->select(
                DB::raw('DATE(customer_sources.created) as day'),
                "products.code as product_code",
                "products.price as product_price",
                "orders.id as order_id")
            ->leftJoin("orders", function ($join) {
                $join->on('customer_sources.customer_id', '=', 'orders.customer_id')
                    ->where('orders.order_state', '!=', OrderState::STATE_ORDER_CANCEL);
            })
            ->whereNull('orders.replace_order_id')
            ->join("products", "customer_sources.product_code", "=", "products.code")
            //->groupBy('day', "products.code", "products.price")
            ->orderBy('day', 'DESC');
        if ($userId != -1) {
            $queryData->join("customers", "customer_sources.customer_id", "=", "customers.id");
            $queryData->where('customers.user_id', $userId);
        }
        $reportsData = [];
        if (count($listDate) > 0) {
            if (count($listDate) == 1) {
                $from = Util::convertDateSql($listDate[0]);
                $to = Util::convertDateSql($listDate[0]);
                $to = $to->modify('+1 day');
                $queryData->whereDate("customer_sources.created", ">=", $from);
                $queryData->whereDate("customer_sources.created", "<", $to);


            } else {
                $to = Util::convertDateSql($listDate[0]);
                $from = Util::convertDateSql($listDate[count($listDate) - 1]);
                $to = $to->modify('+1 day');
                $queryData->whereDate("customer_sources.created", ">=", $from);
                $queryData->whereDate("customer_sources.created", "<", $to);
            }
            $reportsData = $queryData->get();

        }

        $dayMapListOrder = [];
        foreach ($reportsData as $report) {
            $date = Util::convertDateSql($report->day);
            $dateStr = Util::formatDate($date);
            if (array_key_exists($dateStr, $dayMapListOrder)) {
                if (!array_key_exists($report->product_code, $dayMapListOrder[$dateStr])) {
                    $dayMapListOrder[$dateStr][$report->product_code] = new \stdClass();
                    $dayMapListOrder[$dateStr][$report->product_code]->price = $report->product_price;
                    $dayMapListOrder[$dateStr][$report->product_code]->data = 0;
                    $dayMapListOrder[$dateStr][$report->product_code]->total_order = 0;
                    $dayMapListOrder[$dateStr][$report->product_code]->list_order_ids = [];
                }
                if ($report->order_id != null) {
                    $dayMapListOrder[$dateStr][$report->product_code]->total_order += 1;
                    array_push($dayMapListOrder[$dateStr][$report->product_code]->list_order_ids, $report->order_id);
                }
                $dayMapListOrder[$dateStr][$report->product_code]->data += 1;
            } else {
                $dayMapListOrder[$dateStr] = [];
                $dayMapListOrder[$dateStr][$report->product_code] = new \stdClass();
                $dayMapListOrder[$dateStr][$report->product_code]->data = 0;
                $dayMapListOrder[$dateStr][$report->product_code]->price = $report->product_price;
                $dayMapListOrder[$dateStr][$report->product_code]->total_order = 0;
                $dayMapListOrder[$dateStr][$report->product_code]->list_order_ids = [];
                if ($report->order_id != null) {
                    $dayMapListOrder[$dateStr][$report->product_code]->total_order += 1;
                    array_push($dayMapListOrder[$dateStr][$report->product_code]->list_order_ids, $report->order_id);
                }
                $dayMapListOrder[$dateStr][$report->product_code]->data += 1;
            }

        }
        $today = Util::now();
        $yesterday = Util::yesterday();

        $listRows = [];
        foreach ($dayMapListOrder as $dateStr => $listProducts) {
            $sumRevenue = 0;
            $sumData = 0;
            $sumOrder = 0;

            foreach ($listProducts as $productCode => $data) {
                $revenueRow = DB::table("detail_orders")
                    ->select(DB::raw('SUM(actually_collected) as revenue'))
                    ->leftJoin("detail_products", "detail_orders.detail_product_id", "=", "detail_products.id")
                    ->leftJoin("marketing_products", "detail_orders.marketing_product_id", "=", "marketing_products.id")
                    ->join("orders", "orders.id", "=", "detail_orders.order_id")
                    ->whereIn("order_id", array_values($data->list_order_ids))
                    ->where("orders.is_test", false)
                    ->where(function ($query) use($productCode) {
                        $query->where('detail_products.product_code',$productCode)
                            ->orWhere('marketing_products.product_code', $productCode);
                    })
                    ->first();

                $listProducts[$productCode]->revenue = $revenueRow->revenue;
                $sumRevenue += $revenueRow->revenue;

                $row = new \stdClass();
                if ($today == $dateStr) {
                    $row->date_str = "Hôm nay";
                } else {
                    if (Util::equalDate($yesterday, $date)) {
                        $row->date_str = "Hôm qua";
                    } else {
                        $row->date_str = $dateStr;
                    }
                }
                $row->revenue = $revenueRow->revenue;
                $row->total_order = count($data->list_order_ids);
                $row->product_code = $productCode;
                $row->data = $listProducts[$productCode]->data;
                $row->cr2 = round($row->total_order * 1.0 / $row->data * 100, 2);
                $row->product_price = Util::formatMoney($listProducts[$productCode]->price);
                $row->revenue = Util::formatMoney($row->revenue);
                array_push($listRows, $row);
                $sumData += $row->data;
                $sumOrder +=  $row->total_order;
            }
              $row = new \stdClass();
              $row->date_str = "";
              $row->product_code = "TỔNG";
              $row->total_order = $sumOrder;
              $row->data = $sumData;
              $row->cr2 = "";
              $row->product_price = "";
              $row->revenue = Util::formatMoney($sumRevenue);
              array_push($listRows, $row);
        }


        /*  $today = Util::formatDate(Util::now());
          $yesterday = Util::formatDate(Util::yesterday());

          $sumOrder = 0;
          $sumData = 0;
          $sumRevenue = 0;

          $prevDate = null;
          $listRows = [];
          foreach ($reportsData as $report) {

              $date = Util::convertDateSql($report->day);
              $dateStr = Util::formatDate($date);

              if ($prevDate != null && !Util::equalDate($prevDate, $date)) {
                  $row = new \stdClass();
                  $row->date_str = "";
                  $row->product_code = "TỔNG";
                  $row->total_order = $sumOrder;
                  $row->data = $sumData;
                  $row->cr2 = "";
                  $row->product_price = "";
                  $row->revenue = Util::formatMoney($sumRevenue);
                  array_push($listRows, $row);

                  $sumOrder = 0;
                  $sumData = 0;
                  $sumRevenue = 0;
              }
              $row = new \stdClass();
              $prevDate = $date;
              if (Util::equalDate($today, $date)) {
                  $row->date_str = "Hôm nay";
              } else {
                  if (Util::equalDate($yesterday, $date)) {
                      $row->date_str = "Hôm qua";
                  } else {
                      $row->date_str = $dateStr;
                  }

              }
              $row->total_order = 0;
              $row->revenue = 0;
              $row->product_code = $report->product_code;
              $key = json_encode([$report->day, $report->product_code]);
              if (array_key_exists($key, $dayMapReportRevenue)) {
                  $reportRevenue = $dayMapReportRevenue[$key];
                  $row->revenue = $reportRevenue->total_revenue;
                  $row->total_order = $reportRevenue->total_order;
                  $sumRevenue += $reportRevenue->total_revenue;
              }

              $row->data = $report->data;
              $row->cr2 = round($row->total_order * 1.0 / $report->data * 100, 2);
              $row->product_price = Util::formatMoney($report->product_price);
              $row->revenue = Util::formatMoney($row->revenue);
              array_push($listRows, $row);
              $sumOrder += $row->total_order;
              $sumData += $report->data;

          }
          $row = new \stdClass();
          $row->date_str = "";
          $row->product_code = "TỔNG";
          $row->total_order = $sumOrder;
          $row->data = $sumData;
          $row->cr2 = "";
          $row->product_price = "";
          $row->revenue = Util::formatMoney($sumRevenue);
          array_push($listRows, $row);*/
        $result->list_rows = $listRows;
        return $result;

    }


    public static function checkProductCodeForAddCustomer($productCode, $created = null)
    {
        if ($created == null) {
            $created = Util::now();
        }
        $product = Product::where("code", $productCode)->where("is_active", true)->first();
        if ($product != null) {
            return ResultCode::SUCCESS;
        }
        $marketingProduct = MarketingProduct::where("code", $productCode)->first();
        if ($marketingProduct == null) {
            return ResultCode::FAILED_PRODUCT_NOT_FOUND;
        }
        $count = MarketingProduct::where("code", $productCode)
            ->whereDate("created", $created)
            ->count();
        if ($count > 0) {
            return ResultCode::SUCCESS;
        } else {
            return ResultCode::FAILED_CUSTOMER_MARKETING_PRODUCT_NOT_FOUND_TODAY;
        }
    }

    public static function findStorageFromCustomer($customerCode)
    {
        $result = DB::table("customers")
            ->select("products.storage_id as storage_id")
            ->join("customer_sources", "customers.id", "=", "customer_sources.customer_id")
            ->join("products", "products.code", "=", "customer_sources.product_code")
            ->where("customers.code", $customerCode)->first();
        if ($result != null) {
            return Storage::get($result->storage_id);
        }
        return null;

    }

    public static function summaryOrder($orderId)
    {

        $order = self::getOrder($orderId);
        if ($order != null) {
            $content = "Dạ em gửi Anh/Chị thông tin đặt hàng:\n";
            $content .= "\tNgười nhận : " . $order->customer_name . "\n";
            $content .= "\tSĐT : " . $order->customer_phone . "\n";
            $content .= "\tĐịa chi : " . $order->customer_address . "\n";
            $content .= "Thông tin sản phẩm:\n";
            $totalMoney = 0;
            foreach ($order->list_detail_orders as $detailOrder) {
                $productCode = $detailOrder->product_code;
                if ($productCode == "") {

                }
                $content .= "\tMSP : " . $detailOrder->product_code . "\n";
                $content .= "\tSize : " . $detailOrder->product_size . " " . $detailOrder->product_color . "\n";
                $totalMoney += $detailOrder->actually_collected;
            }
            $content .= "Tổng đơn hàng của mình là : " . Util::formatMoney($totalMoney) . " đ và miễn phí ship";
            return $content;
        }
        return "";
    }
}

<?php


namespace App\Http\Controllers;

use App\models\Config;
use App\models\Customer;
use App\models\CustomerState;
use App\models\functions\AdminFunctions;
use App\models\functions\CommonFunctions;
use App\models\functions\Log;
use App\models\functions\MarketingFunctions;
use App\models\functions\ResultCode;
use App\models\functions\SaleFunctions;
use App\models\functions\StoreKeeperFunctions;
use App\models\functions\Util;
use App\models\OrderState;
use App\models\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use mysql_xdevapi\Exception;

class SaleController
{
    public function schedules(Request $request)
    {
        $startTimeStr = $request->get('start_time', '');
        $endTimeStr = $request->get('end_time', '');

        $startTime = Util::safeParseDate($startTimeStr);
        $endTime = Util::safeParseDate($endTimeStr);

        $listSchedules = SaleFunctions::findSchedules(Auth::user(), $startTime, $endTime);

        return view("sale.sale_list_schedules", [
            "list_schedules" => $listSchedules,
            'start_time_str' => $startTimeStr,
            'end_time_str' => $endTimeStr
        ]);

    }

    public function formAddSchedule(Request $request)
    {
        $response = array(
            "status" => 200,
            "content" => "",
            "message" => ""
        );

        $emptySchedule = new \stdClass();
        $emptySchedule->time_str = "";
        $emptySchedule->note = "";
        $emptySchedule->id = -1;
        $response['content'] = view("sale.sale_edit_schedule", [
            "schedule" => $emptySchedule,
        ])->render();
        return response()->json($response);

    }

    public function formUpdateSchedule(Request $request)
    {
        $response = array(
            "status" => 406,
            "content" => "",
            "message" => ""
        );
        $id = $request->get("schedule_id");
        $schedule = SaleFunctions::getSchedule($id);
        if ($schedule != null) {
            $response['status'] = 200;
            $response['content'] = view("sale.sale_edit_schedule", [
                "schedule" => $schedule,
            ])->render();
        }

        return response()->json($response);
    }

    public function saveSchedule(Request $request)
    {
        $response = array(
            "status" => 406,
            "content" => "",
            "message" => ""
        );

        $id = $request->get("schedule_id");
        $timeStr = $request->get('time', '');
        $note = $request->get('note', '');
        $time = Util::safeParseDateTime($timeStr);
        Log::log("TAIH", strval($time));
        if ($time != null && $time > Util::now()) {
            $scheduleInfo = new \stdClass();
            $scheduleInfo->id = $id;
            $scheduleInfo->time = $time;
            $scheduleInfo->note = $note;
            $resultCode = SaleFunctions::saveSchedule(Auth::user(), $scheduleInfo);
            if ($resultCode == ResultCode::SUCCESS) {
                $response['status'] = 200;
            } else {
                $response['message'] = "Lỗi lưu";
            }
        } else {
            $response['message'] = "Thời gian phải lớn hơn thời gian hiện tại";
        }
        return response()->json($response);
    }

    public function deleteSchedule(Request $request)
    {
        $response = array(
            "status" => 406,
            "content" => "",
            "message" => ""
        );

        $id = $request->get("schedule_id");
        $resultCode = SaleFunctions::deleteSchedule(Auth::user(), $id);
        if ($resultCode == ResultCode::SUCCESS) {
            $response['status'] = 200;
        } else {
            $response['message'] = "Lỗi xoá";
        }
        return response()->json($response);
    }

    public function listProducts(Request $request)
    {
        $productCode = $request->get('product_code', '');
        $productCode = trim($productCode);
        $products = SaleFunctions::findListProducts($productCode);
        return view("sale.sale_list_products", [
            "products" => $products,
            "search_product_code" => $productCode
        ]);
    }

    public function searchProductCode(Request $request)
    {
        $response = array();
        $productCode = trim($request->get("search", ""));
        if ($productCode != "") {
            $listProductCode = SaleFunctions::searchProductCode($productCode);
            foreach ($listProductCode as $productCode) {
                $response[] = array("value" => strval($productCode));
            }
        }
        return response()->json($response);
    }

    public function listDiscounts(Request $request)
    {
        $discountCode = trim($request->get("discount_code", ""));
        $listDiscount = SaleFunctions::findDiscounts($discountCode);
        return view("sale.sale_list_discounts", [
            "list_discounts" => $listDiscount,
            "search_discount_code" => $discountCode
        ]);
    }

    public function listOrderFailReasons(Request $request)
    {
        $listOrderFailReasons = SaleFunctions::findOrderFailReasons();
        $editable = Auth::user()->isLeader();
        return view("sale.sale_list_order_fail_reasons", [
            "list_order_fail_reasons" => $listOrderFailReasons,
            'edit_able' => $editable
        ]);
    }

    public function formAddOrderFailReason(Request $request)
    {
        $response = array(
            "status" => 200,
            "content" => "",
            "message" => ""
        );

        $emptyOrderFailReason = new \stdClass();
        $emptyOrderFailReason->cause = "";
        $emptyOrderFailReason->note = "";
        $emptyOrderFailReason->id = -1;
        $response['content'] = view("sale.sale_edit_order_fail_reason", [
            "order_fail_reason" => $emptyOrderFailReason,
        ])->render();
        return response()->json($response);
    }

    public function formUpdateOrderFailReason(Request $request)
    {
        $response = array(
            "status" => 406,
            "content" => "",
            "message" => ""
        );
        $id = $request->get("order_fail_reason_id");
        $orderFailReason = SaleFunctions::getOrderFailedReason($id);
        if ($orderFailReason != null) {
            $response['status'] = 200;
            $response['content'] = view("sale.sale_edit_order_fail_reason", [
                "order_fail_reason" => $orderFailReason,
            ])->render();
        }

        return response()->json($response);
    }

    public function saveOrderFailReason(Request $request)
    {
        $response = array(
            "status" => 406,
            "content" => "",
            "message" => ""
        );
        $id = $request->get("order_fail_reason_id");
        $cause = trim($request->get('cause', ''));
        $note = trim($request->get('note', ''));
        if ($cause != "") {
            $orderFailReasonInfo = new \stdClass();
            $orderFailReasonInfo->id = $id;
            $orderFailReasonInfo->cause = $cause;
            $orderFailReasonInfo->note = $note;
            $resultCode = SaleFunctions::saveOrderFailReason($orderFailReasonInfo);
            if ($resultCode == ResultCode::SUCCESS) {
                $response['status'] = 200;
            } else {
                $response['message'] = "Lỗi lưu";
            }
        }
        return response()->json($response);
    }

    public function deleteOrderFailReason(Request $request)
    {
        $response = array(
            "status" => 406,
            "content" => "",
            "message" => ""
        );

        $id = $request->get("order_fail_reason_id");
        $resultCode = SaleFunctions::deleteOrderFailReason($id);
        if ($resultCode == ResultCode::SUCCESS) {
            $response['status'] = 200;
        } else {
            $response['message'] = "Lỗi xoá";
        }
        return response()->json($response);
    }

    public function listCustomers(Request $request)
    {
        $searchPhoneNumber = $request->get('search_phone_number', "");
        $filterMemberId = Util::parseInt($request->get('filter_member_id', -1));

        $filterMemberStr = "Chọn Người Tạo";
        $listMembers = [];
        $result = SaleFunctions::findAllSales();
        if (Auth::user()->isAdmin()) {
            array_push($listMembers, Auth::user());
        }
        foreach ($result as $member) {
            array_push($listMembers, $member);
        }


        $listUserIds = [];
        if (Auth::user()->isLeader()) {
            if ($filterMemberId == -1) {
                foreach ($listMembers as $member) {
                    array_push($listUserIds, $member->id);
                }

            } else {
                foreach ($listMembers as $member) {
                    if ($member->id == $filterMemberId) {
                        $filterMemberStr = $member->alias_name;
                        break;
                    }
                }
                array_push($listUserIds, $filterMemberId);
            }
        } else {
            $listMembers = [Auth::user()];
            $listUserIds = [Auth::user()->id];
            $filterMemberStr = Auth::user()->username;
            $filterMemberId = Auth::user()->id;
        }

        $listCustomer = SaleFunctions::findCustomers($listUserIds, $searchPhoneNumber);
        $filterMemberDisplay = "";

        if (!Auth::user()->isLeader()) {
            $filterMemberDisplay = "none";
        }
        Log::log("taih", "filter_member_display " . $filterMemberDisplay);
        return view("sale.sale_list_customers", [
            "list_customers" => $listCustomer,
            'search_phone_number' => $searchPhoneNumber,
            'total_customer' => SaleFunctions::countCustomer($listUserIds, $searchPhoneNumber),
            'list_members' => $listMembers,
            'filter_member_id' => strval($filterMemberId),
            'filter_member_str' => $filterMemberStr,
            "filter_member_display" => $filterMemberDisplay
        ]);

    }

    public function detailCustomer(Request $request)
    {
        $response = array(
            "status" => 200,
            "content" => "",
            "message" => ""
        );
        $id = $request->get('customer_id', "");
        $customer = SaleFunctions::getCustomer($id);
        if ($customer != null) {
            $response['content'] = view("sale.sale_detail_customer", [
                'customer' => $customer
            ])->render();
        } else {
            $response['status'] = 406;
            $response['message'] = "Không tìm thấy khách hành";
        }
        return response()->json($response);

    }

    public function formAddCustomer(Request $request)
    {
        $response = array(
            "status" => 200,
            "content" => "",
            "message" => ""
        );
        $emptyCustomer = new \stdClass();
        $emptyCustomer->name = "";
        $emptyCustomer->id = -1;
        $emptyCustomer->phone_number = "";
        $emptyCustomer->birthday_str = "";
        $emptyCustomer->address = "";
        $emptyCustomer->is_public_phone_number = false;
        $emptyCustomer->customer_state = CustomerState::STATE_CUSTOMER_WAITING_FOR_CONFIRMING_CUSTOMER;
        $emptyCustomer->customer_state_name = CustomerState::getName(CustomerState::STATE_CUSTOMER_WAITING_FOR_CONFIRMING_CUSTOMER);
        $emptyCustomer->landing_page_id = -1;
        $emptyCustomer->list_marketing_code = [];
        $emptyCustomer->landing_page_name = "____";
        $listOrderStates = SaleFunctions::listCustomerState([CustomerState::STATE_CUSTOMER_ORDER_CREATED]);

        $listLandingPages = SaleFunctions::listLandingPages();
        $listProvinceNames = CommonFunctions::getListProvinceNames();


        $response['content'] = view("sale.sale_edit_customer", [
            'customer' => $emptyCustomer,
            'customer_district_text' => 'Chọn quận/huyện',
            'customer_province_text' => 'Chọn tỉnh/thành phố',
            'customer_street_text' => 'Chọn đường/phố',
            'list_province_names' => $listProvinceNames,
            'list_district_names' => [],
            'list_street_names' => [],
            'list_province_names_encode' => json_encode($listProvinceNames),
            'list_district_names_encode' => json_encode([]),
            'list_street_names_encode' => json_encode([]),
            'customer_is_public_phone_number' => false,
            'list_customer_states' => $listOrderStates,
            'list_landing_pages' => $listLandingPages,
            'prevProvinceName' => "",
            'prevDistrictName' => "",
            'prevStreetName' => "",
        ])->render();
        return response()->json($response);

    }

    public function formUpdateCustomer(Request $request)
    {
        $response = array(
            "status" => 200,
            "content" => "",
            "message" => ""
        );
        $id = $request->get('customer_id', "");
        $customer = SaleFunctions::getCustomer($id);
        if ($customer != null) {
            if ($customer->landing_page_id == null) {
                $customer->landing_page_id = -1;
            }
            $listOrderStates = SaleFunctions::listCustomerState([CustomerState::STATE_CUSTOMER_ORDER_CREATED]);
            $listLandingPages = SaleFunctions::listLandingPages();
            $listProvinceNames = CommonFunctions::getListProvinceNames();
            $listDistrictNames = [];
            $listStreetNames = [];
            if ($customer->province_name != "") {
                $listDistrictNames = CommonFunctions::getDistrictNames($customer->province_name);
            }
            if ($customer->province_name != "" && $customer->district_name != "") {
                $listStreetNames = CommonFunctions::getStreetsNames($customer->province_name, $customer->district_name);
            }


            $response['content'] = view("sale.sale_edit_customer", [
                'customer' => $customer,
                'customer_district_text' => 'Chọn quận/huyện',
                'customer_province_text' => 'Chọn tỉnh/thành phố',
                'customer_street_text' => 'Chọn đường/phố',
                'list_province_names' => $listProvinceNames,
                'list_district_names' => $listDistrictNames,
                'list_street_names' => $listStreetNames,
                'list_province_names_encode' => json_encode($listProvinceNames),
                'list_district_names_encode' => json_encode($listDistrictNames),
                'list_street_names_encode' => json_encode($listStreetNames),
                'customer_is_public_phone_number' => false,
                'list_customer_states' => $listOrderStates,
                'list_landing_pages' => $listLandingPages,
                'prevProvinceName' => $customer->province_name,
                'prevDistrictName' => $customer->district_name,
                'prevStreetName' => $customer->street_name,
            ])->render();
        } else {
            $response['status'] = 406;
        }

        return response()->json($response);

    }

    public function saveCustomer(Request $request)
    {
        try {
            $response = array(
                "status" => 406,
                "content" => "",
                "message" => ""
            );
            $id = $request->get('customer_id', "");
            $name = $request->get('name', "");
            $phoneNumber = $request->get('phone_number', "");
            $isPublicPhoneNumber = trim($request->get('is_public_phone_number', 'false'));
            $birthdayStr = $request->get('birthday', '');
            $provinceName = $request->get('province_name', '');
            $districtName = $request->get('district_name', '');
            $streetName = $request->get('street_name', '');
            $address = $request->get('address', '');
            $listMarketingProducts = json_decode($request->get('list_marketing_product', '{}'));


            $stateId = Util::parseInt($request->get('state_id', -1));
            $landing_page_id = Util::parseInt($request->get('landing_page_id', -1));
            $birthday = Util::safeParseDate($birthdayStr);
            $street = null;
            if ($provinceName != null && $districtName != null && $streetName != null) {
                $street = CommonFunctions::getStreet($provinceName, $districtName, $streetName);
            }

            if ($isPublicPhoneNumber != null && Util::toLower($isPublicPhoneNumber) == 'false') {
                $isPublicPhoneNumber = false;
            } else {
                $isPublicPhoneNumber = true;
            }


            if ($stateId == -1) {
                $response['status'] = 406;
                $response['message'] = 'Trạng thái không được để trống';
                return response()->json($response);
            }
            if ($name == "") {
                $response['status'] = 406;
                $response['message'] = 'Tên khách hàng không được để trống';
                return response()->json($response);
            }

            if ($stateId == CustomerState::STATE_CUSTOMER_CUSTOMER_AGREED && $address == "") {
                $response['status'] = 406;
                $response['message'] = 'Địa chỉ không được để trống';
                return response()->json($response);
            }

            if ($stateId == CustomerState::STATE_CUSTOMER_CUSTOMER_AGREED && $street == null) {
                $response['status'] = 406;
                $response['message'] = 'Đường phố không được để trống';
                return response()->json($response);
            }

            if ($birthdayStr != "" && $birthday == null) {
                $response['status'] = 406;
                $response['message'] = 'Bạn phải nhập ngày theo dạng dd/mm/yyyy';
                return response()->json($response);
            }

            $customerInfo = new \stdClass();
            $customerInfo->id = $id;
            $customerInfo->name = $name;
            $customerInfo->address = $address;
            $customerInfo->listMarketingProducts = $listMarketingProducts;
            if ($street != null) {
                $customerInfo->street_id = $street->id;
            } else {
                $customerInfo->street_id = null;
            }

            if ($landing_page_id == -1) {
                $customerInfo->landing_page_id = null;
            } else {
                $customerInfo->landing_page_id = $landing_page_id;
            }

            $customerInfo->state_id = $stateId;
            $customerInfo->phone_number = $phoneNumber;
            $customerInfo->is_public_phone_number = $isPublicPhoneNumber;
            $customerInfo->birthday = $birthday;

            $prevCustomer = SaleFunctions::getCustomer($customerInfo->id);

            $resultCode = SaleFunctions::saveCustomer(Auth::user(), $customerInfo);
            if ($resultCode != ResultCode::SUCCESS) {
                $response['message'] = 'Lỗi lưu';
                if ($resultCode == ResultCode::FAILED_SAVE_CUSTOMER_NOT_FOUND_MARKETING_CODE) {
                    $response['message'] = 'Mã marketing không tồn tại';
                }
                if ($resultCode == ResultCode::FAILED_PERMISSION_DENY) {
                    $response['message'] = 'Lỗi Lưu bạn không thể sửa khách hàng của người khác';
                }
                if ($resultCode == ResultCode::UPDATE_FAILED_CUSTOMER_NOT_SAME_DATE) {
                    $response['message'] = 'Lỗi Lưu ngày tạo khách hàng này khách hôm nay';
                }
                if ($resultCode == ResultCode::UPDATE_FAILED_CUSTOMER_IN_STATE_ORDER_CREATED) {
                    $response['message'] = "Lỗi sửa khách hàng đang trong trạng thái lên đơn";
                }

            } else {
                $response['status'] = 200;
                if ($customerInfo->state_id == CustomerState::STATE_CUSTOMER_CUSTOMER_AGREED) {
                    if ($prevCustomer == null || $prevCustomer->order_state != $customerInfo->state_id) {
                        $response['status'] = 302;
                        $response['content'] = $this->createFormAddOrder(Auth::user(), $customerInfo->code)['content'];
                    }
                }

            }
        } catch (\Exception $e) {
            Log::log("error message ", $e->getMessage());
        }
        return response()->json($response);
    }

    public function deleteCustomer(Request $request)
    {
        $response = array(
            "status" => 406,
            "content" => "",
            "message" => "Lỗi xóa"
        );
        try {

            $id = $request->get('customer_id', "");
            $resultCode = SaleFunctions::deleteCustomer(Auth::user(), $id);
            if ($resultCode == ResultCode::SUCCESS) {
                $response['status'] = 200;
            } else {
                if ($resultCode == ResultCode::FAILED_DELETE_CUSTOMER_EXISTED_IN_ORDER) {
                    $response['message'] = "Lỗi xóa khách hàng này đã được lập hóa đơn";
                }
            }

        } catch (\Exception $e) {
            Log::log("taih", $e->getMessage());
        }
        return response()->json($response);
    }

    public function listOrder(Request $request)
    {
        $orderStateId = Util::parseInt($request->get('order_state_id', -1));
        $startTimeStr = $request->get('start_time', '');
        $endTimeStr = $request->get('end_time', '');
        $filterOrderType = Util::parseInt($request->get('filter_order_type', -1));
        $filterCustomerName = $request->get('search_customer_name', '');
        $search_phone_number = $request->get('search_phone_number', '');
        $searchGHTKCode = $request->get('search_ghtk_code', '');
        $filterOrderTypeStr = "";
        $orderStateStr = "Chọn trạng thái";
        $filterMemberStr = "Chọn Người Tạo";
        $orderStateIdStr = "-1";
        $filterMemberId = Util::parseInt($request->get('filter_member_id', -1));

        $startTime = Util::safeParseDate($startTimeStr);
        $endTime = Util::safeParseDate($endTimeStr);

        if ($filterOrderType == 1) {
            $filterOrderTypeStr = "Đơn Test";
        }
        if ($filterOrderType == 0) {
            $filterOrderTypeStr = "Đơn Thực";
        }

        $listMembers = [];
        $result = SaleFunctions::findAllSales();
        if (Auth::user()->isAdmin()) {
            array_push($listMembers, Auth::user());
        }
        foreach ($result as $member) {
            array_push($listMembers, $member);
        }


        $listUserIds = [];
        if (Auth::user()->isLeader()) {
            if ($filterMemberId == -1) {
                foreach ($listMembers as $member) {
                    array_push($listUserIds, $member->id);
                }

            } else {
                foreach ($listMembers as $member) {
                    if ($member->id == $filterMemberId) {
                        $filterMemberStr = $member->alias_name;
                        break;
                    }
                }
                array_push($listUserIds, $filterMemberId);
            }
        } else {
            $listMembers = [Auth::user()];
            $listUserIds = [Auth::user()->id];
            $filterMemberStr = Auth::user()->username;
            $filterMemberId = Auth::user()->id;
        }

        if (OrderState::getName($orderStateId) != "") {
            $orderStateStr = OrderState::getName($orderStateId);
        }

        $listStates = SaleFunctions::getListOrderStates();
        $listOrders = SaleFunctions::findOrders($listUserIds, $startTime, $endTime, $orderStateId, $filterOrderType, $search_phone_number, $searchGHTKCode);
        return view("sale.sale_list_orders", [
            "list_orders" => $listOrders,
            "start_time_str" => $startTimeStr,
            "end_time_str" => $endTimeStr,
            "order_state_str" => $orderStateStr,
            "order_state_id_str" => $orderStateIdStr,
            'list_members' => $listMembers,
            'search_customer_name' => $filterCustomerName,
            'search_phone_number' => $search_phone_number,
            'search_ghtk_code' => $searchGHTKCode,
            'filter_member_id' => strval($filterMemberId),
            'filter_member_str' => $filterMemberStr,
            'list_states' => $listStates,
            'total_order' => SaleFunctions::countOrder($listUserIds, $startTime, $endTime, $orderStateId, $filterOrderType, $search_phone_number, $searchGHTKCode),
            'filter_order_type' => $filterOrderType,
            'filter_order_type_str' => $filterOrderTypeStr
        ]);
    }

    private function createFormAddOrder($user, $customerCode = "")
    {
        $response = array(
            "status" => 200,
            "content" => "",
            "message" => "Lỗi xóa"
        );
        $emptyOrder = new \stdClass();
        $emptyOrder->id = -1;
        $emptyOrder->customer_code = $customerCode;
        $emptyOrder->order_state_id = OrderState::STATE_ORDER_PENDING;
        $emptyOrder->order_fail_reason_id = -1;
        $emptyOrder->created = Util::now();
        $emptyOrder->replace_order_code = "";
        $emptyOrder->order_fail_cause = "___";
        $emptyOrder->note = Config::getOrNew()->order_note;
        $emptyOrder->is_test = false;
        $listStorages = Storage::findAll();
        $emptyOrder->storage_id = $listStorages[0]->id;
        $emptyOrder->storage_address = $listStorages[0]->address;
        $listOrderStates = [];
        //$listOrderStates = SaleFunctions::getListOrderStates();
        $listFailReasons = SaleFunctions::getListFailReasons();
        $listSizes = CommonFunctions::listProductSizes();
        $listColors = CommonFunctions::listProductColors();
        $listDiscounts = CommonFunctions::listDiscounts();

        $listSuggestionProductCode = SaleFunctions::listSuggestionProductCodes();
        $listDetailOrders = [];
        $detailEditable = true;
        $response['content'] = view("sale.sale_add_order", [
            "order" => $emptyOrder,
            "list_detail_orders" => $listDetailOrders,
            "list_states" => $listOrderStates,
            "list_product_size" => $listSizes,
            "list_product_color" => $listColors,
            'list_product_discount' => $listDiscounts,
            'start_time_str' => "",
            'user' => Auth::user(),
            'order_state_name' => OrderState::getName(OrderState::STATE_ORDER_PENDING),
            'detailEditable' => $detailEditable,
            'list_fail_reasons' => $listFailReasons,
            'list_storages' => $listStorages,
            'list_suggestion_product_codes' => json_encode($listSuggestionProductCode)
        ])->render();
        return $response;
    }


    public function formAddOrder(Request $request)
    {
        return response()->json($this->createFormAddOrder(Auth::user()));
    }

    public function detailOrder(Request $request)
    {
        $response = array(
            "status" => 406,
            "content" => "",
            "message" => "Không tìm thấy hóa đơn"
        );
        $id = $request->get("order_id");
        $order = SaleFunctions::getOrder($id);
        if ($order != null) {
            $response['content'] = view("sale.sale_detail_order", [
                "order" => $order,
            ])->render();
            $response['status'] = 200;
        }
        return response()->json($response);
    }

    public function formUpdateOrder(Request $request)
    {
        $response = array(
            "status" => 406,
            "content" => "",
            "message" => "Không tìm thấy hóa đơn"
        );
        $id = $request->get("order_id");
        $order = SaleFunctions::getOrder($id);
        if ($order != null) {
            $listStorages = Storage::findAll();
            if ($order->order_fail_cause == "") {
                $order->order_fail_cause = "___";
            }
            $listOrderStates = [];
            //$listOrderStates = SaleFunctions::getListOrderStates();
            $listFailReasons = SaleFunctions::getListFailReasons();
            $response["status"] = 200;
            $response['content'] = view("sale.sale_update_order", [
                "order" => $order,
                "list_detail_orders" => $order->list_detail_orders,
                "list_states" => $listOrderStates,
                "list_product_size" => [],
                "list_product_color" => [],
                'list_product_discount' => [],
                'start_time_str' => "",
                'list_storages' => $listStorages,
                'user' => Auth::user(),
                'order_state_name' => OrderState::getName($order->order_state),
                'detailEditable' => false,
                'list_fail_reasons' => $listFailReasons,
                'list_suggestion_product_codes' => json_encode([])
            ])->render();

        }
        return response()->json($response);
    }

    public
    function addOrder(Request $request)
    {
        try {
            $response = array(
                "status" => 406,
                "content" => "",
                "message" => "Lỗi thêm"
            );

            $customerCode = $request->get('customer_code');
            $note = $request->get('note', '');
            $orderStateId = Util::parseInt($request->get('order_state_id'));
            $orderFailId = Util::parseInt($request->get('order_fail_id', -1), -1);
            $isOrderTest = Util::parseInt($request->get('is_order_test', 0));
            $replace_order_code = $request->get('replace_order', '');
            $storageId = Util::parseInt($request->get("storage_id", 0));
            $deliveryTime = Util::safeParseDate($request->get('delivery_time', ''));
            if ($deliveryTime != null) {
                if ($deliveryTime < Util::now()) {
                    $response['message'] = 'Ngày giao hàng phải lớn hơn thời gian hiện tại';
                }
            }
            $listDetailOrders = json_decode($request->get('list_detail_orders', '{}'));
            if (count($listDetailOrders) == 0) {
                $response['message'] = 'Chi tiết đơn hàng không được rỗng';
            }
            $orderInfo = new \stdClass();
            $orderInfo->customer_code = $customerCode;
            $orderInfo->note = $note;
            $orderInfo->delivery_time = $deliveryTime;
            $orderInfo->is_test = $isOrderTest == 1;
            $orderInfo->storage_id = $storageId;
            $orderInfo->order_state_id = CustomerState::STATE_CUSTOMER_CUSTOMER_AGREED;
            if ($orderFailId == -1) {
                $orderInfo->order_fail_reason_id = null;
            } else {
                $orderInfo->order_fail_reason_id = $orderFailId;
            }
            $orderInfo->replace_order_code = $replace_order_code;
            $orderInfo->detail_orders = [];
            foreach ($listDetailOrders as $item) {
                $detailOrder = new \stdClass();
                $detailOrder->marketing_product_code = $item->marketing_product_code;
                $detailOrder->product_size = $item->product_size;
                $detailOrder->product_color = $item->product_color;
                $detailOrder->quantity = Util::parseInt($item->quantity);
                $detailOrder->actually_collected = Util::parseInt($item->actually_collected, -1);
                $detailOrder->pick_money = Util::parseInt($item->pick_money, -1);
                $detailOrder->discount_id = Util::parseInt($item->discount_id, -1);
                if ($detailOrder->discount_id <= 0) {
                    $detailOrder->discount_id = null;
                }
                if ($detailOrder->pick_money < 0) {
                    $response['message'] = "Tiền thu hộ không hợp lệ";
                    return response()->json($response);
                }
                if ($detailOrder->actually_collected < 0) {
                    $response['message'] = "Tiền thực thu không hợp lệ";
                    return response()->json($response);
                }
                if ($detailOrder->quantity <= 0) {
                    $response['message'] = "Số lượng không hợp lệ";
                    return response()->json($response);
                }

                array_push($orderInfo->detail_orders, $detailOrder);
            }
            $resultCode = SaleFunctions::addOrder(Auth::user(), $orderInfo);
            if ($resultCode == ResultCode::SUCCESS) {
                $response['status'] = 200;
            } else {
                if ($resultCode == ResultCode::FAILED_SAVE_DETAIL_ORDER_OUT_OF_PRODUCT) {
                    $response['message'] = "Trong kho không đủ cho số lượng hiện tại";
                }
                if ($resultCode == ResultCode::FAILED_SAVE_ORDER_NOT_FOUND_PRODUCT_CODE_IN_CUSTOMER_SOURCE) {
                    $response['message'] = "Không tìm thấy sản phẩm trong danh sách sản phảm quan tâm";
                }
                if ($resultCode == ResultCode::FAILED_SAVE_ORDER_DUPLICATE_CUSTOMER) {
                    $response['message'] = "Trùng mã khách hàng vui lòng tạo mới khách hàng";
                }
                if ($resultCode == ResultCode::FAILED_SAVE_ORDER_NOT_FOUND_REPLACE_ORDER) {
                    $response['message'] = "Không tìm thấy mã hóa đơn hoàn";
                }
                if ($resultCode == ResultCode::FAILED_SAVE_ORDER_REPLACE_ORDER_LEAK_STATE) {
                    $response['message'] = "Mã hóa đơn hoàn phải trong trạng thái đã trả về lỗi hoặc không lỗi";
                }
            }
            return response()->json($response);
        } catch (\Exception $e) {
            Log::log("error message ", $e->getMessage());
        }

    }

    public function updateOrder(Request $request)
    {
        $response = array(
            "status" => 406,
            "content" => "",
            "message" => "Lỗi sửa"
        );

        $orderId = Util::parseInt($request->get('order_id', -1));
        $customerCode = $request->get('customer_code');
        $note = $request->get('note', '');
        $orderStateId = Util::parseInt($request->get('order_state_id'));
        $orderFailId = Util::parseInt($request->get('order_fail_id', -1), -1);
        $isOrderTest = Util::parseInt($request->get('is_order_test', 0));
        $replace_order_code = $request->get('replace_order', '');
        $storageId = Util::parseInt($request->get("storage_id", 0));

        $deliveryTime = Util::safeParseDate($request->get('delivery_time', ''));
        if ($deliveryTime != null) {
            if ($deliveryTime < Util::now()) {
                $response['message'] = 'Ngày giao hàng phải lớn hơn thời gian hiện tại';
            }
        }
        $orderInfo = new \stdClass();
        $orderInfo->id = $orderId;
        $orderInfo->customer_code = $customerCode;
        $orderInfo->note = $note;
        $orderInfo->delivery_time = $deliveryTime;
        $orderInfo->is_test = $isOrderTest == 1;
        $orderInfo->order_state_id = $orderStateId;
        $orderInfo->storage_id = $storageId;
        if ($orderFailId == -1) {
            $orderInfo->order_fail_reason_id = null;
        } else {
            $orderInfo->order_fail_reason_id = $orderFailId;
        }
        $orderInfo->replace_order_code = $replace_order_code;

        $resultCode = SaleFunctions::updateOrder(Auth::user(), $orderInfo);
        if ($resultCode == ResultCode::SUCCESS) {
            $response['status'] = 200;
        }
        return response()->json($response);
    }

    public function deleteOrder(Request $request)
    {
        $response = array(
            "status" => 406,
            "content" => "",
            "message" => "Lỗi xóa"
        );

        $orderId = Util::parseInt($request->get('order_id', -1));
        $resultCode = SaleFunctions::deleteOrder(Auth::user(), $orderId);

        if ($resultCode == ResultCode::SUCCESS) {
            $response['status'] = 200;
        } else {
            if ($resultCode == ResultCode::FAILED_DELETE_ORDER_STATE_MORE_THAN_STATE_ORDER_CREATED) {
                $response['message'] = "Lỗi xóa hóa đơn đã được đăng lên GHTK";
            }
        }
        return response()->json($response);
    }

    public function queryMarketingProductPrice(Request $request)
    {
        try {

            $response = array(
                "status" => 200,
                "content" => "",
                "message" => ""
            );
            $productCode = $request->get('marketing_product_code', '');
            $productSize = $request->get('product_size', '');
            $productColor = $request->get('product_color', '');
            $discountId = $request->get('discount_id', '');
            $quantity = Util::parseInt($request->get('quantity', ''), 0);
            if ($quantity > 0) {
                $price = SaleFunctions::getPrice($productCode);
                if ($price > 0) {
                    if (SaleFunctions::availableQuantity($productCode, $productSize, $productColor, $quantity)) {
                        $discountValue = 0;
                        $discount = AdminFunctions::getDiscount($discountId);
                        if ($discount != null) {
                            $discountValue = $discount->discount_value;
                        }
                        $response['content'] = [
                            'price' => $price,
                            'discount_value' => $discountValue
                        ];
                    } else {
                        $response['status'] = 406;
                        $response['message'] = "Trong kho không đủ cho số lượng hiện tại";
                    }
                } else {
                    $response['status'] = 406;
                    $response['message'] = 'Không tìm thấy sản phẩm';
                }
            } else {
                $response['status'] = 406;
                $response['message'] = 'Không tìm thấy sản phẩm';
            }
            return response()->json($response);
        } catch (\Exception $e) {
            Log::log("error message ", $e->getMessage());
        }
    }

    public function orderHistory(Request $request)
    {
        $startTimeStr = $request->get('start_time', '');
        $endTimeStr = $request->get('end_time', '');
        $startTime = Util::safeParseDate($startTimeStr);
        $endTime = Util::safeParseDate($endTimeStr);
        $listHistories = SaleFunctions::listOrderHistories($startTime, $endTime);

        return view("sale.sale_order_history", [
            "list_histories" => $listHistories,
            'start_time_str' => $startTimeStr,
            'end_time_str' => $endTimeStr
        ]);

    }

    public function exportingProductHistory(Request $request)
    {
        $startTimeStr = $request->get('start_time', '');
        $endTimeStr = $request->get('end_time', '');
        $startTime = Util::safeParseDate($startTimeStr);
        $endTime = Util::safeParseDate($endTimeStr);
        $listHistories = StoreKeeperFunctions::listImportingExportingHistories(1, $startTime, $endTime);

        return view("sale.sale_exporting_product_history", [
            "list_histories" => $listHistories,
            'start_time_str' => $startTimeStr,
            'end_time_str' => $endTimeStr
        ]);
    }

    public function orderDeliver(Request $request)
    {
        $response = array(
            "status" => 302,
            "content" => "",
            "message" => "Permission Denied"
        );
        if (Auth::user()->isLeader()) {
            $listOrders = SaleFunctions::listDeliverOrders();
            return view("sale.sale_leader_order_deliver", [
                "list_orders" => $listOrders
            ]);
        } else {
            return response()->json($response);
        }
    }

    public function getFormPrepareOrderDeliver(Request $request)
    {
        $response = array(
            "status" => 302,
            "content" => "",
            "message" => "Permission Denied"
        );
        if (Auth::user()->isLeader()) {
            $listOrderIds = json_decode($request->get('list_order_ids', '[]'));
            $listOrders = SaleFunctions::listOrderDelivering($listOrderIds);
            $response['status'] = 200;
            $response['content'] = view("sale.sale_leader_form_prepare_order_deliver", [
                "list_orders" => $listOrders
            ])->render();
            return response()->json($response);
        } else {
            return response()->json($response);
        }
    }

    public function pushOrderToGHTK(Request $request)
    {
        $response = array(
            'status' => 403,
            'message' => '',
            'content' => ''
        );
        if (!Auth::user()->isLeader()) {
            $response["message"] = "Permission Denied";
            return response()->json($response);
        }
        $orderId = $request->get("order_id");
        $resultCode = SaleFunctions::pushOrderToGHTK($orderId);
        if ($resultCode == ResultCode::SUCCESS) {
            $response['status'] = 200;
        }
        return response()->json($response);
    }

    public function orderStateManager(Request $request)
    {
        $response = array(
            "status" => 302,
            "content" => "",
            "message" => "Permission Denied"
        );
        if (Auth::user()->isLeader()) {
            $startTimeStr = $request->get('start_time', '');
            $searchGHTKCode = $request->get('search_ghtk_code', '');
            $search_phone_number = $request->get('search_phone_number', '');
            $endTimeStr = $request->get('end_time', '');
            $orderStateStr = "Chọn trạng thái";
            $orderStateIdStr = "-1";
            $startTime = Util::safeParseDate($startTimeStr);
            $endTime = Util::safeParseDate($endTimeStr);
            $orderStateId = Util::parseInt($request->get('order_state_id', ''), -1);
            if ($orderStateId != -1) {
                $orderStateIdStr = strval($orderStateId);
                $orderStateStr = OrderState::getName($orderStateId);
            }

            $listMembers = SaleFunctions::findAllSales();

            $listUserIds = [];
            foreach ($listMembers as $member) {
                array_push($listUserIds, $member->id);
            }
            $listOrders = SaleFunctions::listOrderStateManager($listUserIds, $startTime, $endTime, $orderStateId, $searchGHTKCode, $search_phone_number);
            $listState = SaleFunctions::getListOrderStates();
            return view("sale.sale_leader_order_state_manager", [
                "list_orders" => $listOrders,
                'list_states' => $listState,
                'start_time_str' => $startTimeStr,
                'end_time_str' => $endTimeStr,
                'order_state_str' => $orderStateStr,
                'order_state_id_str' => $orderStateIdStr,
                'search_ghtk_code' => $searchGHTKCode,
                'search_phone_number' => $search_phone_number,
                'total_order' => SaleFunctions::countOrderStateManager($listUserIds, $startTime, $endTime, $orderStateId, $searchGHTKCode, $search_phone_number)
            ]);
        } else {
            return response()->json($response);
        }
    }

    public function getFormPrepareOrderStateSynchronizer(Request $request)
    {
        $response = array(
            "status" => 302,
            "content" => "",
            "message" => "Permission Denied"
        );
        if (Auth::user()->isLeader()) {
            $listOrderIds = json_decode($request->get('list_order_ids', '[]'));
            $listOrders = SaleFunctions::filterOrderByIds($listOrderIds);
            $response['status'] = 200;
            $response['content'] = view("sale.sale_leader_form_prepare_order_state_synchronizer", [
                "list_orders" => $listOrders
            ])->render();
            return response()->json($response);
        } else {
            return response()->json($response);
        }
    }

    public function synchronizeOrderState(Request $request)
    {
        $response = array(
            "status" => 403,
            'message' => '',
            'content' => '',
            "new_order_state" => "fake state",
            "is_change" => true
        );
        if (!Auth::user()->isLeader()) {
            $response["message"] = "Permission Denied";
            return response()->json($response);
        }
        $orderId = $request->get("order_id");
        $result = SaleFunctions::syncOrderState($orderId);
        if ($result->result_code == ResultCode::SUCCESS) {
            $response['status'] = 200;
            $response['new_order_state'] = $result->new_order_state;
            $response['is_change'] = $result->is_change;
        }
        return response()->json($response);
    }

    public function getFormPrepareCancelOrder(Request $request)
    {
        $response = array(
            "status" => 302,
            "content" => "",
            "message" => "Permission Denied"
        );
        if (Auth::user()->isLeader()) {
            $listOrderIds = json_decode($request->get('list_order_ids', '[]'));
            $listOrders = SaleFunctions::filterOrderByIds($listOrderIds);
            $response['status'] = 200;
            $response['content'] = view("sale.sale_leader_form_prepare_cancel_order", [
                "list_orders" => $listOrders
            ])->render();
            return response()->json($response);
        } else {
            return response()->json($response);
        }
    }

    public function cancelOrder(Request $request)
    {
        $response = array(
            "status" => 403,
            'message' => '',
            'content' => '',
            "new_order_state" => "fake state",
            "is_change" => true
        );
        if (!Auth::user()->isLeader()) {
            $response["message"] = "Permission Denied";
            return response()->json($response);
        }
        $orderId = $request->get("order_id");
        $result = SaleFunctions::cancelOrder(Auth::user(), $orderId);
        if ($result->result_code == ResultCode::SUCCESS) {
            $response['status'] = 200;
            $response['new_order_state'] = $result->new_order_state;
            $response['is_change'] = $result->is_change;
        }
        return response()->json($response);
    }

    public function prepareUpdateOrderState(Request $request)
    {
        $response = array(
            "status" => 302,
            "content" => "",
            "message" => "Permission Denied"
        );
        if (Auth::user()->isLeader()) {
            $listOrderIds = json_decode($request->get('list_order_ids', '[]'));
            $listOrders = SaleFunctions::filterOrderByIds($listOrderIds);

            $listStates = [];
            $state = new \stdClass();
            $state->id = OrderState::STATE_ORDER_IS_RETURNED_AND_BROKEN;
            $state->name = OrderState::getName($state->id);
            array_push($listStates, $state);

            $state = new \stdClass();
            $state->id = OrderState::STATE_ORDER_IS_RETURNED_AND_NO_BROKEN;
            $state->name = OrderState::getName($state->id);
            array_push($listStates, $state);

            $response['status'] = 200;
            $response['content'] = view("sale.sale_leader_form_prepare_update_order_state", [
                "list_orders" => $listOrders,
                "list_states" => $listStates
            ])->render();
            return response()->json($response);
        } else {
            return response()->json($response);
        }
    }


    public function updateOrderState(Request $request)
    {
        $response = array(
            "status" => 403,
            'message' => '',
            'content' => ''
        );
        if (!Auth::user()->isLeader()) {
            $response["message"] = "Permission Denied";
            return response()->json($response);
        }
        $newOrderState = Util::parseInt($request->get("new_state_id", ''), 0);
        $orderId = Util::parseInt($request->get("order_id", ''), 0);
        $result = SaleFunctions::orderStateManagerUpdateState(Auth::user(), $orderId, $newOrderState);
        if ($result->result_code == ResultCode::SUCCESS) {
            $response['status'] = 200;
        }
        return response()->json($response);
    }

    public function reportOrder(Request $request)
    {
        $filterMemberId = Util::parseInt($request->get('filter_member_id', -1));
        $filterMemberStr = Auth::user()->alias_name;
        $listMembers = [];
        $result = SaleFunctions::findAllSales();
        if (Auth::user()->isAdmin()) {
            array_push($listMembers, Auth::user());
        }
        foreach ($result as $member) {
            array_push($listMembers, $member);
        }


        $listUserIds = [];

        if ($filterMemberId != -1) {
            foreach ($listMembers as $member) {
                if ($member->id == $filterMemberId) {
                    $filterMemberStr = $member->alias_name;
                    break;
                }
            }
        } else {
            $filterMemberId = Auth::user()->id;
        }


        $listOrderReports = SaleFunctions::reportOrder($filterMemberId);
        return view("sale.sale_report", [
            "list_order_reports" => $listOrderReports,
            'list_members' => $listMembers,
            'filter_member_id' => strval($filterMemberId),
            'filter_member_str' => $filterMemberStr,
        ]);
    }

    public function searchPhoneNumber(Request $request)
    {
        $response = array();
        $phoneNumber = trim($request->get("search", ""));
        if ($phoneNumber != "") {
            $listCustomers = SaleFunctions::searchPhoneNumber($phoneNumber);
            foreach ($listCustomers as $customer) {
                $response[] = array("value" => strval($customer->phone_number));
            }
        }
        return response()->json($response);
    }

    public function customerCheckProductCode(Request $request)
    {
        $response = array(
            "status" => 403,
            'message' => '',
            'content' => ''
        );
        $productCode = $request->get("product_code", "");
        Log::log("taih", $productCode);
        if ($productCode == "") {
            $response['message'] = "Không tìm thấy sản phẩm";
        } else {
            $resultCode = SaleFunctions::checkProductCodeForAddCustomer($productCode);
            if ($resultCode == ResultCode::SUCCESS) {
                $response['status'] = 200;
            } else {
                if ($resultCode == ResultCode::FAILED_PRODUCT_NOT_FOUND) {
                    $response['message'] = "Không tìm thấy sản phẩm";
                }
                if ($resultCode == ResultCode::FAILED_CUSTOMER_MARKETING_PRODUCT_NOT_FOUND_TODAY) {
                    $response['message'] = "Hôm nay, Marketing chưa tạo mã sản phẩm này";
                }
            }
        }

        return response()->json($response);
    }

    public function findCustomerByPhoneNumber(Request $request)
    {
        $response = array(
            "status" => 403,
            'message' => '',
            'content' => ''
        );
        $phoneNumber = $request->get("phone_number", "");
        if ($phoneNumber != "") {
            $customer = SaleFunctions::findCustomerByPhoneNumber($phoneNumber);
            $listDistrictNames = [];
            $listStreetNames = [];
            if ($customer->province_name != "") {
                $listDistrictNames = CommonFunctions::getDistrictNames($customer->province_name);
            }
            if ($customer->province_name != "" && $customer->district_name != "") {
                $listStreetNames = CommonFunctions::getStreetsNames($customer->province_name, $customer->district_name);
            }
            if ($customer != null) {
                $response['status'] = 200;
                $response['customer'] = [
                    "name" => $customer->name,
                    "is_public_phone_number" => $customer->public_phone_number,
                    "birthday_text" => $customer->birthday_str,
                    "province" => $customer->province_name,
                    "district" => $customer->district_name,
                    "street" => $customer->street_name,
                    "address" => $customer->address,
                    'list_district_names_encode' => json_encode($listDistrictNames),
                    'list_street_names_encode' => json_encode($listStreetNames),
                ];
            }
        }
        return response()->json($response);
    }

    public function summaryOrder(Request $request)
    {
        $response = array(
            "status" => 403,
            'message' => '',
            'content' => ''
        );

        $orderId = $request->get("order_id", "");
        if ($orderId == null || $orderId == "") {
            $response['message'] = "Không tìm thấy hóa đơn";
        } else {
            $response['status'] = 200;
            $response['content'] = SaleFunctions::summaryOrder($orderId);
        }
        return response()->json($response);

    }


}

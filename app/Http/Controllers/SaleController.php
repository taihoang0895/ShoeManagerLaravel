<?php


namespace App\Http\Controllers;

use App\models\CustomerState;
use App\models\functions\CommonFunctions;
use App\models\functions\Log;
use App\models\functions\ResultCode;
use App\models\functions\SaleFunctions;
use App\models\functions\StoreKeeperFunctions;
use App\models\functions\Util;
use App\models\OrderState;
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
        $listCustomer = SaleFunctions::findCustomers(Auth::user(), $searchPhoneNumber);
        return view("sale.sale_list_customers", [
            "list_customers" => $listCustomer,
            'search_phone_number' => $searchPhoneNumber
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
        $emptyCustomer->landing_page_name = "____";
        $listOrderStates = SaleFunctions::listCustomerState();
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
            $listOrderStates = SaleFunctions::listCustomerState();
            $listLandingPages = SaleFunctions::listLandingPages();
            $listProvinceNames = CommonFunctions::getListProvinceNames();

            $response['content'] = view("sale.sale_edit_customer", [
                'customer' => $customer,
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

            $resultCode = SaleFunctions::saveCustomer(Auth::user(), $customerInfo);
            if ($resultCode != ResultCode::SUCCESS) {
                $response['message'] = 'Lỗi lưu';
            } else {
                $response['status'] = 200;
            }
            return response()->json($response);
        } catch (\Exception $e) {
            Log::log("error message ", $e->getMessage());
            return ResultCode::FAILED_UNKNOWN;
        }

    }

    public function deleteCustomer(Request $request)
    {
        $response = array(
            "status" => 406,
            "content" => "",
            "message" => "Lỗi xóa"
        );
        $id = $request->get('customer_id', "");
        $resultCode = SaleFunctions::deleteCustomer(Auth::user(), $id);
        if ($resultCode == ResultCode::SUCCESS) {
            $response['status'] = 200;
        }
        return response()->json($response);
    }

    public function listOrder(Request $request)
    {
        $orderStateId = Util::parseInt($request->get('order_state_id', -1));
        $startTimeStr = $request->get('start_time', '');
        $endTimeStr = $request->get('end_time', '');
        $orderStateStr = "Chọn trạng thái";
        $filterMemberStr = "Chọn Người Tạo";
        $orderStateIdStr = "-1";
        $filterMemberId = Util::parseInt($request->get('filter_member_id', -1));

        $startTime = Util::safeParseDate($startTimeStr);
        $endTime = Util::safeParseDate($endTimeStr);

        $listMembers = SaleFunctions::findAllSales();
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
        $listOrders = SaleFunctions::findOrders($listUserIds, $startTime, $endTime, $orderStateId);
        return view("sale.sale_list_orders", [
            "list_orders" => $listOrders,
            "start_time_str" => $startTimeStr,
            "end_time_str" => $endTimeStr,
            "order_state_str" => $orderStateStr,
            "order_state_id_str" => $orderStateIdStr,
            'list_members' => $listMembers,
            'filter_member_id' => strval($filterMemberId),
            'filter_member_str' => $filterMemberStr,
            'list_states' => $listStates
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
        $emptyOrder->customer_code = "";
        $emptyOrder->order_state_id = OrderState::STATE_CUSTOMER_AGREED;
        $emptyOrder->order_fail_reason_id = -1;
        $emptyOrder->created = Util::now();
        $emptyOrder->replace_order_code = "";
        $emptyOrder->order_fail_cause = "___";
        $emptyOrder->note = "";
        $emptyOrder->is_test = false;
        $listOrderStates = SaleFunctions::getListOrderStates();
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
            'order_state_name' => OrderState::getName(OrderState::STATE_CUSTOMER_AGREED),
            'detailEditable' => $detailEditable,
            'list_fail_reasons' => $listFailReasons,
            'list_suggestion_product_codes' => json_encode($listSuggestionProductCode)
        ])->render();
        return response()->json($response);
    }


    public function formAddOrder(Request $request)
    {
        return $this->createFormAddOrder(Auth::user());
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
            if ($order->order_fail_cause == "") {
                $order->order_fail_cause = "___";
            }

            $listOrderStates = SaleFunctions::getListOrderStates();
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

                'user' => Auth::user(),
                'order_state_name' => OrderState::getName(OrderState::STATE_CUSTOMER_AGREED),
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
            $orderInfo->order_state_id = $orderStateId;
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
        if (SaleFunctions::deleteOrder(Auth::user(), $orderId) == ResultCode::SUCCESS) {
            $response['status'] = 200;
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
            $quantity = Util::parseInt($request->get('quantity', ''), 0);
            if ($quantity > 0) {
                $price = SaleFunctions::getPrice($productCode);
                if ($price > 0) {
                    if (SaleFunctions::availableQuantity($productCode, $productSize, $productColor, $quantity)) {
                        $response['content'] = ['price' => $price];
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
}

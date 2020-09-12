<?php

namespace App\Http\Controllers;


use App\Http\Controllers\objects\TableCell;
use App\models\functions\CommonFunctions;
use App\models\functions\Log;
use App\models\functions\Util;
use App\models\NotificationManager;
use App\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class CommonController extends Controller
{
    public function logout(Request $request)
    {
        Auth::logout();
        return redirect("/login/");
    }

    public function login(Request $request)
    {
        if (Auth::check()) {
            return $this->switchUser();
        }

        if ($request->method() == Request::METHOD_POST) {
            $credentials = $request->only('username', 'password');
            if (Auth::attempt($credentials)) {
                if (!Auth::user()->is_active) {
                    Auth::logout();
                    return view("login", [
                        "login_failed" => true
                    ]);
                }
                return $this->switchUser();
            } else {
                return view("login", [
                    "login_failed" => true
                ]);
            }
        }
        return view("login", [
            "login_failed" => false
        ]);
    }

    private function switchUser()
    {
        if (Auth::user()->isAdmin()) {
            return redirect("/admin");
        }
        if (Auth::user()->isStorekeeper()) {
            return redirect("/storekeeper");
        }
        if (Auth::user()->isSale()) {
            return redirect("/sale");
        }
        if (Auth::user()->isMarketing()) {
            return redirect("/marketing");
        }
    }
    public function searchGHTKCode(Request $request){
        $response = array();
        $code = trim($request->get("search", ""));
        if ($code != "") {
            $listOrders = CommonFunctions::searchGHTKCode($code);
            foreach ($listOrders as $order) {
                $response[] = array("value" => strval($order->ghtk_label));
            }
        }


        return response()->json($response);
    }
    public function searchProductCode(Request $request)
    {
        $response = array();
        $productCode = trim($request->get("search", ""));
        if ($productCode != "") {
            $listProducts = CommonFunctions::searchProduct($productCode);
            foreach ($listProducts as $product) {
                $response[] = array("value" => strval($product->code));
            }
        }


        return response()->json($response);
    }

    public function detailProduct(Request $request)
    {
        $response = array(
            "status" => 200,
            "content" => "",
            "message" => ""
        );
        $size_cells = array();
        $code_color_cells = array();
        $total_remaining_quantity_cells = array();
        $remaining_quantity_cells = array();
        $product_code = $request->get("product_code", "");
        $group_data = array();
        $list_detail_products = CommonFunctions::findDetailProducts($product_code);

        foreach ($list_detail_products as $detail_product) {
            $key = json_encode([
                'product_code' => $detail_product->product_code,
                'product_color' => $detail_product->color]);
            if (!array_key_exists($key, $group_data)) {
                $group_data[$key] = [];
            }
            $group_data[$key][$detail_product->size] = $detail_product->remaining_quantity;
        }
        $sum_remaining_quantity = 0;
        foreach ($group_data as $key => $value) {
            $total_remaining_quantity = 0;
            foreach ($value as $size => $quantity) {
                $total_remaining_quantity += $quantity;
                $sum_remaining_quantity += $quantity;
                $size_cell = new TableCell($size);
                array_push($size_cells, $size_cell);

                $remaining_quantity_cell = new TableCell($quantity);
                array_push($remaining_quantity_cells, $remaining_quantity_cell);
            }
            $total_remaining_quantity_cell = new TableCell($total_remaining_quantity, count($value));

            $code_color_product = json_decode($key);

            $code_color_cell = new TableCell(strval($code_color_product->product_code) . " " . strval($code_color_product->product_color),
                count($value));

            array_push($code_color_cells, $code_color_cell);
            array_push($total_remaining_quantity_cells, $total_remaining_quantity_cell);
        }

        $response['content'] = view("common.detail_product", [
            "size_cells" => $size_cells,
            "code_color_cells" => $code_color_cells,
            "total_remaining_quantity_cells" => $total_remaining_quantity_cells,
            "remaining_quantity_cells" => $remaining_quantity_cells,
            "sum_remaining_quantity" => $sum_remaining_quantity
        ])->render();
        return response()->json($response);
    }

    public function getNotifications(Request $request)
    {
        $response = array(
            "status" => 200,
            "content" => "",
            "message" => "",
            "count_message_unread" => 0
        );
        $listNotification = CommonFunctions::listNotifications(Auth::user());
        $totalMessageUnread = 0;
        foreach ($listNotification as $notification) {
            if ($notification->unread) {
                $totalMessageUnread += 1;
            }
        }
        $response['count_message_unread'] = $totalMessageUnread;
        $response['content'] = view("list_notifications", [
            "list_notification_rows" => $listNotification
        ])->render();
        return response()->json($response);

    }

    public function updateNotification(Request $request)
    {
        $response = array(
            "status" => 200,
            "content" => "",
            "message" => "",
            "count_message_unread" => 0
        );
        $listNotificationIds = json_decode($request->get("notification_ids", '[]'));
        $listNotificationUnread = json_decode($request->get("list_unread", '[]'));
        $notificationMapUnread = [];
        for ($i = 0; $i < count($listNotificationIds); $i++) {
            $notificationMapUnread[$listNotificationIds[$i]] = $listNotificationUnread[$i];
        }
        CommonFunctions::markNotification(Auth::user(), $notificationMapUnread);
        $response['count_message_unread'] = CommonFunctions::notificationCountUnread(Auth::user());
        return response()->json($response);
    }

    public function checkNotification(Request $request)
    {
        try {

            $response = array(
                "status" => 200,
                "content" => "",
                "message" => "",
                'has_notification' => 0,
                'unread_message' => 0
            );
            CommonFunctions::checkReminds(Auth::user());

            $notificationManager = NotificationManager::getOrNew(Auth::user());
            $response['unread_message'] = $notificationManager->unread_count;
            if ($notificationManager->has_notification) {
                $response['has_notification'] = 1;
            }
            CommonFunctions::updateHasNotification(Auth::user(), false);
            return response()->json($response);
        }catch (\Exception $e){
            Log::log("error messgae",$e->getMessage());
        }
    }

    public function fakeNotification(Request $request)
    {
        $response = array(
            "status" => 200,
            "content" => "",
            "message" => ""
        );

        CommonFunctions::createNotification(Auth::user(), "fake notificationfak");
        return response()->json($response);
    }


    public function listDistricts(Request $request)
    {
        $response = [
            'status' => 200,
            'message' => '',
            'content' => ''
        ];
        $provinceName = $request->get('province_name', '');
        $response['message'] = $provinceName;
        $response['content'] = json_encode(CommonFunctions::getDistrictNames($provinceName));

        return response()->json($response);
    }

    public function listStreets(Request $request)
    {
        $response = [
            'status' => 200,
            'message' => '',
            'content' => ''
        ];

        $provinceName = $request->get('province_name', '');
        $districtName = $request->get('district_name', '');
        $response['content'] = json_encode(CommonFunctions::getStreetsNames($provinceName, $districtName));
        return response()->json($response);
    }
}

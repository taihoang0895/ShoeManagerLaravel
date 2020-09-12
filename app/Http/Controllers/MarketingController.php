<?php


namespace App\Http\Controllers;


use App\Http\Controllers\objects\TableCell;
use App\models\functions\AdminFunctions;
use App\models\functions\CommonFunctions;
use App\models\functions\Log;
use App\models\functions\MarketingFunctions;
use App\models\functions\ResultCode;
use App\models\functions\StoreKeeperFunctions;
use App\models\functions\Util;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use mysql_xdevapi\Exception;

class MarketingController
{
    public function listProducts(Request $request)
    {
        $productCode = $request->get('product_code', '');
        $products = MarketingFunctions::findListProducts($productCode);
        return view("marketing.marketing_list_products", [
            "products" => $products,
            "search_product_code" => $productCode
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
            $response['content'] = view("marketing.marketing_edit_product", [
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

        $response['content'] = view("marketing.marketing_edit_product", [
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

            //if (count($listProductDetails) != 0) {
                $resultCode = AdminFunctions::addProduct($product, $listProductDetails);
                if ($resultCode == ResultCode::SUCCESS) {
                    $response['status'] = 200;
                } else {
                    if ($resultCode == ResultCode::FAILED_PRODUCT_DUPLICATE_CODE) {
                        $response['message'] = "Trùng mã sản phẩm";
                    }
                }
            //}
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

            //if (count($listProductDetails) != 0) {
                if (AdminFunctions::updateProduct($product, $listProductDetails) == ResultCode::SUCCESS) {
                    $response['status'] = 200;
                }
            //}
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



    private function listMarketingProductsForLeader(Request $request)
    {
        $marketingSourceId = Util::parseInt($request->get('marketing_source_id', -1), -1);
        $startTimeStr = $request->get('start_time', "");
        $endTimeStr = $request->get('end_time', "");
        $filterMemberId = $request->get('filter_member_id', -1);
        $searchProductCode = $request->get('search_product_code', "");

        $startTime = Util::safeParseDate($startTimeStr);
        $endTime = Util::safeParseDate($endTimeStr);
        $filterMemberStr = "";
        $listUserIds = [];
        $members = MarketingFunctions::findAllMarketing();
        if ($filterMemberId == -1) {
            foreach ($members as $member) {
                array_push($listUserIds, $member->id);
            }
        } else {
            foreach ($members as $member) {
                if ($member->id == $filterMemberId) {
                    $filterMemberStr = $member->alias_name;
                }
            }
            array_push($listUserIds, $filterMemberId);
        }
        $marketingProducts = MarketingFunctions::findMarketingProduct($listUserIds, $marketingSourceId, $startTime, $endTime, $searchProductCode);
        $listMarketingSource = MarketingFunctions::listMarketingSource();
        $filterMarketingSourceStr = "";
        $listProductCodes =  json_encode(CommonFunctions::listProductCodes());
        foreach ($listMarketingSource as $marketingSource) {
            if ($marketingSourceId == $marketingSource->id) {
                $filterMarketingSourceStr = $marketingSource->name;
            }
        }
        return view("marketing.marketing_leader_list_marketing_products", [
            "list_marketing_products" => $marketingProducts,
            'list_marketing_sources' => $listMarketingSource,
            'marketing_source_id' => $marketingSourceId,
            'filter_marketing_source_str' => $filterMarketingSourceStr,
            'start_time_str' => $startTimeStr,
            'end_time_str' => $endTimeStr,
            'list_members' => $members,
            'filter_member_id' => $filterMemberId,
            'filter_member_str' => $filterMemberStr,
            'list_product_codes' => $listProductCodes,
            'search_product_code' => $searchProductCode
        ]);
    }

    private function listMarketingProductsForMember(Request $request)
    {
        $marketingSourceId = Util::parseInt($request->get('marketing_source_id', -1), -1);
        $startTimeStr = $request->get('start_time', "");
        $endTimeStr = $request->get('end_time', "");
        $searchProductCode = $request->get('search_product_code', "");

        $startTime = Util::safeParseDate($startTimeStr);
        $endTime = Util::safeParseDate($endTimeStr);

        $marketingProducts = MarketingFunctions::findMarketingProduct([Auth::user()->id], $marketingSourceId, $startTime, $endTime, $searchProductCode);
        $listProductCodes = json_encode(CommonFunctions::listProductCodes());
        $listMarketingSource = MarketingFunctions::listMarketingSource();
        $filterMarketingSourceStr = "";
        foreach ($listMarketingSource as $marketingSource) {
            if ($marketingSourceId == $marketingSource->id) {
                $filterMarketingSourceStr = $marketingSource->name;
            }
        }
        return view("marketing.marketing_list_marketing_products", [
            "list_marketing_products" => $marketingProducts,
            'list_marketing_sources' => $listMarketingSource,
            'marketing_source_id' => $marketingSourceId,
            'filter_marketing_source_str' => $filterMarketingSourceStr,
            'start_time_str' => $startTimeStr,
            'end_time_str' => $endTimeStr,
            'list_product_codes' => $listProductCodes,
            'search_product_code' => $searchProductCode

        ]);
    }

    public function listMarketingProducts(Request $request)
    {
        if (Auth::user()->isLeader()) {
            return $this->listMarketingProductsForLeader($request);
        } else {
            return $this->listMarketingProductsForMember($request);
        }

    }

    public function getFormAddMarketingProduct(Request $request)
    {
        $response = array(
            "status" => 200,
            "content" => "",
            "message" => ""
        );

        $emptyMarketingProduct = new \stdClass();
        $emptyMarketingProduct->marketing_source = "___";
        $emptyMarketingProduct->id = -1;
        $emptyMarketingProduct->marketing_source_id = -1;
        $emptyMarketingProduct->product_code = "";
        $emptyMarketingProduct->code = "";
        $listMarketingSource = MarketingFunctions::listMarketingSource();
        $listCampaignName = MarketingFunctions::listCampaignName();
        $listBankAccounts = MarketingFunctions::listBankAccounts();
        $listProductCodes = json_encode(CommonFunctions::listProductCodes());
        $response['content'] = view("marketing.marketing_edit_marketing_product", [
            'marketing_source' => $emptyMarketingProduct->marketing_source,
            'marketing_product_id' => $emptyMarketingProduct->id,
            'marketing_code' => $emptyMarketingProduct->code,
            'marketing_product_source_id' => $emptyMarketingProduct->marketing_source_id,
            'detail_campaign_additional_campaign_name_selected_id' => -1,
            'detail_campaign_additional_bank_account_selected_id' => -1,
            'list_campaign_names' => $listCampaignName,
            'list_campaigns' => [],
            'list_bank_accounts' => $listBankAccounts,
            'marketing_product_code' => $emptyMarketingProduct->product_code,
            'marketing_product_created' => Util::formatDate(Util::now()),
            'list_marketing_sources' => $listMarketingSource,
            'list_product_codes' => $listProductCodes
        ])->render();
        return response()->json($response);

    }

    public function getFormUpdateMarketingProduct(Request $request)
    {
        $response = array(
            "status" => 200,
            "content" => "",
            "message" => ""
        );
        $marketingProductId = $request->get('marketing_product_id', "");
        $marketingProduct = MarketingFunctions::getMarketingProduct($marketingProductId);
        if ($marketingProduct != null) {
            $listMarketingSource = MarketingFunctions::listMarketingSource();
            $listCampaignName = MarketingFunctions::listCampaignName();
            $listBankAccounts = MarketingFunctions::listBankAccounts();
            $listProductCodes = json_encode(CommonFunctions::listProductCodes());
            $response['content'] = view("marketing.marketing_edit_marketing_product", [
                'marketing_source' => $marketingProduct->sourceName(),
                'marketing_product_id' => $marketingProduct->id,
                'marketing_code' => $marketingProduct->code,
                'marketing_product_source_id' => $marketingProduct->marketing_source_id,
                'detail_campaign_additional_campaign_name_selected_id' => -1,
                'detail_campaign_additional_bank_account_selected_id' => -1,
                'list_campaign_names' => $listCampaignName,
                'list_campaigns' => $marketingProduct->list_campaigns,
                'list_bank_accounts' => $listBankAccounts,
                'marketing_product_code' => $marketingProduct->product_code,
                'marketing_product_created' => Util::formatDate(Util::now()),
                'list_marketing_sources' => $listMarketingSource,
                'list_product_codes' => $listProductCodes
            ])->render();
        } else {
            $response['status'] = 406;
        }

        return response()->json($response);
    }

    public function saveMarketingProduct(Request $request)
    {
        $response = array(
            "status" => 406,
            "content" => "",
            "message" => ""
        );
        try {

            $marketingProductId = $request->get('marketing_product_id', -1);
            Log::log("sjdbsbds", $marketingProductId);
            $productCode = $request->get('product_code', "");
            $marketingCode= $request->get('marketing_code', "");
            $marketingSourceId = Util::parseInt($request->get("marketing_source_id"));
            $created = Util::safeParseDate($request->get('marketing_product_created'));
            if($marketingCode == ""){
                $response['message'] = "Mã marketing không được để rỗng";
                return response()->json($response);
            }
            if ($created != null) {
                $listCampaigns = json_decode($request->get('list_campaigns', '{}'));
                if (count($listCampaigns) > 0) {
                    $marketingProductInfo = new \stdClass();
                    $marketingProductInfo->id = $marketingProductId;
                    $marketingProductInfo->source_id = $marketingSourceId;
                    $marketingProductInfo->created = $created;
                    $marketingProductInfo->product_code = $productCode;
                    $marketingProductInfo->code = $marketingCode;

                    $listCampaignInfo = [];
                    foreach ($listCampaigns as $item) {
                        $campaignInfo = new \stdClass();
                        $campaignInfo->campaign_name_id = $item->campaign_name_id;
                        $campaignInfo->bank_account_id = $item->bank_account_id;
                        $campaignInfo->total_comment = $item->total_comment;
                        $campaignInfo->budget = $item->budget;
                        if ($campaignInfo->budget <= 0) {
                            $response['message'] = "budget must be more than 0";
                            throw new \Exception("budget must be more than 0");
                        }
                        if ($campaignInfo->total_comment <= 0) {
                            $response['message'] = "total_comment must be more than 0";
                            throw new \Exception("total_comment must be more than 0");
                        }
                        array_push($listCampaignInfo, $campaignInfo);
                    }
                    $marketingProductInfo->list_campaign_infos = $listCampaignInfo;
                    $resultCode = MarketingFunctions::saveMarketingProduct(Auth::user(), $marketingProductInfo);
                    if ($resultCode == ResultCode::SUCCESS) {
                        $response['status'] = 200;
                    }else{
                        if($resultCode == ResultCode::FAILED_MARKETING_PRODUCT_DUPLICATE_CODE){
                        $response['message'] = "Lỗi trùng mã marketing";
                        }
                    }
                }
            }
        } catch (\Exception $e) {

        }
        return response()->json($response);
    }

    public function detailMarketingProductCode(Request $request)
    {
        $response = array(
            "status" => 406,
            "content" => "",
            "message" => "Lỗi"
        );
        $marketingProductId = $request->get('marketing_product_id', "");
        $marketingProduct = MarketingFunctions::getMarketingProduct($marketingProductId);
        if ($marketingProduct != null) {
            $response['status'] = 200;
            $response['content'] = view("marketing.marketing_detail_marketing_product", [
                'marketing_product' => $marketingProduct
            ])->render();
        }
        return response()->json($response);
    }

    public function deleteMarketingProduct(Request $request)
    {
        $response = array(
            "status" => 200,
            "content" => "",
            "message" => ""
        );
        $marketingProductId = $request->get('marketing_product_id', "");
        $resultCode = MarketingFunctions::deleteMarketingProduct(Auth::user(), $marketingProductId);
        if ($resultCode != ResultCode::SUCCESS) {
            $response['status'] = 406;
            $response['message'] = "Lỗi xoá";
        }
        return response()->json($response);
    }

    public function marketingSources(Request $request)
    {
        $marketingSources = MarketingFunctions::findMarketingSource();
        $editable = Auth::user()->isLeader();
        return view("marketing.marketing_list_marketing_sources", [
            "list_marketing_sources" => $marketingSources,
            'editable' => $editable
        ]);


    }

    public function formAddMarketingSource(Request $request)
    {
        $response = array(
            "status" => 200,
            "content" => "",
            "message" => "Lỗi"
        );
        $marketingSource = new \stdClass();
        $marketingSource->id = -1;
        $marketingSource->name = "";
        $marketingSource->note = "";
        $response['content'] = view("marketing.marketing_edit_marketing_source", [
            'marketing_source' => $marketingSource
        ])->render();
        return response()->json($response);
    }

    public function formUpdateMarketingSource(Request $request)
    {
        $response = array(
            "status" => 406,
            "content" => "",
            "message" => "Lỗi"
        );
        $marketingSourceId = $request->get("marketing_source_id");
        $marketingSource = MarketingFunctions::getMarketingSource($marketingSourceId);
        if ($marketingSource != null) {
            $response['status'] = 200;
            $response['content'] = view("marketing.marketing_edit_marketing_source", [
                'marketing_source' => $marketingSource
            ])->render();
        }
        Log::log("isnfs", $marketingSourceId);
        return response()->json($response);
    }

    public function saveMarketingSource(Request $request)
    {
        $response = array(
            "status" => 406,
            "content" => "",
            "message" => "Lỗi lưu"
        );
        $id = $request->get("marketing_source_id", "");
        $name = $request->get("name", "");
        $note = $request->get("note", "");
        if ($note == null) {
            $note = "";
        }
        if ($name != "") {
            $marketingSourceInfo = new \stdClass();
            $marketingSourceInfo->id = $id;
            $marketingSourceInfo->name = $name;
            $marketingSourceInfo->note = $note;
            $resultCode = MarketingFunctions::saveMarketingSource($marketingSourceInfo);
            if ($resultCode == ResultCode::SUCCESS) {
                $response['status'] = 200;
            }
        }
        return response()->json($response);
    }

    public function deleteMarketingSource(Request $request)
    {
        $response = array(
            "status" => 200,
            "content" => "",
            "message" => ""
        );
        $marketingSourceId = $request->get('marketing_source_id', "");
        $resultCode = MarketingFunctions::deleteMarketingSource(Auth::user(), $marketingSourceId);
        if ($resultCode != ResultCode::SUCCESS) {
            $response['status'] = 406;
            $response['message'] = "Lỗi xoá";
        }
        return response()->json($response);
    }

    public function listBankAccounts(Request $request)
    {
        $bankAccountName = $request->get('bank_account', '');
        $bankAccounts = MarketingFunctions::findBankAccount($bankAccountName);
        return view("marketing.marketing_list_bank_accounts", [
            "list_bank_accounts" => $bankAccounts,
            'search_bank_account' => $bankAccountName
        ]);
    }

    public function formAddBankAccount(Request $request)
    {
        $response = array(
            "status" => 200,
            "content" => "",
            "message" => "Lỗi"
        );
        $bankAccount = new \stdClass();
        $bankAccount->id = -1;
        $bankAccount->name = "";
        $response['content'] = view("marketing.marketing_edit_bank_account", [
            'bank_account' => $bankAccount
        ])->render();
        return response()->json($response);
    }

    public function formUpdateBankAccount(Request $request)
    {
        $response = array(
            "status" => 406,
            "content" => "",
            "message" => "Lỗi"
        );
        $id = $request->get("bank_account_id", -1);
        $bankAccount = MarketingFunctions::getBankAccount($id);
        if ($bankAccount != null) {
            $response['status'] = 200;
            $response['content'] = view("marketing.marketing_edit_bank_account", [
                'bank_account' => $bankAccount
            ])->render();
        }

        return response()->json($response);
    }

    public function saveBankAccount(Request $request)
    {
        $response = array(
            "status" => 406,
            "content" => "",
            "message" => "Lỗi lưu"
        );
        $id = $request->get("bank_account_id", "");
        $name = $request->get("name", "");
        Log::log("id", $id);
        if ($name != "") {
            $bankAccountInfo = new \stdClass();
            $bankAccountInfo->id = $id;
            $bankAccountInfo->name = $name;
            $resultCode = MarketingFunctions::saveBankAccount($bankAccountInfo);
            if ($resultCode == ResultCode::SUCCESS) {
                $response['status'] = 200;
            }
        }
        return response()->json($response);
    }

    public function deleteBankAccount(Request $request)
    {
        $response = array(
            "status" => 200,
            "content" => "",
            "message" => ""
        );
        $id = $request->get('bank_account_id', "");
        $resultCode = MarketingFunctions::deleteBankAccount($id);
        if ($resultCode != ResultCode::SUCCESS) {
            $response['status'] = 406;
            $response['message'] = "Lỗi xoá";
        }
        return response()->json($response);
    }

    public function revenueReport(Request $request)
    {
        $listMembers = [];
        $listUserIds = [];
        $filterMemberStr = "";
        $filterMemberId = Util::parseInt($request->get('filter_member_id', -1));
        $reportTimeType = Util::parseInt($request->get('report_time_type', 0), 0);
        $reportTimeStr = $request->get('report_time');



        if(Auth::user()->isLeader()){
            $listMembers = MarketingFunctions::findAllMarketing();
            if($filterMemberId == -1){
                foreach ($listMembers as $member) {
                    array_push($listUserIds, $member->id);
                }
                $filterMemberStr = "___";
            }else{

                foreach ($listMembers as $member) {
                    if ($member->id == $filterMemberId) {
                        $filterMemberStr = $member->alias_name;
                    }
                }
                array_push($listUserIds, $filterMemberId);
            }
        }else{
            $listMembers= [Auth::user()];
            $filterMemberId = Auth::user()->id;
            $listUserIds = [Auth::user()->id];
            $filterMemberStr = Auth::user()->username;
        }


        $day = null;
        $month = null;
        $year = null;
        if ($reportTimeType == 0) {
            $reportTime = Util::safeParseDate($reportTimeStr, null);
            if ($reportTime != null) {
                $day = $reportTime->day;
                $month = $reportTime->month;
                $year = $reportTime->year;
            } else {
                $reportTimeStr = "";
            }
        }
        if ($reportTimeType == 1) {
            $tmp = "1/" . $reportTimeStr;
            $reportTime = Util::safeParseDate($tmp, null);
            if ($reportTime != null) {
                $month = $reportTime->month;
                $year = $reportTime->year;
            } else {
                $reportTimeStr = "";
            }

        }
        if ($reportTimeType == 2) {
            $tmp = "1/1/" . $reportTimeStr;
            $reportTime = Util::safeParseDate($tmp, null);
            if ($reportTime != null) {
                $year = $reportTime->year;
            } else {
                $reportTimeStr = "";
            }

        }

        $revenueReportTimeType = "Ngày";
        if ($reportTimeType == 1) {
            $revenueReportTimeType = "Tháng";
        }
        if ($reportTimeType == 2) {
            $revenueReportTimeType = "Năm";
        }


        $revenueReport = MarketingFunctions::reportRevenue($listUserIds, $day, $month, $year);
        return view("marketing.marketing_revenue_report", [
            "table" => $revenueReport->dataset,
            'col_names' => $revenueReport->col_names,
            'row_names' => $revenueReport->row_names,
            'report_time_type' => $reportTimeType,
            'revenue_report_time_type' => $revenueReportTimeType,
            'report_time_str' => $reportTimeStr,
            'list_members' => $listMembers,
            'filter_member_id' => $filterMemberId,
            'filter_member_str' => $filterMemberStr
        ]);
    }


    public function inventoryReport(Request $request)
    {
        $importing_quantity_cells = [];
        $exporting_quantity_cells = [];
        $returning_quantity_cells = [];
        $failed_quantity_cells = [];
        $remaining_quantity_cells = [];
        $size_cells = [];
        $code_color_cells = [];
        $total_remaining_quantity_cells = [];
        $group_data = [];

        $sum_importing_quantity = 0;
        $sum_exporting_quantity = 0;
        $sum_returning_quantity = 0;
        $sum_failed_quantity = 0;
        $sum_remaining_quantity = 0;

        $listInventoryReports = StoreKeeperFunctions::reportInventory();

        foreach ($listInventoryReports as $inventoryReport) {
            $key = json_encode([$inventoryReport->product_code, $inventoryReport->product_color]);
            if (!array_key_exists($key, $group_data)) {
                $group_data[$key] = [];
            }

            $group_data[$key][$inventoryReport->product_size] = [$inventoryReport->remaining_quantity,
                $inventoryReport->importing_quantity,
                $inventoryReport->exporting_quantity,
                $inventoryReport->returning_quantity,
                $inventoryReport->failed_quantity];
        }
        foreach ($group_data as $group_key => $group) {
            $total_remaining_quantity = 0;
            foreach ($group as $size => $record) {
                for ($i = 0; $i <= 4; ++$i) {
                    if ($record[$i] == null) {
                        $record[$i] = 0;
                    }
                }

                $sum_remaining_quantity += $record[0];
                $sum_importing_quantity += $record[1];
                $sum_exporting_quantity += $record[2];
                $sum_returning_quantity += $record[3];
                $sum_failed_quantity += $record[4];

                $total_remaining_quantity += $record[0];

                $remaining_quantity_cell = new TableCell($record[0]);
                $importing_quantity_cell = new TableCell($record[1]);
                $exporting_quantity_cell = new TableCell($record[2]);
                $returning_quantity_cell = new TableCell($record[3]);
                $failed_quantity_cell = new TableCell($record[4]);
                $size_cell = new TableCell($size);
                array_push($remaining_quantity_cells, $remaining_quantity_cell);
                array_push($importing_quantity_cells, $importing_quantity_cell);
                array_push($exporting_quantity_cells, $exporting_quantity_cell);
                array_push($returning_quantity_cells, $returning_quantity_cell);
                array_push($failed_quantity_cells, $failed_quantity_cell);
                array_push($size_cells, $size_cell);
            }
            $total_remaining_quantity_cell = new TableCell($total_remaining_quantity, count($group));
            $group_key = json_decode($group_key);
            $code_color_cell = new TableCell(($group_key[0] . " " . $group_key[1]), count($group));

            array_push($code_color_cells, $code_color_cell);
            array_push($total_remaining_quantity_cells, $total_remaining_quantity_cell);

        }

        return view("marketing.marketing_inventory_report", [
            "importing_quantity_cells" => $importing_quantity_cells,
            'exporting_quantity_cells' => $exporting_quantity_cells,
            'returning_quantity_cells' => $returning_quantity_cells,
            'failed_quantity_cells' => $failed_quantity_cells,
            'remaining_quantity_cells' => $remaining_quantity_cells,
            'size_cells' => $size_cells,
            'code_color_cells' => $code_color_cells,
            'total_remaining_quantity_cells' => $total_remaining_quantity_cells,
            'sum_importing_quantity' => $sum_importing_quantity,
            'sum_exporting_quantity' => $sum_exporting_quantity,
            'sum_returning_quantity' => $sum_returning_quantity,
            'sum_failed_quantity' => $sum_failed_quantity,
            'sum_remaining_quantity' => $sum_remaining_quantity
        ]);

    }


    public function listCampaignNames(Request $request)
    {
        $campaignName = $request->get('campaign_name', "");
        $listCampaignNames = MarketingFunctions::findCampaignName($campaignName);

        return view("marketing.marketing_list_campaign_names", [
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
        $resultCode = MarketingFunctions::saveCampaignName($campaignNameInfo);
        if ($resultCode != ResultCode::SUCCESS) {
            $response['status'] = 406;
            $response['message'] = 'Lỗi thêm';
            if($resultCode == ResultCode::FAILED_CAMPAIGN_NAME_DUPLICATE_NAME){
                $response['message'] = 'Lỗi trùng tên chiến dịch';
            }
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

        $response['content'] = view("marketing.marketing_edit_campaign_name", [
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
        $campaignName = MarketingFunctions::getCampaignName($CampaignNameId);
        if ($campaignName != null) {
            $response['content'] = view("marketing.marketing_edit_campaign_name", [
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
        $resultCode = MarketingFunctions::deleteCampaignName($campaignNameId);
        if ($resultCode != ResultCode::SUCCESS) {
            $response['status'] = 406;
            $response['message'] = 'Lỗi xóa khuyến mại';
        }
        return response()->json($response);
    }

}

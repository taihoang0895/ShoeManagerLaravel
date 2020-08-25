<?php


namespace App\Http\Controllers;


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
        $listProductCodes = json_encode(MarketingFunctions::listMarketingProductCodes());
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
        $listProductCodes = json_encode(MarketingFunctions::listMarketingProductCodes());
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
        $listMarketingSource = MarketingFunctions::listMarketingSource();
        $listCampaignName = MarketingFunctions::listCampaignName();
        $listBankAccounts = MarketingFunctions::listBankAccounts();
        $listProductCodes = json_encode(CommonFunctions::listProductCodes());
        $response['content'] = view("marketing.marketing_edit_marketing_product", [
            'marketing_source' => $emptyMarketingProduct->marketing_source,
            'marketing_product_id' => $emptyMarketingProduct->id,
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
            $marketingSourceId = Util::parseInt($request->get("marketing_source_id"));
            $created = Util::safeParseDate($request->get('marketing_product_created'));
            if ($created != null) {
                $listCampaigns = json_decode($request->get('list_campaigns', '{}'));
                if (count($listCampaigns) > 0) {
                    $marketingProductInfo = new \stdClass();
                    $marketingProductInfo->id = $marketingProductId;
                    $marketingProductInfo->source_id = $marketingSourceId;
                    $marketingProductInfo->created = $created;
                    $marketingProductInfo->product_code = $productCode;

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
}

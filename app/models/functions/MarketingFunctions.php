<?php


namespace App\models\functions;


use App\models\BankAccount;
use App\models\Campaign;
use App\models\CampaignName;
use App\models\MarketingProduct;
use App\models\MarketingSource;
use App\models\Product;
use App\User;
use Illuminate\Support\Facades\DB;

class MarketingFunctions
{
    public static function findAllMarketing()
    {
        return User::where("department", User::$DEPARTMENT_MARKETING)->get();
    }

    public static function findListProducts($productCode = "")
    {
        $filterOptions = [];
        if ($productCode != "") {
            $filterOptions[] = ["code", "like", '%' . $productCode . '%'];
        }
        $perPage = config('settings.per_page');
        return Product::where($filterOptions)->paginate($perPage);
    }

    public static function listMarketingProductCodes(){
        $listProductCodes = [];
        foreach (DB::table('marketing_products')->distinct()->get(['code']) as $productCat){
            array_push($listProductCodes, $productCat->code);
        }
        return $listProductCodes;
    }

    public static function findMarketingProduct($listUserIds, $marketingSourceId, $startTime = null, $endTime = null, $searchProductCode = "")
    {
        $filterOptions = [];
        if ($marketingSourceId != "" && $marketingSourceId != -1) {
            $filterOptions['marketing_source_id'] = $marketingSourceId;
        }
        if ($startTime != null && $endTime != null) {
            $filterOptions[] = ['created', '>=', $startTime];
            $filterOptions[] = ['created', '<=', $endTime];
        }
        if ($searchProductCode != "") {
            $filterOptions[] = ['code', 'like', '%' . $searchProductCode . '%'];
        }
        $perPage = config('settings.per_page');
        $marketingProducts = MarketingProduct::whereIn('user_id', $listUserIds)->where($filterOptions)->paginate($perPage);
        return $marketingProducts;
    }

    public static function listMarketingSource()
    {
        return MarketingSource::all();
    }

    public static function listCampaignName()
    {
        return CampaignName::all();
    }

    public static function listBankAccounts()
    {
        return BankAccount::all();
    }

    public static function getMarketingProduct($marketingProductId)
    {
        $marketingProduct = MarketingProduct::where("id", $marketingProductId)->first();
        if ($marketingProduct != null) {
            $marketingProduct->list_campaigns = Campaign::where("marketing_product_id", $marketingProduct->id)->get();
        }
        return $marketingProduct;
    }

    public static function saveMarketingProduct($user, $marketingProductInfo)
    {
        DB::beginTransaction();
        try {
            $marketingProduct = MarketingProduct::where("id", $marketingProductInfo->id)->first();

            if ($marketingProduct != null) {
                if ($marketingProduct->user_id != $user->id) {
                    return ResultCode::FAILED_PERMISSION_DENY;
                }
                Campaign::where("marketing_product_id", $marketingProductInfo->id)->delete();
            } else {
                $marketingProduct = new MarketingProduct();
                $marketingProduct->created = $marketingProductInfo->created;
            }
            $marketingProduct->user_id = $user->id;
            $marketingProduct->marketing_source_id = $marketingProductInfo->source_id;
            $marketingProduct->product_code = $marketingProductInfo->product_code;
            $marketingProduct->save();
            if ($marketingProduct->code == null) {
                $marketingProduct->code = "MSPM_" . Util::formatLeadingZeros($marketingProduct->id, 4);
                $marketingProduct->save();
            }
            $totalComment = 0;
            $totalBudget = 0;
            foreach ($marketingProductInfo->list_campaign_infos as $campaignInfo) {
                $campaign = new Campaign();
                $campaign->marketing_product_id = $marketingProduct->id;
                $campaign->budget = $campaignInfo->budget;
                $campaign->bank_account_id = $campaignInfo->bank_account_id;
                $campaign->total_comment = $campaignInfo->total_comment;
                $campaign->campaign_name_id = $campaignInfo->campaign_name_id;
                $campaign->save();
                $totalComment += $campaignInfo->total_comment;
                $totalBudget += $campaignInfo->budget;
            }
            $marketingProduct->total_budget = $totalBudget;
            $marketingProduct->total_comment = $totalComment;
            $marketingProduct->save();
            DB::commit();
            return ResultCode::SUCCESS;
        } catch (\Exception $e) {
            Log::log("error message", $e->getMessage());
            DB::rollBack();
        }
        return ResultCode::FAILED_UNKNOWN;
    }

    public static function deleteMarketingProduct($user, $marketingProductId)
    {
        DB::beginTransaction();
        try {
            $marketingProduct = MarketingProduct::where("id", $marketingProductId)->first();
            if ($marketingProduct != null) {
                if ($marketingProduct->user_id != $user->id) {
                    return ResultCode::FAILED_PERMISSION_DENY;
                }
                Campaign::where("marketing_product_id", $marketingProductId)->delete();
                MarketingProduct::where("id", $marketingProductId)->delete();
                DB::commit();
                return ResultCode::SUCCESS;
            }
        } catch (\Exception $e) {
            Log::log("error message", $e->getMessage());
            DB::rollBack();
        }
        return ResultCode::FAILED_UNKNOWN;
    }

}

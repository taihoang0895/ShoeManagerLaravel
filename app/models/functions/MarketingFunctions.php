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
        return Product::where($filterOptions)->where("is_active", true)->paginate($perPage);
    }

    public static function listMarketingProductCodes()
    {
        $listProductCodes = [];
        foreach (DB::table('marketing_products')->distinct()->get(['code']) as $productCat) {
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
            $filterOptions[] = ['product_code', 'like', '%' . $searchProductCode . '%'];
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

    public static function findMarketingSource()
    {
        $perPage = config('settings.per_page');
        return MarketingSource::where([])->paginate($perPage);
    }

    public static function saveMarketingSource($marketingSourceInfo)
    {
        try {
            $marketingSource = MarketingSource::where("id", $marketingSourceInfo->id)->first();
            if ($marketingSource == null) {
                $marketingSource = new MarketingSource();
            }
            $marketingSource->name = $marketingSourceInfo->name;
            $marketingSource->note = $marketingSourceInfo->note;
            if ($marketingSource->save()) {
                return ResultCode::SUCCESS;
            }
        } catch (\Exception $e) {
            Log::log("error message", $e->getMessage());
        }
        return ResultCode::FAILED_UNKNOWN;

    }

    public static function getMarketingSource($marketingSourceId)
    {
        $marketingSource = MarketingSource::where("id", $marketingSourceId)->first();
        return $marketingSource;
    }

    public static function deleteMarketingSource($user, $marketingSourceId)
    {
        try {
            if ($user->isMember()) {
                return ResultCode::FAILED_PERMISSION_DENY;
            }
            if (MarketingSource::where("id", $marketingSourceId)->delete()) {
                return ResultCode::SUCCESS;
            }
        } catch (\Exception $e) {
            Log::log("error message", $e->getMessage());
        }
        return ResultCode::FAILED_UNKNOWN;

    }

    public static function findBankAccount($bankAccount = "")
    {
        $filterOptions = [];
        $filterOptions['is_active'] = true;
        if ($bankAccount != "") {
            $filterOptions[] = ["name", "like", '%' . $bankAccount . '%'];
        }
        $perPage = config('settings.per_page');
        return BankAccount::where($filterOptions)->paginate($perPage);
    }

    public static function getBankAccount($id)
    {
        $bankAccount = BankAccount::where("id", $id)->first();
        return $bankAccount;
    }

    public static function saveBankAccount($bankAccountInfo)
    {
        $bankAccount = BankAccount::where("id", $bankAccountInfo->id)->first();
        if ($bankAccount == null) {
            $bankAccount = new BankAccount();
            $bankAccount->created = Util::now();
        }
        $bankAccount->name = $bankAccountInfo->name;
        if ($bankAccount->save()) {
            return ResultCode::SUCCESS;
        }
        return ResultCode::FAILED_UNKNOWN;
    }

    public static function deleteBankAccount($id)
    {
        try {
            $bankAccount = BankAccount::where("id", $id)->first();
            if ($bankAccount != null) {
                $bankAccount->is_active = false;
                if ($bankAccount->save()) {
                    return ResultCode::SUCCESS;
                }
            }
        } catch (\Exception $e) {
            Log::log("error message", $e->getMessage());
        }
        return ResultCode::FAILED_UNKNOWN;
    }

    public static function reportRevenue($listUserIds, $day = null, $month = null, $year = null)
    {
        $filterOptions = [];
        $query = DB::table("marketing_products");
        $query->select("users.username as username","bank_accounts.name as bank_account_name", DB::raw('SUM(campaigns.budget) as total_budget'));
        $query->join("campaigns", "marketing_products.id","=", "campaigns.marketing_product_id");
        $query->join("bank_accounts", "campaigns.bank_account_id", "=", "bank_accounts.id");
        $query->join("users", "users.id", "=", "marketing_products.user_id");
        $query->groupBy("username", "bank_account_name");
        $query->whereIn("users.id", $listUserIds);
        if($day != null){
            $query->whereDay("marketing_products.created", $day);
        }
        if($month != null){
            $query->whereMonth("marketing_products.created", $month);
        }
        if($year != null){
            $query->whereYear("marketing_products.created", $year);
        }
        $results = $query->where($filterOptions)->get();
        $dataset = [];
        $colHeader = [];
        $rowHeader = [];
        foreach ($results as $row){
            $bankAccount = $row->bank_account_name;
            $owner = $row->username;
            if(!array_key_exists($owner, $dataset)){
                $dataset[$owner] = [];
            }
            $dataset[$owner][$bankAccount] = $row->total_budget;
            if(!in_array(strval($bankAccount), $colHeader)){
                array_push($colHeader,strval($bankAccount));
            }
            if(!in_array(strval($owner), $rowHeader)){
                array_push($rowHeader,strval($owner));
            }
        }
        foreach ($rowHeader as $row){
            foreach ($colHeader as $col){
                if(!array_key_exists($col, $dataset[$row])){
                    $dataset[$row][$col] = 0;
                }
            }
        }
        $revenueReport = new \stdClass();
        $revenueReport->col_names = $colHeader;
        $revenueReport->row_names = $rowHeader;
        $revenueReport->dataset = $dataset;
        return $revenueReport;
    }


}

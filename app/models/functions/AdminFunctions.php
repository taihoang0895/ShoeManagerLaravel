<?php


namespace App\models\functions;


use App\models\CampaignName;
use App\models\DetailProduct;
use App\models\Discount;
use App\models\functions\rows\ProductRow;
use App\models\LandingPage;
use App\models\Product;
use App\models\ProductCategory;
use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminFunctions
{


    public static function saveCampaignName($campaignNameRow)
    {
        $campaignName = CampaignName::where("id", $campaignNameRow->id)->first();
        if ($campaignName == null) {
            $campaignName = new CampaignName();
            $campaignName->created = Util::now();
        }

        $campaignName->name = $campaignNameRow->name;

        if ($campaignName->save()) {
            return ResultCode::SUCCESS;
        }

        return ResultCode::FAILED_UNKNOWN;
    }

    public
    static function deleteCampaignName($id)
    {
        $campaignName = CampaignName::where('id', $id)->first();
        if ($campaignName != null) {
            $campaignName->is_active = false;
            if ($campaignName->save()) {
                return ResultCode::SUCCESS;
            }
        }
        return ResultCode::FAILED_UNKNOWN;
    }

    public
    static function getCampaignName($id)
    {
        $campaignName = CampaignName::where("id", $id)->first();
        if ($campaignName != null) {
            $campaignName->create_str = $campaignName->getCreatedStr();
        }
        return $campaignName;
    }

    public
    static function findCampaignName($campaignName = "")
    {
        $perPage = config('settings.per_page');
        $condition = [];
        if ($campaignName != "") {
            $condition[] = ['name', 'like', '%' . $campaignName . '%'];
        }
        $condition['is_active'] = true;

        $listCampaignName = CampaignName::where($condition)->paginate($perPage);
        return $listCampaignName;
    }

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
        } else {
            $condition = [];
            $condition['username'] = $userInfo->username;
            if (User::where($condition)->first() != null) {
                return ResultCode::FAILED_USER_DUPLICATE_USERNAME;
            }
        }


        $user->username = $userInfo->username;
        $user->password = Hash::make($userInfo->password);
        $user->alias_name = $userInfo->alias_name;
        $user->department = User::parseDepartmentName($userInfo->department_name);
        $user->is_active = true;
        $user->role = User::parseRoleName($userInfo->role_name);
        if ($user->save()) {
            return ResultCode::SUCCESS;
        }
        return ResultCode::FAILED_UNKNOWN;
    }

    public static function findUsers($searchUsername = "", $excludeUser = [])
    {
        $perPage = config('settings.per_page');
        $condition = [];
        $condition[] = ['is_active', true];
        if ($searchUsername != "") {
            $condition[] = ['username', 'like', '%' . $searchUsername . '%'];
        }

        return User::where($condition)->whereNotIn('id', $excludeUser)->paginate($perPage);
    }

    public static function getUser($userId)
    {
        $user = User::where('id', $userId)->first();
        if ($user != null) {
            $user->role_name = User::getRoleName($user->role);
            $user->department_name = User::getDepartmentName($user->department);
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
            if ($product->save()) {
                if (DetailProduct::where("product_code", $product->code)->delete() == 0) {
                    throw new \Exception("updateProduct -> delete detail product failed");
                }
                foreach ($listDetailProductInfo as $detailProductInfo) {
                    $productCat = ProductCategory::getOrNew($detailProductInfo->size, $detailProductInfo->color);
                    $detailProduct = new DetailProduct();
                    $detailProduct->product_code = $product->code;
                    $detailProduct->product_category_id = $productCat->id;
                    if (!$detailProduct->save()) {
                        throw new \Exception("updateProduct -> add detail product failed");
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
            DetailProduct::where("product_code", $productCode)->delete();
            Product::where("code", $productCode)->delete();
            return ResultCode::SUCCESS;

        } catch (\Exception $e) {
            Log::log("error message ", $e->getMessage());
            return ResultCode::FAILED_UNKNOWN;
        }
    }

    public static function getProduct($productCode)
    {
        $product = Product::where("code", $productCode)->first();
        if ($product != null) {
            $listDetailProducts = DetailProduct::where("product_code", $productCode)->get();
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
        return Product::where($condition)->paginate($perPage);
    }

    public static function saveDiscount($discountRow)
    {
        $discount = Discount::where("id", $discountRow->id)->first();
        if ($discount == null) {
            $discount = new Discount();
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
        $condition = [];
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
            if (Discount::where("id", $discountId)->delete()) {
                return ResultCode::SUCCESS;
            }
        } catch (\Exception $e) {
            Log::log("error message ", $e->getMessage());
        }
        return ResultCode::FAILED_UNKNOWN;
    }
}
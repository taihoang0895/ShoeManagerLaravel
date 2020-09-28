<?php


namespace App\models\functions;


use App\models\CampaignName;
use App\models\Customer;
use App\models\CustomerState;
use App\models\DetailOrder;
use App\models\DetailProduct;
use App\models\Discount;
use App\models\functions\rows\ProductRow;
use App\models\LandingPage;
use App\models\MarketingProduct;
use App\models\Order;
use App\models\Product;
use App\models\ProductCategory;
use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use mysql_xdevapi\Exception;

class AdminFunctions
{
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
            if ($user->password != $userInfo->password) {
                $user->password = Hash::make($userInfo->password);
            }
        } else {
            $condition = [];
            $condition['username'] = $userInfo->username;
            if (User::where($condition)->first() != null) {
                return ResultCode::FAILED_USER_DUPLICATE_USERNAME;
            }
            $user->password = Hash::make($userInfo->password);
        }


        $user->username = $userInfo->username;

        $user->alias_name = $userInfo->alias_name;
        $user->department = User::parseDepartmentName($userInfo->department_name);
        $user->is_active = true;
        $user->role = User::parseRoleName($userInfo->role_name);
        if ($user->save()) {
            return ResultCode::SUCCESS;
        }
        return ResultCode::FAILED_UNKNOWN;
    }

    public static function findUsers($user, $searchUsername = "", $excludeUser = [])
    {
        array_push($excludeUser, $user->id);
        $perPage = config('settings.per_page');
        $condition = [];
        $condition[] = ['is_active', true];
        if ($searchUsername != "") {
            $condition[] = ['username', 'like', '%' . $searchUsername . '%'];
        }

        $listUsers = User::where($condition)->whereNotIn('id', $excludeUser)->paginate($perPage);
        foreach ($listUsers as $user) {
            $user->department_name = $user->getDepartmentName();
            $user->role_name = User::getRoleName($user->role);
        }
        return $listUsers;
    }

    public static function getUser($userId)
    {
        $user = User::where('id', $userId)->first();
        if ($user != null) {
            $user->role_name = User::getRoleName($user->role);
            $user->department_name = $user->getDepartmentName();
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
            $product->is_test = $productInfo->is_test;
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

    private static function inListDetailProduct($listDetailProduct, $size, $color)
    {
        foreach ($listDetailProduct as $detailProduct) {
            if (strcmp($detailProduct->size, $size) == 0 and strcmp($detailProduct->color, $color) == 0) {
                return true;
            }
        }
        return false;
    }

    private static function attachExtraPropertyDetailProduct($detailProduct)
    {
        $productCat = ProductCategory::where("id", $detailProduct->product_category_id)->first();
        if ($productCat != null) {
            $detailProduct->size = $productCat->size;
            $detailProduct->color = $productCat->color;
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
            $product->is_test = $productInfo->is_test;
            if ($product->save()) {
                $listOldDetailProducts = DetailProduct::where("product_code", $product->code)->get();
                foreach ($listOldDetailProducts as $oldDetailProduct) {
                    self::attachExtraPropertyDetailProduct($oldDetailProduct);
                    if (!self::inListDetailProduct($listDetailProductInfo, $oldDetailProduct->size, $oldDetailProduct->color)) {
                        if (!$oldDetailProduct->delete()) {
                            throw new \Exception("updateProduct -> delete detail product failed");
                        }
                    }
                }
                foreach ($listDetailProductInfo as $detailProductInfo) {
                    if (!self::inListDetailProduct($listOldDetailProducts, $detailProductInfo->size, $detailProductInfo->color)) {
                        $productCat = ProductCategory::getOrNew($detailProductInfo->size, $detailProductInfo->color);
                        $detailProduct = new DetailProduct();
                        $detailProduct->product_code = $product->code;
                        $detailProduct->product_category_id = $productCat->id;
                        if (!$detailProduct->save()) {
                            throw new \Exception("updateProduct -> add detail product failed");
                        }
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
            $product = Product::where("code", $productCode)->first();
            if ($product != null) {
                $product->is_active = false;
                if ($product->save()) {
                    return ResultCode::SUCCESS;
                }
            }
        } catch (\Exception $e) {
            Log::log("error message ", $e->getMessage());
        }
        return ResultCode::FAILED_UNKNOWN;
    }

    public static function getProduct($productCode)
    {
        $product = Product::where("code", $productCode)->first();
        if ($product != null) {
            $listDetailProducts = DetailProduct::where("product_code", $productCode)->get();
            foreach ($listDetailProducts as $detailProduct) {
                $productCategory = ProductCategory::where("id", $detailProduct->product_category_id)->first();
                $detailProduct->color = $productCategory->color;
                $detailProduct->size = $productCategory->size;
            }
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
        return Product::where($condition)->where("is_active", true)->paginate($perPage);
    }

    public static function saveDiscount($discountRow)
    {
        $discount = Discount::where("id", $discountRow->id)->first();
        if ($discount == null) {
            $discount = new Discount();
            $discount->discount_value = $discountRow->discount_value;
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
        $condition = [
            "is_active" => true
        ];
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
            $discount = Discount::where("id", $discountId)->first();
            if ($discount != null) {
                $discount->is_active = false;
                if ($discount->save()) {
                    return ResultCode::SUCCESS;
                }
            }
        } catch (\Exception $e) {
            Log::log("error message ", $e->getMessage());
        }
        return ResultCode::FAILED_UNKNOWN;
    }

    public static function syncCustomerState()
    {
        DB::beginTransaction();
        try {
            $listCustomers = Customer::all();
            foreach ($listCustomers as $customer) {
                if (Order::existCustomer($customer->id)) {
                    $customer->customer_state = CustomerState::STATE_CUSTOMER_ORDER_CREATED;
                    if (!$customer->save()) {
                        throw new \Exception("failed");
                    }
                }
            }

            DB::commit();
            return ResultCode::SUCCESS;
        } catch (\Exception $e) {
            Log::log("error message", $e->getMessage());
            DB::rollBack();
        }
        return ResultCode::FAILED_UNKNOWN;

    }

    public static function syncCustomerSource()
    {

        DB::beginTransaction();
        try {
            $listDetailOrders = DetailOrder::all();
            foreach ($listDetailOrders as $detailOrder) {
                $order = Order::where("id", $detailOrder->order_id)->first();
                $customer = Customer::where("id", $order->customer_id)->first();
                $productCode = "";
                if ($detailOrder->marketing_product_id != null) {

                    $marketingProduct = MarketingProduct::get($detailOrder->marketing_product_id, $order->created);
                    if ($marketingProduct == null) {
                        $productCode = DetailProduct::getProductCode($detailOrder->detail_product_id);
                    } else {
                        $productCode = $marketingProduct->code;
                    }
                } else {
                    $productCode = DetailProduct::getProductCode($detailOrder->detail_product_id);
                }
                if ($productCode == "") {
                    continue;
                }
                $resultCode = SaleFunctions::saveCustomerSource($customer->id, [$productCode], $order->created);
                if ($resultCode != ResultCode::SUCCESS) {
                    throw new \Exception($resultCode);
                }
            }

            DB::commit();
            return ResultCode::SUCCESS;
        } catch (\Exception $e) {
            Log::log("TAIH", $e->getMessage());
            DB::rollBack();
            return $e->getMessage();

        }
        return ResultCode::FAILED_UNKNOWN;
    }


}

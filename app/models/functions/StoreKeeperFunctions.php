<?php


namespace App\models\functions;


use App\models\DetailProduct;
use App\models\FailedProduct;
use App\models\HistoryFailedProduct;
use App\models\HistoryImportingProduct;
use App\models\HistoryReturningProduct;
use App\models\ImportingProduct;
use App\models\Inventory;
use App\models\Product;
use App\models\ProductCategory;
use App\models\ReturningProduct;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class StoreKeeperFunctions
{

    public static function listProductCodes()
    {
        $listProductCodes = [];
        foreach (DB::table('products')
                     ->where("is_active", true)
                     ->where("is_test", false)
                     ->where("storage_id", Util::getCurrentStorageId())
                     ->distinct()->get(['code']) as $productCat) {
            array_push($listProductCodes, $productCat->code);
        }
        return $listProductCodes;

    }


    public static function findImportingProducts($listUserIds, $filterTime = null, $filterProductCode = "___",
                                                 $filterProductSize = "___", $filterProductColor = "___")
    {
        $listImportingProducts = DB::table('importing_products');
        $filterOptions = [];
        if ($filterProductCode != "" && $filterProductCode != "___") {
            $filterOptions[] = ['detail_products.product_code', 'like', '%' . $filterProductCode . '%'];
        }
        if ($filterProductSize != "" && $filterProductSize != "___") {
            $filterOptions[] = ['product_categories.size', $filterProductSize];
        }
        if ($filterProductColor != "" && $filterProductColor != "___") {
            $filterOptions[] = ['product_categories.color', $filterProductColor];
        }
        $detailProducts = DB::table('detail_products')
            ->select("detail_products.id as id", "product_categories.size as product_size", "product_categories.color as product_color", "detail_products.product_code as product_code")
            ->join('product_categories', 'product_categories.id', "=", "detail_products.product_category_id")
            ->where($filterOptions);

        $listImportingProducts->joinSub($detailProducts, 'detail_products', 'importing_products.detail_product_id', "=", "detail_products.id");


        if ($filterTime != null) {
            $listImportingProducts->whereDay('importing_products.created', '=', $filterTime->day);
            $listImportingProducts->whereMonth('importing_products.created', '=', $filterTime->month);
            $listImportingProducts->whereYear('importing_products.created', '=', $filterTime->year);
        }
        $perPage = config('settings.per_page');
        $result = $listImportingProducts
            ->select("importing_products.id", "product_size", "product_color", "note", "created", "quantity", "product_code")
            ->whereIn("user_id", $listUserIds)
            ->orderBy("importing_products.created", "DESC")->paginate($perPage);

        foreach ($result as $importingProduct) {
            $importingProduct->created_str = Util::formatDate(Util::convertDateTimeSql($importingProduct->created));
        }

        return $result;
    }

    public static function getImportingProduct($id)
    {
        $importingProduct = ImportingProduct::where("id", $id)->first();
        if ($importingProduct != null) {
            $detailProduct = DetailProduct::where("id", $importingProduct->detail_product_id)->first();
            $productCode = "";
            $productColor = "";
            $productSize = "";
            if ($detailProduct != null) {
                $productCode = $detailProduct->product_code;
                $productCat = ProductCategory::where("id", $detailProduct->product_category_id)->first();
                if ($productCat != null) {
                    $productColor = $productCat->color;
                    $productSize = $productCat->size;
                }
            }
            $importingProduct->product_code = $productCode;
            $importingProduct->product_color = $productColor;
            $importingProduct->product_size = $productSize;
        }
        return $importingProduct;
    }

    public static function saveImportingProduct($user, $importingProductInfo, $createTransaction = true)
    {
        if ($createTransaction) {
            DB::beginTransaction();
        }
        try {
            $productCat = ProductCategory::get($importingProductInfo->size, $importingProductInfo->color);
            if ($productCat == null) {
                return ResultCode::FAILED_UNKNOWN;
            }
            $condition = [];
            $condition[] = ["product_code", $importingProductInfo->product_code];
            $condition[] = ["product_category_id", $productCat->id];
            $detailProduct = DetailProduct::where($condition)->first();
            if ($detailProduct == null) {
                return ResultCode::FAILED_UNKNOWN;
            }


            $prevDetailProduct = null;
            $prevImportingQuantity = 0;
            $importingProduct = ImportingProduct::where("id", $importingProductInfo->id)->first();
            if ($importingProduct == null) {
                $importingProduct = new ImportingProduct();
                $importingProduct->created = Util::now();
            } else {
                if ($importingProduct->user_id != $user->id) {
                    return ResultCode::FAILED_PERMISSION_DENY;
                }
                $prevImportingQuantity = $importingProduct->quantity;
                $prevDetailProduct = DetailProduct::where("id", $importingProduct->detail_product_id)->first();
            }

            $importingProduct->user_id = $user->id;
            $importingProduct->detail_product_id = $detailProduct->id;
            $importingProduct->quantity = $importingProductInfo->quantity;
            if ($importingProductInfo->note == null) {
                $importingProductInfo->note = "";
            }
            $importingProduct->note = $importingProductInfo->note;
            $importingProduct->save();


            if ($prevDetailProduct != null && $prevDetailProduct->id != $detailProduct->id) {
                $inventory = Inventory::getOrNew($prevDetailProduct->id);
                $inventory->importing_quantity -= $prevImportingQuantity;
                $inventory->save();

                $inventory = Inventory::getOrNew($detailProduct->id);
                $inventory->importing_quantity += $importingProductInfo->quantity;
                $inventory->save();

            } else {
                $inventory = Inventory::getOrNew($detailProduct->id);
                $result = DB::table("importing_products")
                    ->where("detail_product_id", $detailProduct->id)
                    ->select([DB::raw("SUM(importing_products.quantity) as total_quantity")])
                    ->groupBy("detail_product_id")
                    ->first();
                $inventory->importing_quantity = $result->total_quantity;
                $inventory->save();
            }
            $historyImportingProduct = new HistoryImportingProduct();
            $historyImportingProduct->user_id = $user->id;
            $historyImportingProduct->created = Util::now();
            $historyImportingProduct->importing_product = $importingProduct->encode();
            if ($prevDetailProduct != null) {
                $historyImportingProduct->Action = ActionCode::UPDATE;
            } else {
                $historyImportingProduct->Action = ActionCode::INSERT;
            }
            $historyImportingProduct->save();
            if ($createTransaction) {
                DB::commit();
            }
            return ResultCode::SUCCESS;
        } catch (\Exception $e) {
            Log::log("error mesage", $e->getMessage());
            if ($createTransaction) {
                DB::rollBack();
            }
        }
        return ResultCode::FAILED_UNKNOWN;
    }

    public static function deleteImportingProduct($user, $id)
    {

        DB::beginTransaction();
        try {
            $importingProduct = ImportingProduct::where("id", $id)->first();
            if ($importingProduct == null) {
                return ResultCode::FAILED_UNKNOWN;
            }
            if ($importingProduct->user_id != $user->id) {
                return ResultCode::FAILED_PERMISSION_DENY;
            }
            $inventory = Inventory::getOrNew($importingProduct->detail_product_id);
            $inventory->importing_quantity -= $importingProduct->quantity;
            $inventory->save();
            $importingProduct->delete();

            $historyImportingProduct = new HistoryImportingProduct();
            $historyImportingProduct->user_id = $user->id;
            $historyImportingProduct->created = Util::now();
            $historyImportingProduct->importing_product = $importingProduct->encode();
            $historyImportingProduct->Action = ActionCode::DELETE;
            $historyImportingProduct->save();
            DB::commit();
            return ResultCode::SUCCESS;
        } catch (\Exception $e) {
            Log::log("error mesage", $e->getMessage());
            DB::rollBack();
        }
        return ResultCode::FAILED_UNKNOWN;
    }


    public static function findReturningProducts($listUserIds, $filterTime = null, $filterProductCode = "___",
                                                 $filterProductSize = "___", $filterProductColor = "___")
    {
        $listReturningProducts = DB::table('returning_products');
        $filterOptions = [];
        if ($filterProductCode != "" && $filterProductCode != "___") {
            $filterOptions[] = ['detail_products.product_code', 'like', '%' . $filterProductCode . '%'];
        }
        if ($filterProductSize != "" && $filterProductSize != "___") {
            $filterOptions[] = ['product_categories.size', $filterProductSize];
        }
        if ($filterProductColor != "" && $filterProductColor != "___") {
            $filterOptions[] = ['product_categories.color', $filterProductColor];
        }
        $detailProducts = DB::table('detail_products')
            ->select("detail_products.id as id", "product_categories.size as product_size", "product_categories.color as product_color", "detail_products.product_code as product_code")
            ->join('product_categories', 'product_categories.id', "=", "detail_products.product_category_id")
            ->where($filterOptions);

        $listReturningProducts->joinSub($detailProducts, 'detail_products', 'returning_products.detail_product_id', "=", "detail_products.id");


        if ($filterTime != null) {
            $listReturningProducts->whereDay('returning_products.created', '=', $filterTime->day);
            $listReturningProducts->whereMonth('returning_products.created', '=', $filterTime->month);
            $listReturningProducts->whereYear('returning_products.created', '=', $filterTime->year);
            $listReturningProducts->whereYear('returning_products.created', '=', $filterTime->year);
            $listReturningProducts->whereYear('returning_products.created', '=', $filterTime->year);
        }
        $perPage = config('settings.per_page');
        $result = $listReturningProducts
            ->select("returning_products.id", "product_size", "product_color", "note", "created", "quantity", "product_code")
            ->whereIn("user_id", $listUserIds)->paginate($perPage);

        foreach ($result as $returningProduct) {
            $returningProduct->created_str = Util::formatDate(Util::convertDateTimeSql($returningProduct->created));
        }

        return $result;
    }

    public static function getReturningProduct($id)
    {
        $returningProduct = ReturningProduct::where("id", $id)->first();
        if ($returningProduct != null) {
            $detailProduct = DetailProduct::where("id", $returningProduct->detail_product_id)->first();
            $productCode = "";
            $productColor = "";
            $productSize = "";
            if ($detailProduct != null) {
                $productCode = $detailProduct->product_code;
                $productCat = ProductCategory::where("id", $detailProduct->product_category_id)->first();
                if ($productCat != null) {
                    $productColor = $productCat->color;
                    $productSize = $productCat->size;
                }
            }
            $returningProduct->product_code = $productCode;
            $returningProduct->product_color = $productColor;
            $returningProduct->product_size = $productSize;
        }
        return $returningProduct;
    }

    public static function saveReturningProduct($user, $returningProductInfo, $createTransaction = true)
    {
        if ($createTransaction) {
            DB::beginTransaction();
        }

        try {
            $productCat = ProductCategory::get($returningProductInfo->size, $returningProductInfo->color);
            if ($productCat == null) {
                return ResultCode::FAILED_UNKNOWN;
            }

            $condition = [];
            $condition[] = ["product_code", $returningProductInfo->product_code];
            $condition[] = ["product_category_id", $productCat->id];
            $detailProduct = DetailProduct::where($condition)->first();
            if ($detailProduct == null) {
                return ResultCode::FAILED_UNKNOWN;
            }


            $prevDetailProduct = null;
            $prevReturningQuantity = 0;
            $returningProduct = ReturningProduct::where("id", $returningProductInfo->id)->first();
            if ($returningProduct == null) {
                $returningProduct = new ReturningProduct();
                $returningProduct->created = Util::now();
            } else {
                if ($returningProduct->user_id != $user->id) {
                    return ResultCode::FAILED_PERMISSION_DENY;
                }
                $prevReturningQuantity = $returningProduct->quantity;
                $prevDetailProduct = DetailProduct::where("id", $returningProduct->detail_product_id)->first();
            }

            $returningProduct->user_id = $user->id;
            $returningProduct->detail_product_id = $detailProduct->id;
            $returningProduct->quantity = $returningProductInfo->quantity;
            if ($returningProductInfo->note == null) {
                $returningProductInfo->note = "";
            }
            $returningProduct->note = $returningProductInfo->note;
            $returningProduct->save();


            if ($prevDetailProduct != null && $prevDetailProduct->id != $detailProduct->id) {
                $inventory = Inventory::getOrNew($prevDetailProduct->id);
                $inventory->returning_quantity -= $prevReturningQuantity;
                $inventory->save();

                $inventory = Inventory::getOrNew($detailProduct->id);
                $inventory->returning_quantity += $returningProductInfo->quantity;
                $inventory->save();

            } else {
                $inventory = Inventory::getOrNew($detailProduct->id);

                $result = DB::table("returning_products")
                    ->where("detail_product_id", $detailProduct->id)
                    ->select([DB::raw("SUM(returning_products.quantity) as total_quantity")])
                    ->groupBy("detail_product_id")
                    ->first();
                $inventory->returning_quantity = $result->total_quantity;
                $inventory->save();
            }
            $historyReturningProduct = new HistoryReturningProduct();
            $historyReturningProduct->user_id = $user->id;
            $historyReturningProduct->created = Util::now();
            $historyReturningProduct->returning_product = $returningProduct->encode();
            if ($prevDetailProduct != null) {
                $historyReturningProduct->Action = ActionCode::UPDATE;
            } else {
                $historyReturningProduct->Action = ActionCode::INSERT;
            }
            $historyReturningProduct->save();
            if ($createTransaction) {
                DB::commit();
            }
            return ResultCode::SUCCESS;
        } catch (\Exception $e) {
            Log::log("error mesage", $e->getMessage());
            if ($createTransaction) {
                DB::rollBack();
            }
        }
        return ResultCode::FAILED_UNKNOWN;
    }

    public static function deleteReturningProduct($user, $id)
    {

        DB::beginTransaction();
        try {
            $returningProduct = ReturningProduct::where("id", $id)->first();
            if ($returningProduct == null) {
                return ResultCode::FAILED_UNKNOWN;
            }
            if ($returningProduct->user_id != $user->id) {
                return ResultCode::FAILED_PERMISSION_DENY;
            }
            $inventory = Inventory::getOrNew($returningProduct->detail_product_id);
            $inventory->returning_quantity -= $returningProduct->quantity;
            $inventory->save();
            $returningProduct->delete();

            $historyReturningProduct = new HistoryReturningProduct();
            $historyReturningProduct->user_id = $user->id;
            $historyReturningProduct->created = Util::now();
            $historyReturningProduct->returning_product = $returningProduct->encode();
            $historyReturningProduct->Action = ActionCode::DELETE;
            $historyReturningProduct->save();
            DB::commit();
            return ResultCode::SUCCESS;
        } catch (\Exception $e) {
            Log::log("error mesage", $e->getMessage());
            DB::rollBack();
        }
        return ResultCode::FAILED_UNKNOWN;
    }

    public static function findFailedProducts($listUserIds, $filterTime = null, $filterProductCode = "___",
                                              $filterProductSize = "___", $filterProductColor = "___")
    {
        $listFailedProducts = DB::table('failed_products');
        $filterOptions = [];
        if ($filterProductCode != "" && $filterProductCode != "___") {
            $filterOptions[] = ['detail_products.product_code', 'like', '%' . $filterProductCode . '%'];
        }
        if ($filterProductSize != "" && $filterProductSize != "___") {
            $filterOptions[] = ['product_categories.size', $filterProductSize];
        }
        if ($filterProductColor != "" && $filterProductColor != "___") {
            $filterOptions[] = ['product_categories.color', $filterProductColor];
        }
        $detailProducts = DB::table('detail_products')
            ->select("detail_products.id as id", "product_categories.size as product_size", "product_categories.color as product_color", "detail_products.product_code as product_code")
            ->join('product_categories', 'product_categories.id', "=", "detail_products.product_category_id")
            ->where($filterOptions);

        $listFailedProducts->joinSub($detailProducts, 'detail_products', 'failed_products.detail_product_id', "=", "detail_products.id");


        if ($filterTime != null) {
            $listFailedProducts->whereDay('failed_products.created', '=', $filterTime->day);
            $listFailedProducts->whereMonth('failed_products.created', '=', $filterTime->month);
            $listFailedProducts->whereYear('failed_products.created', '=', $filterTime->year);
            $listFailedProducts->whereYear('failed_products.created', '=', $filterTime->year);
            $listFailedProducts->whereYear('failed_products.created', '=', $filterTime->year);
        }
        $perPage = config('settings.per_page');
        $result = $listFailedProducts
            ->select("failed_products.id", "product_size", "product_color", "note", "created", "quantity", "product_code")
            ->whereIn("user_id", $listUserIds)->paginate($perPage);

        foreach ($result as $failedProduct) {
            $failedProduct->created_str = Util::formatDate(Util::convertDateTimeSql($failedProduct->created));
        }

        return $result;
    }

    public static function getFailedProduct($id)
    {
        $failedProduct = FailedProduct::where("id", $id)->first();
        if ($failedProduct != null) {
            $detailProduct = DetailProduct::where("id", $failedProduct->detail_product_id)->first();
            $productCode = "";
            $productColor = "";
            $productSize = "";
            if ($detailProduct != null) {
                $productCode = $detailProduct->product_code;
                $productCat = ProductCategory::where("id", $detailProduct->product_category_id)->first();
                if ($productCat != null) {
                    $productColor = $productCat->color;
                    $productSize = $productCat->size;
                }
            }
            $failedProduct->product_code = $productCode;
            $failedProduct->product_color = $productColor;
            $failedProduct->product_size = $productSize;
        }
        return $failedProduct;
    }

    public static function saveFailedProduct($user, $failedProductInfo, $createTransaction = true)
    {
        if ($createTransaction) {
            DB::beginTransaction();
        }
        try {
            $productCat = ProductCategory::get($failedProductInfo->size, $failedProductInfo->color);
            if ($productCat == null) {
                return ResultCode::FAILED_UNKNOWN;
            }

            $condition = [];
            $condition[] = ["product_code", $failedProductInfo->product_code];
            $condition[] = ["product_category_id", $productCat->id];
            $detailProduct = DetailProduct::where($condition)->first();
            if ($detailProduct == null) {
                return ResultCode::FAILED_UNKNOWN;
            }


            $prevDetailProduct = null;
            $prevFailedQuantity = 0;
            $failedProduct = FailedProduct::where("id", $failedProductInfo->id)->first();
            if ($failedProduct == null) {
                $failedProduct = new FailedProduct();
                $failedProduct->created = Util::now();
            } else {
                if ($failedProduct->user_id != $user->id) {
                    return ResultCode::FAILED_PERMISSION_DENY;
                }
                $prevFailedQuantity = $failedProduct->quantity;
                $prevDetailProduct = DetailProduct::where("id", $failedProduct->detail_product_id)->first();
            }

            $failedProduct->user_id = $user->id;
            $failedProduct->detail_product_id = $detailProduct->id;
            $failedProduct->quantity = $failedProductInfo->quantity;
            if ($failedProductInfo->note == null) {
                $failedProductInfo->note = "";
            }
            $failedProduct->note = $failedProductInfo->note;
            $failedProduct->save();


            if ($prevDetailProduct != null && $prevDetailProduct->id != $detailProduct->id) {
                $inventory = Inventory::getOrNew($prevDetailProduct->id);
                $inventory->failed_quantity -= $prevFailedQuantity;
                $inventory->save();

                $inventory = Inventory::getOrNew($detailProduct->id);
                $inventory->failed_quantity += $failedProductInfo->quantity;
                $inventory->save();

            } else {
                $inventory = Inventory::getOrNew($detailProduct->id);

                $result = DB::table("failed_products")
                    ->where("detail_product_id", $detailProduct->id)
                    ->select([DB::raw("SUM(failed_products.quantity) as total_quantity")])
                    ->groupBy("detail_product_id")
                    ->first();
                $inventory->failed_quantity = $result->total_quantity;
                $inventory->save();
            }
            $historyFailedProduct = new HistoryFailedProduct();
            $historyFailedProduct->user_id = $user->id;
            $historyFailedProduct->created = Util::now();
            $historyFailedProduct->failed_product = $failedProduct->encode();
            if ($prevDetailProduct != null) {
                $historyFailedProduct->action = ActionCode::UPDATE;
            } else {
                $historyFailedProduct->action = ActionCode::INSERT;
            }
            $historyFailedProduct->save();
            if ($createTransaction) {
                DB::commit();
            }
            return ResultCode::SUCCESS;
        } catch (\Exception $e) {
            Log::log("error mesage", $e->getMessage());
            if ($createTransaction) {
                DB::rollBack();
            }
        }
        return ResultCode::FAILED_UNKNOWN;
    }

    public static function deleteFailedProduct($user, $id)
    {

        DB::beginTransaction();
        try {
            $failedProduct = FailedProduct::where("id", $id)->first();
            if ($failedProduct == null) {
                return ResultCode::FAILED_UNKNOWN;
            }
            if ($failedProduct->user_id != $user->id) {
                return ResultCode::FAILED_PERMISSION_DENY;
            }
            $inventory = Inventory::getOrNew($failedProduct->detail_product_id);
            $inventory->failed_quantity -= $failedProduct->quantity;
            $inventory->save();
            $failedProduct->delete();

            $historyFailedProduct = new HistoryFailedProduct();
            $historyFailedProduct->user_id = $user->id;
            $historyFailedProduct->created = Util::now();
            $historyFailedProduct->failed_product = $failedProduct->encode();
            $historyFailedProduct->action = ActionCode::DELETE;
            $historyFailedProduct->save();
            DB::commit();
            return ResultCode::SUCCESS;
        } catch (\Exception $e) {
            Log::log("error mesage", $e->getMessage());
            DB::rollBack();
        }
        return ResultCode::FAILED_UNKNOWN;
    }

    public static function reportInventory($user)
    {
        $extrasWhere = "";
        if (!$user->isMarketing() && !$user->isAdmin()) {
            $extrasWhere = "and products.storage_id=" . Util::getCurrentStorageId();
        }

        $sql = "SELECT " .
            "         detail_products.product_code as product_code," .
            "         product_categories.size as product_size," .
            "         product_categories.color as product_color," .
            "         inventories.importing_quantity as importing_quantity," .
            "         inventories.exporting_quantity as exporting_quantity," .
            "         inventories.returning_quantity as returning_quantity," .
            "         inventories.failed_quantity as failed_quantity" .
            " FROM detail_products" .
            " INNER JOIN product_categories ON detail_products.product_category_id=product_categories.id" .
            " INNER JOIN products ON detail_products.product_code=products.code" .
            " LEFT JOIN inventories ON detail_products.id=inventories.detail_product_id" .
            " WHERE products.is_active=1 and products.is_test=0 " . $extrasWhere .
            " ORDER BY product_code,product_size";

        $listInventoryReports = array();
        $results = DB::select(DB::raw($sql));
        foreach ($results as $inventoryReport) {
            $inventoryReport->remaining_quantity = $inventoryReport->importing_quantity + $inventoryReport->returning_quantity - $inventoryReport->exporting_quantity;
            array_push($listInventoryReports, $inventoryReport);
        }
        return $listInventoryReports;
    }

    public static function listDetailProducts()
    {
        return DB::table('detail_products')
            ->join('product_categories', 'product_categories.id', "=", "detail_products.product_category_id")
            ->join('products', 'detail_products.product_code', "=", "products.code")
            ->where("products.is_active", true)
            ->where("products.is_test", false)
            ->where("products.storage_id", Util::getCurrentStorageId())
            ->select("detail_products.id as id", "detail_products.product_code as product_code", "product_categories.size as size", "product_categories.color as color")
            ->get();
    }

    public static function reportProduct($tabIndex, $startTime = null, $endTime = null)
    {
        $filterOptions = [];
        if ($startTime != null && $endTime != null) {
            $filterOptions[] = ['created', '>=', $startTime];
            $filterOptions[] = ['created', '<=', $endTime];
        }
        $tableName = "importing_products";
        switch ($tabIndex) {
            case 0:
                break;
            case 1:
                $tableName = "detail_orders";
                break;
            case 2:
                $tableName = "returning_products";
                break;
            case 3:
                $tableName = "failed_products";
                break;
        }
        $query = DB::table($tableName);
        if ($tabIndex == 1) {
            $query->join("orders", 'detail_orders.order_id', "=", "orders.id")
                ->select(DB::raw("detail_orders.detail_product_id as detail_product_id"),
                    DB::raw('SUM(detail_orders.quantity) as total_quantity'),
                    DB::raw('DATE(orders.created) as day'))
                ->join('detail_products', "detail_orders.detail_product_id", "=", "detail_products.id")
                ->join('products', "detail_products.product_code", "=", "products.code")
                ->where("products.is_active", true)
                ->where("products.is_test", false)
                ->where("products.storage_id", Util::getCurrentStorageId())
                ->where("orders.is_test", false);
        } else {
            $query->select("detail_product_id", DB::raw('SUM(' . $tableName . '.quantity) as total_quantity'), DB::raw('DATE(' . $tableName . '.created) as day'))
                ->join('detail_products', $tableName . ".detail_product_id", "=", "detail_products.id")
                ->join('products', "detail_products.product_code", "=", "products.code")
                ->where("products.is_active", true)
                ->where("products.is_test", false)
                ->where("products.storage_id", Util::getCurrentStorageId());
        }
        $results = $query->where($filterOptions)
            ->groupBy('detail_product_id', 'day')
            ->orderBy('day')
            ->get();
        $listProductReports = [];
        foreach ($results as $importingProductReport) {
            $productReport = new \stdClass();
            $productReport->created_date = Util::formatDate(Util::convertDateSql($importingProductReport->day));
            $productReport->detail_product_id = $importingProductReport->detail_product_id;
            $productReport->quantity = $importingProductReport->total_quantity;

            array_push($listProductReports, $productReport);
        }
        return $listProductReports;
    }

    public static function listImportingExportingHistories($tabIndex, $startTime = null, $endTime = null)
    {
        $tableName = "history_importing_products";
        switch ($tabIndex) {
            case 0:
                break;
            case 1:
                $tableName = "history_exporting_products";
                break;
            case 2:
                $tableName = "history_returning_products";
                break;
            case 3:
                $tableName = "history_failed_products";
                break;
        }
        $filterOptions = [];
        if ($startTime != null && $endTime != null) {
            $filterOptions[] = ['created', '>=', $startTime];
            $filterOptions[] = ['created', '<=', $endTime];
        }
        $query = DB::table($tableName);
        $perPage = config('settings.per_page');
        $results = $query->where($filterOptions)->orderBy("created", "DESC")->paginate($perPage);
        foreach ($results as $historyProduct) {
            $data = [];
            switch ($tabIndex) {
                case 0:
                    $data = json_decode($historyProduct->importing_product);
                    break;
                case 1:
                    $data = json_decode($historyProduct->exporting_product);
                    break;
                case 2:
                    $data = json_decode($historyProduct->returning_product);
                    break;
                case 3:
                    $data = json_decode($historyProduct->failed_product);
                    break;
            }
            $historyProduct->username = User::where("id", $historyProduct->user_id)->first()->username;
            $historyProduct->action = ActionCode::getName($historyProduct->action);
            $historyProduct->date_str = Util::formatDateTime(Util::convertDateTimeSql($historyProduct->created));
            $historyProduct->product_code = $data->product_code;
            $historyProduct->product_size = $data->product_size;
            $historyProduct->product_color = $data->product_color;
            $historyProduct->quantity = $data->quantity;
        }
        return $results;
    }

    public static function checkInventoryIsUnharmed()
    {
        $response = array(
            "status" => 200,
            "exporting_product" => "true",
            "importing_product" => "true",
            "returning_product" => "true",
            "failed_product" => "true",
        );
        //check exporting product
        $result = DB::table("detail_orders")
            ->select("detail_product_id", DB::raw('SUM(quantity) as total_quantity'))
            ->whereNotNull("detail_product_id")
            ->groupBy('detail_product_id')
            ->get();

        foreach ($result as $row) {
            $detailProductId = $row->detail_product_id;
            $inventory = Inventory::get($detailProductId);
            if ($inventory->exporting_quantity != $row->total_quantity) {
                $response['exporting_product'] = "false";
                break;
            }
        }

        //check returning product
        $result = DB::table("returning_products")
            ->select("detail_product_id", DB::raw('SUM(quantity) as total_quantity'))
            ->whereNotNull("detail_product_id")
            ->groupBy('detail_product_id')
            ->get();
        foreach ($result as $row) {
            $detailProductId = $row->detail_product_id;
            $inventory = Inventory::get($detailProductId);
            if ($inventory->returning_quantity != $row->total_quantity) {
                $response['returning_product'] = "false";
                break;
            }
        }

        //check importing product
        $result = DB::table("importing_products")
            ->select("detail_product_id", DB::raw('SUM(quantity) as total_quantity'))
            ->whereNotNull("detail_product_id")
            ->groupBy('detail_product_id')
            ->get();
        foreach ($result as $row) {
            $detailProductId = $row->detail_product_id;
            $inventory = Inventory::get($detailProductId);
            if ($inventory->importing_quantity != $row->total_quantity) {
                $response['importing_product'] = "false";
                break;
            }
        }

        //check failed product
        $result = DB::table("failed_products")
            ->select("detail_product_id", DB::raw('SUM(quantity) as total_quantity'))
            ->whereNotNull("detail_product_id")
            ->groupBy('detail_product_id')
            ->get();
        foreach ($result as $row) {
            $detailProductId = $row->detail_product_id;
            $inventory = Inventory::get($detailProductId);
            if ($inventory->failed_quantity != $row->total_quantity) {
                $response['failed_product'] = "false";
                break;
            }
        }
        return $response;
    }

}

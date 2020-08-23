<?php


namespace App\models\functions;


use App\models\Config;
use App\models\functions\rows\DetailProductRow;
use App\models\Notification;
use App\models\NotificationManager;
use App\models\Product;
use Illuminate\Support\Facades\DB;

class CommonFunctions
{
    public static function searchProduct($productCode)
    {
        return Product::where("code", 'like', '%' . $productCode . '%')->limit(5)->get();
    }

    public static function findDetailProducts($productCode)
    {
        $sql = "SELECT   detail_products.id," .
            "         detail_products.product_code as product_code," .
            "         product_categories.size as product_size," .
            "         product_categories.color as product_color," .
            "         inventories.importing_quantity as importing_quantity," .
            "         inventories.exporting_quantity as exporting_quantity," .
            "         inventories.returning_quantity as returning_quantity," .
            "         inventories.failed_quantity as failed_quantity" .
            " FROM detail_products " .
            " INNER JOIN product_categories ON detail_products.product_category_id =product_categories.id" .
            " LEFT JOIN inventories ON detail_products.id=inventories.detail_product_id " .
            " WHERE product_code =:productCode";

        $listDetailProducts = DB::select($sql, ['productCode' => $productCode]);
        $listDetailProductRow = array();
        foreach ($listDetailProducts as $detailProduct) {
            $detailProductRow = new DetailProductRow();
            $detailProductRow->id = $detailProduct->id;
            $detailProductRow->product_code = $detailProduct->product_code;
            $detailProductRow->size = $detailProduct->product_size;
            $detailProductRow->color = $detailProduct->product_color;
            if ($detailProduct->importing_quantity != null) {
                $detailProductRow->importing_quantity = $detailProduct->importing_quantity;
            } else {
                $detailProductRow->importing_quantity = 0;
            }

            if ($detailProduct->exporting_quantity != null) {
                $detailProductRow->exporting_quantity = $detailProduct->exporting_quantity;
            } else {
                $detailProductRow->exporting_quantity = 0;
            }
            array_push($listDetailProductRow, $detailProductRow);

            $detailProductRow->importing_quantity = $detailProduct->importing_quantity;
            $detailProductRow->remaining_quantity = $detailProduct->importing_quantity - $detailProduct->exporting_quantity;
            $detailProductRow->id = $detailProduct->id;

        }
        return $listDetailProductRow;
    }

    public static function getConfig()
    {
        return Config::getOrNew();
    }

    public static function saveConfig($configInfo)
    {

        $config = Config::getOrNew();
        $config->threshold_bill_cost_green = $configInfo->threshold_bill_cost_green;
        $config->threshold_comment_cost_green = $configInfo->threshold_comment_cost_green;
        if ($config->save()) {
            return ResultCode::SUCCESS;
        }
        return ResultCode::FAILED_UNKNOWN;

    }

    public static function listNotifications($user, $startTime = null)
    {
        $notificationIfo = NotificationManager::getOrNew($user);
        $notificationIfo->has_notification = false;
        $notificationIfo->save();
        $filterOption = [];
        $filterOption['user_id'] = $user->id;
        if ($startTime != null) {
            $filterOption[] = ['created', ">=", $startTime];
        }
        $listNotifications = Notification::where($filterOption)->get();
        return $listNotifications;
    }

    public static function notificationCountUnread($user)
    {
        return NotificationManager::getOrNew($user)->unread_count;
    }

    public static function markNotification($user, $notificationMapUnread)
    {
        DB::beginTransaction();
        try {
            $deltaUnreadMessage = 0;
            foreach ($notificationMapUnread as $notificationId => $unread) {
                $notification = Notification::where("id", $notificationId)->first();
                if ($notification != null) {
                    if ($notification->user_id != $user->id) {
                        throw new \Exception("you can not modify notifications of other");
                    }
                    if ($unread) {
                        $deltaUnreadMessage += 1;
                    } else {
                        $deltaUnreadMessage -= 1;
                    }
                    $notification->unread = $unread;
                    $notification->save();
                }
            }
            $notificationManager = NotificationManager::getOrNew($user);
            $notificationManager->unread_count += $deltaUnreadMessage;
            $notificationManager->save();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
        }

    }

    public static function updateHasNotification($user, $hasNotification)
    {
        $notificationManager = NotificationManager::getOrNew($user);
        $notificationManager->has_notification = $hasNotification;
        $notificationManager->save();
    }

    public static function createNotification($user, $content)
    {
        DB::beginTransaction();
        try {
            $notification = new Notification();
            $notification->user_id = $user->id;
            $notification->content = $content;
            $notification->unread = true;
            $notification->created = Util::now();
            $notification->save();

            $notificationManager = NotificationManager::getOrNew($user);
            $notificationManager->total += 1;
            $notificationManager->has_notification = true;
            $notificationManager->unread_count += 1;
            $notificationManager->save();
            DB::commit();
        } catch (\Exception $e) {
            echo ($e->getMessage());
            DB::rollBack();
        }
    }

    public static function listProductSizes(){
        $listSizes = [];
        foreach (DB::table('product_categories')->distinct()->get(['size']) as $productCat){
            array_push($listSizes, $productCat->size);
        }
        return $listSizes;

    }
    public static function listProductCodes(){
        $listProductCodes = [];
        foreach (DB::table('products')->distinct()->get(['code']) as $productCat){
            array_push($listProductCodes, $productCat->code);
        }
        return $listProductCodes;

    }

    public static function listProductColors(){
        $listColors = [];
        foreach (DB::table('product_categories')->distinct()->get(['color']) as $productCat){
            array_push($listColors, $productCat->color);
        }
        return $listColors;
    }
}

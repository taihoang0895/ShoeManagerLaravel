<?php


namespace App\Http\Controllers;


use App\Http\Controllers\objects\TableCell;
use App\models\functions\CommonFunctions;
use App\models\functions\Log;
use App\models\functions\ResultCode;
use App\models\functions\StoreKeeperFunctions;
use App\models\functions\Util;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StorekeeperController
{
    public function importingProducts(Request $request)
    {
        $filterTimeStr = $request->get('filter_time', "");
        $filterProductCode = $request->get('product_code', "");
        $filterProductSize = $request->get('product_size', "___");
        $filterProductColor = $request->get('product_color', "___");
        $filterTime = Util::safeParseDate($filterTimeStr);
        $listUserIds = [Auth::user()->id];
        $listImportingProducts = StoreKeeperFunctions::findImportingProducts($listUserIds, $filterTime, $filterProductCode, $filterProductSize, $filterProductColor);

        $listProductColors = CommonFunctions::listProductColors();
        $listProductSizes = CommonFunctions::listProductSizes();
        $listProductCodes = json_encode(CommonFunctions::listProductCodes());
        return view("storekeeper.storekeeper_importing_products", [
            "list_importing_products" => $listImportingProducts,
            "list_product_sizes" => $listProductSizes,
            "list_product_colors" => $listProductColors,
            "list_product_codes" => $listProductCodes,
            "filter_time" => $filterTimeStr,
            "filter_product_code" => $filterProductCode,
            "filter_product_size" => $filterProductSize,
            "filter_product_color" => $filterProductColor,

        ]);
    }

    public function formAddImportingProduct(Request $request)
    {
        $response = array(
            "status" => 200,
            "content" => "",
            "message" => ""
        );

        $productSizeSelected = "___";
        $productColorSelected = "___";
        $productCodeSelected = "";

        $listProductColors = CommonFunctions::listProductColors();
        $listProductSizes = CommonFunctions::listProductSizes();
        $listProductCodes = json_encode(CommonFunctions::listProductCodes());
        if (count($listProductSizes) > 0) {
            $productSizeSelected = $listProductSizes[0];
        }
        if (count($listProductColors) > 0) {
            $productColorSelected = $listProductColors[0];
        }


        $emptyImportingProduct = new \stdClass();
        $emptyImportingProduct->id = -1;
        $emptyImportingProduct->quantity = 1;
        $emptyImportingProduct->note = "";

        $response['content'] = view("storekeeper.storekeeper_edit_importing_product", [
            'importing_product' => $emptyImportingProduct,
            "list_product_sizes" => $listProductSizes,
            "list_product_colors" => $listProductColors,
            "list_product_codes" => $listProductCodes,
            "product_size_selected" => $productSizeSelected,
            "product_color_selected" => $productColorSelected,
            "product_code_selected" => $productCodeSelected,
        ])->render();

        return $response;
    }


    public function formUpdateImportingProduct(Request $request)
    {
        $response = array(
            "status" => 406,
            "content" => "",
            "message" => ""
        );

        $id = $request->get('importing_product_id', -1);
        $importingProduct = StoreKeeperFunctions::getImportingProduct($id);
        if ($importingProduct != null) {

            $listProductColors = CommonFunctions::listProductColors();
            $listProductSizes = CommonFunctions::listProductSizes();
            $listProductCodes = json_encode(CommonFunctions::listProductCodes());
            $productSizeSelected = $importingProduct->product_size;
            $productCodeSelected = $importingProduct->product_code;
            $productColorSelected = $importingProduct->product_color;

            $response['status'] = 200;
            $response['content'] = view("storekeeper.storekeeper_edit_importing_product", [
                'importing_product' => $importingProduct,
                "list_product_sizes" => $listProductSizes,
                "list_product_colors" => $listProductColors,
                "list_product_codes" => $listProductCodes,
                "product_size_selected" => $productSizeSelected,
                "product_color_selected" => $productColorSelected,
                "product_code_selected" => $productCodeSelected,
            ])->render();
        }


        return $response;
    }

    public function saveImportingProduct(Request $request)
    {
        $response = array(
            "status" => 200,
            "content" => "",
            "message" => ""
        );

        $id = $request->get("importing_product_id", "");
        $productCode = $request->get("product_code", "");
        $productSize = $request->get("product_size", "");
        $productColor = $request->get("product_color", "");
        $note = $request->get("note", "");
        $quantity = Util::parseInt($request->get("product_quantity", 0));
        if ($quantity > 0 && $productCode != "") {
            $importingProductInfo = new \stdClass();
            $importingProductInfo->id = $id;
            $importingProductInfo->size = $productSize;
            $importingProductInfo->color = $productColor;
            $importingProductInfo->product_code = $productCode;
            $importingProductInfo->note = $note;
            $importingProductInfo->quantity = $quantity;
            $resultCode = StoreKeeperFunctions::saveImportingProduct(Auth::user(), $importingProductInfo);
            if ($resultCode != ResultCode::SUCCESS) {
                $response['status'] = 406;
                $response['message'] = "Lỗi lưu";
            }
        }
        return response()->json($response);
    }

    public function deleteImportingProduct(Request $request)
    {
        $response = array(
            "status" => 200,
            "content" => "",
            "message" => ""
        );
        $id = $request->get('importing_product_id');
        $resultCode = StoreKeeperFunctions::deleteImportingProduct(Auth::user(), $id);
        if ($resultCode != ResultCode::SUCCESS) {
            $response['status'] = 406;
            $response['message'] = "Lỗi xoá";
        }
        return response()->json($response);

    }


    public function returningProducts(Request $request)
    {
        $filterTimeStr = $request->get('filter_time', "");
        $filterProductCode = $request->get('product_code', "");
        $filterProductSize = $request->get('product_size', "___");
        $filterProductColor = $request->get('product_color', "___");
        $filterTime = Util::safeParseDate($filterTimeStr);
        $listUserIds = [Auth::user()->id];
        $listReturningProducts = StoreKeeperFunctions::findReturningProducts($listUserIds, $filterTime, $filterProductCode, $filterProductSize, $filterProductColor);

        $listProductColors = CommonFunctions::listProductColors();
        $listProductSizes = CommonFunctions::listProductSizes();
        $listProductCodes = json_encode(CommonFunctions::listProductCodes());
        return view("storekeeper.storekeeper_returning_products", [
            "list_returning_products" => $listReturningProducts,
            "list_product_sizes" => $listProductSizes,
            "list_product_colors" => $listProductColors,
            "list_product_codes" => $listProductCodes,
            "filter_time" => $filterTimeStr,
            "filter_product_code" => $filterProductCode,
            "filter_product_size" => $filterProductSize,
            "filter_product_color" => $filterProductColor,

        ]);
    }

    public function formAddReturningProduct(Request $request)
    {
        $response = array(
            "status" => 200,
            "content" => "",
            "message" => ""
        );

        $productSizeSelected = "___";
        $productColorSelected = "___";
        $productCodeSelected = "";

        $listProductColors = CommonFunctions::listProductColors();
        $listProductSizes = CommonFunctions::listProductSizes();
        $listProductCodes = json_encode(CommonFunctions::listProductCodes());
        if (count($listProductSizes) > 0) {
            $productSizeSelected = $listProductSizes[0];
        }
        if (count($listProductColors) > 0) {
            $productColorSelected = $listProductColors[0];
        }


        $emptyReturningProduct = new \stdClass();
        $emptyReturningProduct->id = -1;
        $emptyReturningProduct->quantity = 1;
        $emptyReturningProduct->note = "";

        $response['content'] = view("storekeeper.storekeeper_edit_returning_product", [
            'returning_product' => $emptyReturningProduct,
            "list_product_sizes" => $listProductSizes,
            "list_product_colors" => $listProductColors,
            "list_product_codes" => $listProductCodes,
            "product_size_selected" => $productSizeSelected,
            "product_color_selected" => $productColorSelected,
            "product_code_selected" => $productCodeSelected,
        ])->render();

        return $response;
    }


    public function formUpdateReturningProduct(Request $request)
    {
        $response = array(
            "status" => 406,
            "content" => "",
            "message" => ""
        );

        $id = $request->get('returning_product_id', -1);
        $returningProduct = StoreKeeperFunctions::getReturningProduct($id);
        if ($returningProduct != null) {

            $listProductColors = CommonFunctions::listProductColors();
            $listProductSizes = CommonFunctions::listProductSizes();
            $listProductCodes = json_encode(CommonFunctions::listProductCodes());
            $productSizeSelected = $returningProduct->product_size;
            $productCodeSelected = $returningProduct->product_code;
            $productColorSelected = $returningProduct->product_color;

            $response['status'] = 200;
            $response['content'] = view("storekeeper.storekeeper_edit_returning_product", [
                'returning_product' => $returningProduct,
                "list_product_sizes" => $listProductSizes,
                "list_product_colors" => $listProductColors,
                "list_product_codes" => $listProductCodes,
                "product_size_selected" => $productSizeSelected,
                "product_color_selected" => $productColorSelected,
                "product_code_selected" => $productCodeSelected,
            ])->render();
        }


        return $response;
    }

    public function saveReturningProduct(Request $request)
    {
        $response = array(
            "status" => 200,
            "content" => "",
            "message" => ""
        );

        $id = $request->get("returning_product_id", "");
        $productCode = $request->get("product_code", "");
        $productSize = $request->get("product_size", "");
        $productColor = $request->get("product_color", "");
        $note = $request->get("note", "");
        $quantity = Util::parseInt($request->get("product_quantity", 0));
        if ($quantity > 0 && $productCode != "") {
            $returningProductInfo = new \stdClass();
            $returningProductInfo->id = $id;
            $returningProductInfo->size = $productSize;
            $returningProductInfo->color = $productColor;
            $returningProductInfo->product_code = $productCode;
            $returningProductInfo->note = $note;
            $returningProductInfo->quantity = $quantity;
            $resultCode = StoreKeeperFunctions::saveReturningProduct(Auth::user(), $returningProductInfo);
            if ($resultCode != ResultCode::SUCCESS) {
                $response['status'] = 406;
                $response['message'] = "Lỗi lưu";
            }
        }
        return response()->json($response);
    }

    public function deleteReturningProduct(Request $request)
    {
        $response = array(
            "status" => 200,
            "content" => "",
            "message" => ""
        );
        $id = $request->get('returning_product_id');
        $resultCode = StoreKeeperFunctions::deleteReturningProduct(Auth::user(), $id);
        if ($resultCode != ResultCode::SUCCESS) {
            $response['status'] = 406;
            $response['message'] = "Lỗi xoá";
        }
        return response()->json($response);
    }


    public function failedProducts(Request $request)
    {
        $filterTimeStr = $request->get('filter_time', "");
        $filterProductCode = $request->get('product_code', "");
        $filterProductSize = $request->get('product_size', "___");
        $filterProductColor = $request->get('product_color', "___");
        $filterTime = Util::safeParseDate($filterTimeStr);
        $listUserIds = [Auth::user()->id];
        $listFailedProducts = StoreKeeperFunctions::findFailedProducts($listUserIds, $filterTime, $filterProductCode, $filterProductSize, $filterProductColor);

        $listProductColors = CommonFunctions::listProductColors();
        $listProductSizes = CommonFunctions::listProductSizes();
        $listProductCodes = json_encode(CommonFunctions::listProductCodes());
        return view("storekeeper.storekeeper_failed_products", [
            "list_failed_products" => $listFailedProducts,
            "list_product_sizes" => $listProductSizes,
            "list_product_colors" => $listProductColors,
            "list_product_codes" => $listProductCodes,
            "filter_time" => $filterTimeStr,
            "filter_product_code" => $filterProductCode,
            "filter_product_size" => $filterProductSize,
            "filter_product_color" => $filterProductColor,

        ]);
    }

    public function formAddFailedProduct(Request $request)
    {
        $response = array(
            "status" => 200,
            "content" => "",
            "message" => ""
        );

        $productSizeSelected = "___";
        $productColorSelected = "___";
        $productCodeSelected = "";

        $listProductColors = CommonFunctions::listProductColors();
        $listProductSizes = CommonFunctions::listProductSizes();
        $listProductCodes = json_encode(CommonFunctions::listProductCodes());
        if (count($listProductSizes) > 0) {
            $productSizeSelected = $listProductSizes[0];
        }
        if (count($listProductColors) > 0) {
            $productColorSelected = $listProductColors[0];
        }


        $emptyFailedProduct = new \stdClass();
        $emptyFailedProduct->id = -1;
        $emptyFailedProduct->quantity = 1;
        $emptyFailedProduct->note = "";

        $response['content'] = view("storekeeper.storekeeper_edit_failed_product", [
            'failed_product' => $emptyFailedProduct,
            "list_product_sizes" => $listProductSizes,
            "list_product_colors" => $listProductColors,
            "list_product_codes" => $listProductCodes,
            "product_size_selected" => $productSizeSelected,
            "product_color_selected" => $productColorSelected,
            "product_code_selected" => $productCodeSelected,
        ])->render();

        return $response;
    }


    public function formUpdateFailedProduct(Request $request)
    {
        $response = array(
            "status" => 406,
            "content" => "",
            "message" => ""
        );

        $id = $request->get('failed_product_id', -1);
        $failedProduct = StoreKeeperFunctions::getFailedProduct($id);
        if ($failedProduct != null) {

            $listProductColors = CommonFunctions::listProductColors();
            $listProductSizes = CommonFunctions::listProductSizes();
            $listProductCodes = json_encode(CommonFunctions::listProductCodes());
            $productSizeSelected = $failedProduct->product_size;
            $productCodeSelected = $failedProduct->product_code;
            $productColorSelected = $failedProduct->product_color;

            $response['status'] = 200;
            $response['content'] = view("storekeeper.storekeeper_edit_failed_product", [
                'failed_product' => $failedProduct,
                "list_product_sizes" => $listProductSizes,
                "list_product_colors" => $listProductColors,
                "list_product_codes" => $listProductCodes,
                "product_size_selected" => $productSizeSelected,
                "product_color_selected" => $productColorSelected,
                "product_code_selected" => $productCodeSelected,
            ])->render();
        }


        return $response;
    }

    public function saveFailedProduct(Request $request)
    {
        $response = array(
            "status" => 200,
            "content" => "",
            "message" => ""
        );

        $id = $request->get("failed_product_id", "");
        $productCode = $request->get("product_code", "");
        $productSize = $request->get("product_size", "");
        $productColor = $request->get("product_color", "");
        $note = $request->get("note", "");
        $quantity = Util::parseInt($request->get("product_quantity", 0));
        if ($quantity > 0 && $productCode != "") {
            $failedProductInfo = new \stdClass();
            $failedProductInfo->id = $id;
            $failedProductInfo->size = $productSize;
            $failedProductInfo->color = $productColor;
            $failedProductInfo->product_code = $productCode;
            $failedProductInfo->note = $note;
            $failedProductInfo->quantity = $quantity;
            $resultCode = StoreKeeperFunctions::saveFailedProduct(Auth::user(), $failedProductInfo);
            if ($resultCode != ResultCode::SUCCESS) {
                $response['status'] = 406;
                $response['message'] = "Lỗi lưu";
            }
        }
        return response()->json($response);
    }

    public function deleteFailedProduct(Request $request)
    {
        $response = array(
            "status" => 200,
            "content" => "",
            "message" => ""
        );
        $id = $request->get('failed_product_id');
        $resultCode = StoreKeeperFunctions::deleteFailedProduct(Auth::user(), $id);
        if ($resultCode != ResultCode::SUCCESS) {
            $response['status'] = 406;
            $response['message'] = "Lỗi xoá";
        }
        return response()->json($response);
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

        return view("storekeeper.storekeeper_inventory_report", [
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

    public function importingProductReport(Request $request)
    {
        $response = array(
            "status" => 200,
            "content" => "",
            "message" => ""
        );
        $start_time = Util::safeParseDate($request->get('start_time', ''));
        $end_time = Util::safeParseDate($request->get('end_time', ''));
        $listProductReports = StoreKeeperFunctions::reportImportingProduct($start_time, $end_time);
        $listDetailproduct = StoreKeeperFunctions::listDetailProducts();
        $size_cells = [];
        $code_color_cells = [];
        $group_data_by_size = [];
        foreach ($listDetailproduct as $detailproduct) {
            $product_code = $detailproduct->product_code;
            $product_size = $detailproduct->size;
            $product_color = $detailproduct->color;
            $detail_product_id = $detailproduct->id;
            $key = json_encode([$product_code, $product_color]);
            if(!array_key_exists($key, $group_data_by_size)){
                $group_data_by_size[$key] = [];
            }
            $group_data_by_size[$key]
        }


        echo count($listProductReports);
        return response()->json($response);
    }

    public function returningProductReport(Request $request)
    {

    }

    public function failedProductReport(Request $request)
    {

    }

    public function exportingProductReport(Request $request)
    {

    }
}

<?php

namespace App\models;

use App\models\functions\Log;
use App\models\functions\Util;
use Illuminate\Database\Eloquent\Model;


class ProductCategory extends Model
{
    public $timestamps = false;

    //
    public static function getOrNew($productSize, $productColor)
    {
        $productSize = Util::toUpper($productSize);
        $productColor = Util::toUpper($productColor);
        $condition = [
            "size" => $productSize,
            "color" => $productColor
        ];
        $productCat = ProductCategory::where($condition)->first();
        if ($productCat == null) {
            $productCat = new ProductCategory();
            $productCat->size = $productSize;
            $productCat->color = $productColor;
            if (!$productCat->save()) {
                throw new \Exception("getOrNew -> save product category failed ");
            }
        }
        return $productCat;
    }

    public static function get($productSize, $productColor)
    {
        $productSize = Util::toUpper($productSize);
        $productColor = Util::toUpper($productColor);
        $condition = [];
        $condition['size']=$productSize;
        $condition['color']=$productColor;
        return ProductCategory::where($condition)->first();
    }

}

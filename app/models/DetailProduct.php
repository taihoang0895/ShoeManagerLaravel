<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class DetailProduct extends Model
{
    //
    public $timestamps = false;


    public static function getProductCode($id)
    {
        $detailProduct = DetailProduct::where("id", $id)->first();
        if ($detailProduct != null) {
            return $detailProduct->product_code;
        }
        return "";
    }
}

<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class DetailOrder extends Model
{
    //
    public $timestamps = false;


    public function encode()
    {
        $marketingProductId = "";
        $marketingProductCode = "";
        if ($this->marketing_product_id != null) {
            $marketingProduct = MarketingProduct::where("id", $this->marketing_product_id)->first();
            if ($marketingProduct != null) {
                $marketingProductId = $marketingProduct->id;
                $marketingProductCode= $marketingProduct->code;
            }
        }
        $detailProduct = DetailProduct::where("id", $this->detail_product_id)->first();
        if($detailProduct == null){
            return "";
        }
        $productCategory = ProductCategory::where("id", $detailProduct->product_category_id)->first();

        return json_encode([
            "order_id" => $this->id,
            "order_code" => $this->code,
            "marketing_product_id" => $marketingProductId,
            "marketing_product_code" => $marketingProductCode,
            "quantity" => $this->quantity,
            "detail_product_id" => $detailProduct->id,
            "actually_collected" => $this->actually_collected,
            "pick_money" => $this->pick_money,
            "discount_id" => strval($this->discount_id),
            "product_code"=> $detailProduct->product_code,
            "product_size"=> $productCategory->size,
            "product_color"=> $productCategory->color
        ]);
    }
}

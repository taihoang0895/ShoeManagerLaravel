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
        $productSize = "";
        $productColor = "";
        $productCode = "";
        $detailProductId = "";
        if ($this->marketing_product_id != null) {
            $marketingProduct = MarketingProduct::where("id", $this->marketing_product_id)->first();
            if ($marketingProduct != null) {
                $marketingProductId = $marketingProduct->id;
                $marketingProductCode= $marketingProduct->code;
            }
        }
        $detailProduct = DetailProduct::where("id", $this->detail_product_id)->first();
        if($detailProduct != null){
            $productCode = $detailProduct->product_code;
            $detailProductId = $detailProduct->id;
            $productCategory = ProductCategory::where("id", $detailProduct->product_category_id)->first();
            if($productCategory != null){
                $productSize = $productCategory->size;
                $productColor = $productCategory->color;
            }
        }


        return json_encode([
            "order_id" => $this->id,
            "order_code" => $this->code,
            "marketing_product_id" => $marketingProductId,
            "marketing_product_code" => $marketingProductCode,
            "quantity" => $this->quantity,
            "detail_product_id" =>$detailProductId,
            "actually_collected" => $this->actually_collected,
            "pick_money" => $this->pick_money,
            "discount_id" => strval($this->discount_id),
            "product_code"=> $productCode,
            "product_size"=> $productSize,
            "product_color"=> $productColor
        ]);
    }
}

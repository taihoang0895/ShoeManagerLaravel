<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class FailedProduct extends Model
{
    //
    public $timestamps = false;

    public function getCreatedStr()
    {
        if ($this->created != null) {
            return $this->created->format('d/m/Y');
        }
        return "";
    }

    public function encode()
    {
        $detailProduct = DetailProduct::where("id", $this->detail_product_id)->first();
        $productCode = "";
        $productColor = "";
        $productSize = "";
        if ($detailProduct != null) {
            $productCat = ProductCategory::where("id", $detailProduct->product_category_id)->first();
            $productCode = $detailProduct->product_code;
            if ($productCat != null) {
                $productColor = $productCat->color;
                $productSize = $productCat->size;
            }
        }

        return json_encode([
            "detail_product_id" => $this->detail_product_id,
            "quantity" => $this->quantity,
            "note" => $this->note,
            "product_code" => $productCode,
            "product_size" => $productSize,
            "product_color" => $productColor
        ]);
    }
}

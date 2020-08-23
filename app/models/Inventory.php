<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    //
    public $timestamps = false;

    public static function getOrNew($detailProductId)
    {
        $inventory = Inventory::where("detail_product_id", $detailProductId)->first();
        if ($inventory == null) {
            $inventory = new Inventory();
            $inventory->detail_product_id = $detailProductId;
            $inventory->save();
        }
        return $inventory;
    }
}

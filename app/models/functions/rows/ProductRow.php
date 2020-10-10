<?php


namespace App\models\functions\rows;


class ProductRow
{
    function __construct($product, $listDetailProducts) {
        $this->code = $product->code;
        $this->storage_id = $product->storage_id;
        $this->storage_address = $product->storage_address;
        $this->name = $product->name;
        $this->price = $product->price;
        $this->is_test = $product->is_test;
        $this->historical_cost = $product->historical_cost;
        $this->created = $product->created;
        $this->listDetailProducts = $listDetailProducts;
    }

    public function __toString(){
        return json_encode($this);
    }
    }

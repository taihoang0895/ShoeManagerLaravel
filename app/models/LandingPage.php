<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class LandingPage extends Model
{
    protected $dates = ['created'];
    //
    public $timestamps = false;

    public function getCreatedStr(){
        if ($this->created != null) {
            return $this->created->format('d/m/Y h:i:s');
        }


        return "";
    }
    public static function isShopeeSource($name){
        return $name == "Khách hàng đến từ Shopee";
    }
}

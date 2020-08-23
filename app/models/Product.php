<?php

namespace App\models;

use App\models\functions\Util;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    //
    public $timestamps = false;

    public function getPriceStr(){
        return Util::formatMoney($this->price);
    }
    public function getHistoricalCostStr(){
        return Util::formatMoney($this->historical_cost);
    }

}

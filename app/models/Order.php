<?php

namespace App\models;

use App\models\functions\Log;
use App\models\functions\Util;
use App\User;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{

    protected $dates = ['created','delivery_time'];
    //
    public $timestamps = false;

    public function encode(){
            $createdStr = "";
            $deliveryTimeStr = "";
            $user = User::where("id", $this->user_id)->first();
            if ($user == null) {
                return "";
            }
            if($this->created != null){
                $createdStr =  Util::formatDateTime($this->created);
            }
            if($this->delivery_time != null){
                $deliveryTimeStr =  Util::formatDateTime($this->delivery_time);
            }
            return json_encode([
                "id" => $this->id,
                "code" => $this->code,
                "order_state" => $this->order_state,
                "sale_id" => $user->id,
                "sale_name" => $user->username,
                "replace_order_id" => strval($this->replace_order_id),
                "note" => strval($this->note),
                "created" => $createdStr,
                "delivery_time" => $deliveryTimeStr,
                "is_test" => $this->is_test
            ]);
    }
}

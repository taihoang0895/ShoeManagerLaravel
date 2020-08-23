<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class Config extends Model
{
    public $timestamps = false;
    public static function getOrNew(){
        $config = Config::all()->first();
        if ($config == null) {
            $config = new Config();
            $config->threshold_bill_cost_green = 125000;
            $config->threshold_comment_cost_green = 120000;
            $config->save();
        }
        return $config;
    }
}

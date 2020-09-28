<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    //
    public $timestamps = false;
    protected $dates = ['birthday', 'created'];

    public static function get($id)
    {
        return Customer::where("id", $id)->first();
    }

    public static function isDuplicate($code)
    {
        return Customer::where("code", $code)->count() > 1;
    }
}

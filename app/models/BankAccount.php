<?php

namespace App\models;

use App\models\functions\Util;
use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    protected $dates = ['created'];
    //
    public $timestamps = false;

    public function createdStr(){
        return Util::formatDate($this->created);
    }
}

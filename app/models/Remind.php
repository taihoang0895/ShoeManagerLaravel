<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class Remind extends Model
{
    protected $dates = ['time'];
    //
    public $timestamps = false;

    public function timeStr(){
        if ($this->time != null) {
            return $this->time->format('d/m/Y h:i:s');
        }


        return "";
    }
}

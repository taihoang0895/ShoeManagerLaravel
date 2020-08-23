<?php

namespace App\models;

use App\models\functions\Log;
use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    protected $dates = ['start_time',"end_time"];
    //
    public $timestamps = false;

    public function getStartTimeStr()
    {
        if ($this->start_time != null) {
            return $this->start_time->format('d/m/Y');
        }


        return "";
    }

    public function getEndTimeStr()
    {
        if ($this->end_time != null) {
            return $this->end_time->format('d/m/Y');
        }
        return "";
    }
}

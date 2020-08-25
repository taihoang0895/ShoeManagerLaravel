<?php

namespace App\models;

use App\models\functions\Util;
use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    //
    public $timestamps = false;

    public function campaignName(){
        return CampaignName::where("id", $this->campaign_name_id)->first()->name;
    }
    public function bankAccount(){
        return BankAccount::where("id", $this->bank_account_id)->first()->name;
    }
    public function budgetStr(){
        return Util::formatMoney($this->budget);
    }
}

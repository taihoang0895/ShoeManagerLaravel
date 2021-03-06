<?php

namespace App\models;

use App\models\functions\Log;
use App\models\functions\Util;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MarketingProduct extends Model
{
    private $product = "";
    private $marketingSource = "";
    private $_total_bill = null;
    private $_total_phone = null;
    protected $dates = ['created'];
    //
    public $timestamps = false;

    public function sumBudgetStr()
    {
        return Util::formatMoney($this->total_budget);
    }

    public function billCostColor()
    {
        if ($this->billCost() < Config::getOrNew()->threshold_bill_cost_green) {
            return "#93c47d";
        }
        return "red";
    }

    public function commentCostColor()
    {
        if ($this->commentCost() < Config::getOrNew()->threshold_comment_cost_green) {
            return "#93c47d";
        }
        return "red";
    }

    public function createdStr()
    {
        return Util::formatDate($this->created);
    }

    public function productName()
    {
        if ($this->getProduct() != null) {
            return $this->getProduct()->name;
        }
        return "";
    }

    public function sourceName()
    {
        if ($this->getMarketingSource() != null) {
            return $this->getMarketingSource()->name;
        }
        return "";
    }

    public function price()
    {
        if ($this->getProduct() != null) {
            return $this->getProduct()->price;
        }
        return "";
    }

    public function commentCost()
    {
        if ($this->total_comment > 0) {
            return (int)($this->total_budget * 1.0 / $this->total_comment);
        }
        return $this->total_budget;

    }

    public function totalBill()
    {
        if ($this->_total_bill == null) {
            $this->_total_bill = DB::table("customer_sources")
                ->whereDate("customer_sources.created", $this->created)
                ->join("customers", "customer_sources.customer_id", "=", "customers.id")
                ->where("marketing_product_id", $this->id)
                ->where("customer_state", CustomerState::STATE_CUSTOMER_ORDER_CREATED)
                ->count();
        }
        return $this->_total_bill;
    }

    public function totalPhone()
    {
        if ($this->_total_phone == null) {

            $this->_total_phone = DB::table("customer_sources")
                ->whereDate("customer_sources.created", $this->created)
                ->where("marketing_product_id", $this->id)
                ->count();
        }
        return $this->_total_phone;
    }

    public function totalBudgetStr()
    {
        return Util::formatMoney($this->total_budget);
    }

    public function billCost()
    {
        if ($this->totalBill() > 0) {
            return (int)($this->total_budget * 1.0 / $this->totalBill());
        }
        return $this->total_budget;

    }


    public function totalBillStr()
    {
        return Util::formatMoney($this->totalBill());
    }

    public function commentCostStr()
    {
        return Util::formatMoney($this->commentCost());
    }

    public function billCostStr()
    {
        return Util::formatMoney($this->billCost());
    }

    public function priceStr()
    {
        return Util::formatMoney($this->price());
    }

    public function username()
    {
        return User::where("id", $this->user_id)->first()->name;
    }


    private function getProduct()
    {
        if ($this->product == "") {
            $this->product = Product::where("code", $this->product_code)->first();
        }
        return $this->product;
    }

    private function getMarketingSource()
    {
        if ($this->marketingSource == "") {
            $this->marketingSource = MarketingSource::where("id", $this->marketing_source_id)->first();
        }
        return $this->marketingSource;
    }

    public static function find($code, $date = null)
    {
        $query = MarketingProduct::where("code", $code);
        if ($date != null) {
            $query->whereDate("created", $date);
        }
        return $query->first();
    }

    public static function get($id, $date = null)
    {
        $query = MarketingProduct::where("id", $id);
        if ($date != null) {
            $query->whereDate("created", $date);
        }
        return $query->first();
    }


}

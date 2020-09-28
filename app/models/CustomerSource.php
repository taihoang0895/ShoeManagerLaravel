<?php


namespace App\models;


use Illuminate\Database\Eloquent\Model;

class CustomerSource extends Model
{
    protected $dates = ['created'];
    public $timestamps = false;

    public static function getProductCode($customerId)
    {
        $listProductCodes = [];
        $listCustomerSources = CustomerSource::where("customer_id", $customerId)->get();
        foreach ($listCustomerSources as $customerSources) {
            if ($customerSources->marketing_product_id == null) {
                array_push($listProductCodes, $customerSources->product_code);
            } else {
                $marketingProduct = MarketingProduct::get($customerSources->marketing_product_id);
                array_push($listProductCodes, $marketingProduct->code);
            }
        }
        return $listProductCodes;
    }

    public static function exist($customerSource)
    {
        return CustomerSource::where("customer_id", $customerSource->customer_id)
                ->where("marketing_product_id", $customerSource->marketing_product_id)
                ->where("product_code", $customerSource->product_code)
                ->whereDate("marketing_product_id", $customerSource->created)->count() > 0;
    }
}
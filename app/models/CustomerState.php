<?php


namespace App\models;


class CustomerState
{
    public const STATE_CUSTOMER_WAITING_FOR_CONFIRMING_CUSTOMER = 1;
    public const STATE_CUSTOMER_CUSTOMER_NOT_REPLY_TO_TELESALES = 2;
    public const STATE_CUSTOMER_CUSTOMER_DISAGREED = 3;
    public const STATE_CUSTOMER_CUSTOMER_AGREED = 4;
    public const STATE_CUSTOMER_WAITING_FOR_PRODUCT_AVAILABLE = 5;
    public const STATE_CUSTOMER_ORDER_CREATED = 6;

    public static function getName($stateId)
    {
        switch ($stateId) {
            case self::STATE_CUSTOMER_WAITING_FOR_CONFIRMING_CUSTOMER:
                return "Đợi xác nhận";
            case self::STATE_CUSTOMER_CUSTOMER_NOT_REPLY_TO_TELESALES:
                return "Không nghe điện thoại";
            case self::STATE_CUSTOMER_CUSTOMER_DISAGREED:
                return "Không đồng ý mua";
            case self::STATE_CUSTOMER_CUSTOMER_AGREED:
                return "Đồng ý mua";
            case self::STATE_CUSTOMER_WAITING_FOR_PRODUCT_AVAILABLE:
                return "Đợi hàng";
            case self::STATE_CUSTOMER_ORDER_CREATED:
                return "Lên đơn";
        }
        return "";
    }

    public static function listIds()
    {
        return [
            self::STATE_CUSTOMER_WAITING_FOR_CONFIRMING_CUSTOMER,
            self::STATE_CUSTOMER_CUSTOMER_NOT_REPLY_TO_TELESALES,
            self::STATE_CUSTOMER_CUSTOMER_DISAGREED,
            self::STATE_CUSTOMER_CUSTOMER_AGREED,
            self::STATE_CUSTOMER_WAITING_FOR_PRODUCT_AVAILABLE,
            self::STATE_CUSTOMER_ORDER_CREATED];
    }
}

<?php


namespace App\models;


class OrderState
{
    public const STATE_CUSTOMER_AGREED = 4;
    public const STATE_OUT_OF_PRODUCT = 5;
    public const STATE_WAITING_FOR_DELIVERING = 6;
    public const STATE_CUSTOMER_RECEIVED = 7;
    public const STATE_PAYMENT_SUCCESSFUL = 8;
    public const STATE_PRODUCT_IS_RETURNING = 9;
    public const STATE_PRODUCT_IS_RETURNED = 10;
    public const STATE_CUSTOMER_NOT_REPLY_TO_SHIPPER = 11;

    public static function getName($stateId)
    {
        switch ($stateId) {
            case self::STATE_CUSTOMER_AGREED:
                return "Đồng ý mua";
            case self::STATE_OUT_OF_PRODUCT:
                return "Hết hàng";
            case self::STATE_WAITING_FOR_DELIVERING:
                return "Đợi giao hàng";
            case self::STATE_CUSTOMER_RECEIVED:
                return "Giao hàng thành công";
            case self::STATE_PAYMENT_SUCCESSFUL:
                return "Đã quyết toán";
            case self::STATE_PRODUCT_IS_RETURNING:
                return "Hoàn lại công ty";
            case self::STATE_PRODUCT_IS_RETURNED:
                return "Đã hoàn về công ty";
            case self::STATE_CUSTOMER_NOT_REPLY_TO_SHIPPER:
                return "Không nghe điện thoại";
        }
        return "";
    }

    public static function listIds()
    {
        return [self::STATE_CUSTOMER_AGREED,
            self::STATE_OUT_OF_PRODUCT,
            self::STATE_WAITING_FOR_DELIVERING,
            self::STATE_CUSTOMER_RECEIVED,
            self::STATE_PAYMENT_SUCCESSFUL,
            self::STATE_PRODUCT_IS_RETURNING,
            self::STATE_PRODUCT_IS_RETURNED,
            self::STATE_CUSTOMER_NOT_REPLY_TO_SHIPPER];
    }
}

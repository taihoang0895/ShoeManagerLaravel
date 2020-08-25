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
}

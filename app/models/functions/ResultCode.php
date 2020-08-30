<?php


namespace App\models\functions;


class ResultCode
{
    public const SUCCESS = 200;
    public const FAILED_UNKNOWN = 300;
    public const FAILED_USER_DUPLICATE_USERNAME = 301;
    public const FAILED_PRODUCT_NOT_FOUND = 302;
    public const FAILED_PRODUCT_DUPLICATE_CODE = 303;
    public const FAILED_PERMISSION_DENY = 304;

    public const FAILED_SAVE_ORDER_CUSTOMER_NOT_FOUND = 305;
    public const FAILED_SAVE_ORDER_UNKNOWN_ORDER_STATE = 306;
    public const FAILED_SAVE_DETAIL_ORDER_NOT_FOUND_PRODUCT = 307;
    public const FAILED_SAVE_DETAIL_ORDER_OUT_OF_PRODUCT = 308;
}

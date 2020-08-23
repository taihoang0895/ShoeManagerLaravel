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
}

<?php


namespace App\models\functions;


class Log
{
    public static function log($tag, $message)
    {
        error_log($tag . ' -> ' . $message);
    }
}

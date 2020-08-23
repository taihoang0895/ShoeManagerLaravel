<?php


namespace App\models\functions;


use Carbon\Carbon;
use mysql_xdevapi\Exception;

class Util
{
    public static function now()
    {
        return now();
    }

    public static function formatLeadingZeros($number, $length)
    {
        return substr(str_repeat(0, $length) . $number, -$length);

    }

    public static function toUpper($string)
    {
        return mb_strtoupper($string, "UTF-8");
    }

    public static function parseInt($number, $default = null)
    {
        try {
            return (int)($number);
        } catch (\Exception $e) {
            return $default;
        }
    }

    public static function convertDateTimeSql($datetimeSql)
    {
        return Carbon::createFromFormat('Y-m-d H:i:s', $datetimeSql);
    }

    public static function convertDateSql($datetimeSql)
    {
        return Carbon::createFromFormat('Y-m-d', $datetimeSql);
    }


    public static function formatDate($dateTime)
    {
        return $dateTime->format('d/m/Y');
    }

    public static function formatDateTime($dateTime)
    {
        return $dateTime->format('d/m/Y h:i:s');
    }

    public static function formatMoney($number)
    {
        try {
            $number = Util::parseInt($number, 0);
            $number_str = strrev(strval($number));
            $number = str_split($number_str);

            $money = "";
            for ($i = 0; $i < count($number); $i++) {
                if (($i % 3) == 0 && $i != 0) {
                    $money = $number[$i] . "." . $money;
                } else {
                    $money = $number[$i] . $money;
                }
            }

            return $money;
        } catch (\Exception $e) {
            print($e);
        }
        return "";
    }

    public static function safeParseDate($dateStr)
    {
        $dateSegments = explode("/", $dateStr);
        if (count($dateSegments) != 3) {
            return null;
        }

        try {
            $day = $dateSegments[0];
            $month = $dateSegments[1];
            $year = $dateSegments[2];
            return Carbon::create($year = (int)$year, $month = (int)$month, $day = (int)$day, 0, 0, 0);
        } catch (\Exception $e) {
            Log::log("error", $e->getMessage());
            return null;
        }
    }

}

<?php


namespace App\models\functions;


class ActionCode
{
    public const DELETE = 0;
    public const INSERT = 1;
    public const UPDATE = 2;

    public static function getName($actionCode)
    {
        switch ($actionCode) {
            case ActionCode::DELETE:
                return "Xóa";
            case ActionCode::UPDATE:
                return "Sửa";
            case ActionCode::INSERT:
                return "Thêm";

        }
        return "";
    }
}

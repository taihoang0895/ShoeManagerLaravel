<?php


namespace App\models;


class OrderState
{
    public const STATE_ORDER_CANCEL = 1;
    public const STATE_ORDER_PENDING = 4;
    public const STATE_ORDER_CREATED = 6;
    public const STATE_ORDER_TAKING = 7;
    public const STATE_ORDER_TAKEN = 8;
    public const STATE_ORDER_FAILED_TAKING = 9;
    public const STATE_ORDER_DELIVERING = 10;
    public const STATE_ORDER_DELIVERED = 11;
    public const STATE_ORDER_FAILED_DELIVERING = 12;

    public const STATE_ORDER_IS_RETURNING = 13;
    public const STATE_ORDER_IS_RETURNED = 14;
    public const STATE_PAYMENT_SUCCESSFUL_2 = 15;
    public const STATE_ORDER_IS_RETURNED_AND_NO_BROKEN = 16;
    public const STATE_ORDER_IS_RETURNED_AND_BROKEN = 17;

    public const STATE_PAYMENT_SUCCESSFUL = 18;

    public static function getName($stateId)
    {
        switch ($stateId) {
            case self::STATE_ORDER_CANCEL:
                return "Hủy";
            case self::STATE_ORDER_PENDING:
                return "Chưa tiếp nhận";
            case self::STATE_ORDER_CREATED:
                return "Đã tiếp nhận";
            case self::STATE_ORDER_TAKING:
                return "Đang lấy hàng";
            case self::STATE_ORDER_TAKEN:
                return "Đã lấy hàng";
            case self::STATE_ORDER_FAILED_TAKING:
                return "Không lấy được hàng";
            case self::STATE_ORDER_DELIVERING:
                return "Đang giao hàng";
            case self::STATE_ORDER_DELIVERED:
                return "Đã giao hàng";
            case self::STATE_ORDER_FAILED_DELIVERING:
                return "Không giao được hàng";
            case self::STATE_ORDER_IS_RETURNING:
                return "Đang trả hàng";
            case self::STATE_ORDER_IS_RETURNED:
                return "Đã trả hàng";
            case self::STATE_PAYMENT_SUCCESSFUL:
                return "Đã đối soát";
            case self::STATE_PAYMENT_SUCCESSFUL_2:
                return "Đã đối soát công nợ";
            case self::STATE_ORDER_IS_RETURNED_AND_NO_BROKEN:
                return "Đã trả hàng và không lỗi";
            case self::STATE_ORDER_IS_RETURNED_AND_BROKEN:
                return "Đã trả hàng và lỗi";

        }
        return "";
    }

    public static function listIds()
    {
        return [self::STATE_ORDER_CANCEL,
            self::STATE_ORDER_PENDING,
            self::STATE_ORDER_CREATED,
            self::STATE_ORDER_TAKING,
            self::STATE_ORDER_TAKEN,
            self::STATE_ORDER_FAILED_TAKING,
            self::STATE_ORDER_DELIVERING,
            self::STATE_ORDER_DELIVERED,
            self::STATE_ORDER_FAILED_DELIVERING,
            self::STATE_ORDER_IS_RETURNING,
            self::STATE_ORDER_IS_RETURNED,
            self::STATE_ORDER_IS_RETURNED_AND_NO_BROKEN,
            self::STATE_ORDER_IS_RETURNED_AND_BROKEN,
            self::STATE_PAYMENT_SUCCESSFUL,
            self::STATE_PAYMENT_SUCCESSFUL_2];
    }
}

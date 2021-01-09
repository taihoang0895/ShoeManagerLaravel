<?php


namespace App\models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use phpDocumentor\Reflection\Types\Object_;

class Storage extends Model
{
    public const STORAGE_VU_NGOC_PHAN_ID = 1;
    public const STORAGE_VU_NGOC_PHAN_NAME = "Kho Vũ Ngọc Phan";
    public const STORAGE_VU_NGOC_PHAN_ADDRESS = "LK01, 25 Vũ Ngọc Phan, Đống Đa, Hà Nội, Phố Vũ Ngọc Phan, Quận Đống Đa, Hà Nội";
    public const STORAGE_VU_NGOC_PHAN_PHONE = "0929331111";
    public const STORAGE_XA_DAN_ID = 2;
    public const STORAGE_XA_DAN_NAME = "Kho Xã Đàn";
    public const STORAGE_XA_DAN_ADDRESS = "136A XÃ ĐÀN,PHƯƠNG LIÊN ,ĐỐNG ĐA.HN,PHƯỜNG PHƯƠNG LIÊN, HÀ NỘI, QUẬN ĐỐNG ĐA.";
    public const STORAGE_XA_DAN_PHONE = "02466598617";
    public const STORAGE_XUAN_LA_ID = 3;
    public const STORAGE_XUAN_LA_NAME = "Kho Xuân La";
    public const STORAGE_XUAN_LA_ADDRESS = "340 Lạc Long Quân";
    public const STORAGE_XUAN_LA_PHONE = "02466598617";

    //
    public $timestamps = false;

    public static function findAll()
    {
        $result = [];
        $storage = new Storage();
        $storage->id = Storage::STORAGE_VU_NGOC_PHAN_ID;
        $storage->name = self::STORAGE_VU_NGOC_PHAN_NAME;
        $storage->address = self::STORAGE_VU_NGOC_PHAN_ADDRESS;
        $storage->phone = self::STORAGE_VU_NGOC_PHAN_PHONE;
        array_push($result, $storage);

        $storage = new Storage();
        $storage->id = Storage::STORAGE_XA_DAN_ID;
        $storage->name = self::STORAGE_XA_DAN_NAME;
        $storage->address = self::STORAGE_XA_DAN_ADDRESS;
        $storage->phone = self::STORAGE_XA_DAN_PHONE;
        array_push($result, $storage);

        $storage = new Storage();
        $storage->id = Storage::STORAGE_XUAN_LA_ID;
        $storage->name = self::STORAGE_XUAN_LA_NAME;
        $storage->address = self::STORAGE_XUAN_LA_ADDRESS;
        $storage->phone = self::STORAGE_XUAN_LA_PHONE;
        array_push($result, $storage);

        return $result;
    }


    public static function get($id)
    {

        switch ($id) {
            case Storage::STORAGE_VU_NGOC_PHAN_ID:
                $storage = new Storage();
                $storage->id = Storage::STORAGE_VU_NGOC_PHAN_ID;
                $storage->name = self::STORAGE_VU_NGOC_PHAN_NAME;
                $storage->address = self::STORAGE_VU_NGOC_PHAN_ADDRESS;
                $storage->phone = self::STORAGE_VU_NGOC_PHAN_PHONE;
                return $storage;

            case Storage::STORAGE_XA_DAN_ID:
                $storage = new Storage();
                $storage->id = Storage::STORAGE_XA_DAN_ID;
                $storage->name = self::STORAGE_XA_DAN_NAME;
                $storage->address = self::STORAGE_XA_DAN_ADDRESS;
                $storage->phone = self::STORAGE_XA_DAN_PHONE;
                return $storage;

            case Storage::STORAGE_XUAN_LA_ID:
                $storage = new Storage();
                $storage->id = Storage::STORAGE_XUAN_LA_ID;
                $storage->name = self::STORAGE_XUAN_LA_NAME;
                $storage->address = self::STORAGE_XUAN_LA_ADDRESS;
                $storage->phone = self::STORAGE_XUAN_LA_PHONE;
                return $storage;
        }
        return null;
    }
}
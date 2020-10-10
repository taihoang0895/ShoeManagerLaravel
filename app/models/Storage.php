<?php


namespace App\models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Storage extends Model
{
    //
    public $timestamps = false;

    public static function findAll()
    {
        $result = [];
        $storage = new Storage();
        $storage->id = 1;
        $storage->address = User::convertCodeToDepartmentName(User::$DEPARTMENT_STOREKEEPER_VU_NGOC_PHAN);
        array_push($result, $storage);
        $storage = new Storage();
        $storage->id = 2;
        $storage->address = User::convertCodeToDepartmentName(User::$DEPARTMENT_STOREKEEPER_XA_DAN);;
        array_push($result, $storage);
        return $result;
    }

    public static function getShortName($id)
    {
        if ($id == 1) {
            $storage = new Storage();
            $storage->id = 1;
            $storage->address = User::convertCodeToDepartmentName(User::$DEPARTMENT_STOREKEEPER_VU_NGOC_PHAN);
            return $storage;
        }
        if ($id == 2) {
            $storage = new Storage();
            $storage->id = 2;
            $storage->address = User::convertCodeToDepartmentName(User::$DEPARTMENT_STOREKEEPER_XA_DAN);;
            return $storage;
        }
        return null;
    }

    public static function get($id)
    {
        return Storage::where("id", $id)->first();
    }
}
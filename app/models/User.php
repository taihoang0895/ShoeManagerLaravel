<?php

namespace App;

use App\models\functions\Log;
use App\models\functions\Util;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use phpDocumentor\Reflection\Types\This;

class User extends Authenticatable
{
    static $DEPARTMENT_SALE = 0;
    static $DEPARTMENT_MARKETING = 1;
    static $DEPARTMENT_STOREKEEPER_VU_NGOC_PHAN = 2;
    static $DEPARTMENT_STOREKEEPER_XA_DAN = 3;

    static $ROLE_MEMBER = 0;
    static $ROLE_LEADER = 1;
    static $ROLE_ADMIN = 2;
    static $ROLE_SALE_ADMIN = 3;


    use Notifiable;

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public static function getRoleName($role)
    {
        switch ($role) {
            case User::$ROLE_MEMBER:
                return "Member";
            case User::$ROLE_LEADER:
                return "Leader";
            case User::$ROLE_ADMIN:
                return "Admin";
            case User::$ROLE_SALE_ADMIN:
                return "Sale Admin";
        }
        return "";
    }

    public function getDepartmentName()
    {
        if ($this->isAdmin()) {
            return "Admin";
        }
        switch ($this->department) {
            case User::$DEPARTMENT_SALE:
                return "Sale";
            case User::$DEPARTMENT_MARKETING:
                return "Marketing";
            case User::$DEPARTMENT_STOREKEEPER_VU_NGOC_PHAN:
                return "Kho Vũ Ngọc Phan";
            case User::$DEPARTMENT_STOREKEEPER_XA_DAN:
                return "Kho Xã Đàn";
        }
        return "";
    }

    public static function convertCodeToDepartmentName($department)
    {
        switch ($department) {
            case User::$DEPARTMENT_SALE:
                return "Sale";
            case User::$DEPARTMENT_MARKETING:
                return "Marketing";
            case User::$DEPARTMENT_STOREKEEPER_VU_NGOC_PHAN:
                return "Kho Vũ Ngọc Phan";
            case User::$DEPARTMENT_STOREKEEPER_XA_DAN:
                return "Kho Xã Đàn";
        }
        return "";
    }

    public static function parseDepartmentName($name)
    {

        $name = Util::toUpper($name);
        if ($name == "SALE") {
            return User::$DEPARTMENT_SALE;
        }
        if ($name == "MARKETING") {
            return User::$DEPARTMENT_MARKETING;
        }
        if ($name == "KHO VŨ NGỌC PHAN") {
            return User::$DEPARTMENT_STOREKEEPER_VU_NGOC_PHAN;
        }
        if ($name == "KHO XÃ ĐÀN") {
            return User::$DEPARTMENT_STOREKEEPER_XA_DAN;
        }
        return -1;
    }

    public static function parseRoleName($name)
    {
        $name = Util::toUpper($name);
        if ($name == "MEMBER") {
            return User::$ROLE_MEMBER;
        }
        if ($name == "LEADER") {
            return User::$ROLE_LEADER;
        }
        if ($name == "ADMIN") {
            return User::$ROLE_ADMIN;
        }
        if ($name == "SALE ADMIN") {
            return User::$ROLE_SALE_ADMIN;
        }
        return -1;
    }

    public function setAliasName($value)
    {
        $this->alias_name = $value;
    }

    public function setIsActive($value)
    {
        $this->is_active = $value;
    }

    public function setDepartment($value)
    {
        $this->department = $value;
    }

    public function setRole($value)
    {
        $this->role = $value;
    }

    public function isLeader()
    {
        return ($this->role == User::$ROLE_LEADER || $this->role == User::$ROLE_ADMIN || $this->role == User::$ROLE_SALE_ADMIN);
    }

    public function isAdmin()
    {
        return $this->role == User::$ROLE_ADMIN;
    }

    public function isSaleAdmin()
    {
        return $this->role == User::$ROLE_SALE_ADMIN;
    }

    public function isMember()
    {
        return $this->role == User::$ROLE_MEMBER;
    }

    public function isSale()
    {
        return $this->department == User::$DEPARTMENT_SALE;
    }

    public function isMarketing()
    {
        return $this->department == User::$DEPARTMENT_MARKETING;
    }

    public function isStoreKeeper()
    {
        return in_array($this->department, [User::$DEPARTMENT_STOREKEEPER_VU_NGOC_PHAN, User::$DEPARTMENT_STOREKEEPER_XA_DAN]);
    }
}

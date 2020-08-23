<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class NotificationManager extends Model
{
    //
    public $timestamps = false;

    public static function getOrNew($user){
        $row = NotificationManager::where("user_id", $user->id)->first();
        if($row == null){
            $row = new NotificationManager();
            $row->user_id = $user->id;
            $row->save();
        }
        return $row;
    }
}

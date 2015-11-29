<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    public $timestamps = false;

    public static function getByCode($code)
    {
        return static::where(['code' => $code])->get();
    }
}

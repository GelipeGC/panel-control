<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role
{
    use HasFactory;

    public static function getList()
    {
        return ['admin','user'];
    }
}

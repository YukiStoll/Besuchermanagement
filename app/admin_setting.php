<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class admin_setting extends Model
{
    protected $fillable =
        [
            'setting_key',
            'setting_value',
            'setting_type',
        ];
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class holidays extends Model
{
    protected $fillable =
        [
            'userID',
            'from',
            'to',
        ];
    protected $table = 'holiday_allocation';
    public $timestamps = false;
    use HasFactory;
}

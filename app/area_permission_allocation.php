<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class area_permission_allocation extends Model
{
    use HasFactory;
    protected $fillable =
        [
            'areapermissionID',
            'userID',
            'position',
        ];
    protected $table = 'area_permission_allocation';
    public $timestamps = false;
}

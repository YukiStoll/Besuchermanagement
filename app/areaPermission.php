<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class areaPermission extends Model
{
    use HasFactory, Sortable;
    protected $fillable =
        [
            'name',
            'mawaID',
            'deleted_from_id',
        ];
    public $sortable = ['mawaID',
                        'name'];
    protected $table = 'areapermission';
    public $timestamps = true;
}

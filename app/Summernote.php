<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class Summernote extends Model
{
    use Sortable;
    protected $fillable =
    [
        'content_de',
        'content_en',
    ];
    public $sortable = [
        'name',
    ];
}

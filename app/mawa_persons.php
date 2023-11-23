<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class mawa_persons extends Model
{
    use Sortable;
    protected $fillable =
    [
        'cardID',
        'firstName',
        'lastName',
        'type',
        'validFrom',
        'validTo',
        'doors',
        'created_at',
        'updated_at',
        'deleted_at',
        'deleted_from_id',
    ];
    public $sortable = [
        'cardID',
        'firstName',
        'lastName',
        'type',
        'validFrom',
        'validTo',
        'doors',
    ];
    public $sortableAs = ['Visitor', 'Company', 'visitorCategory', 'name'];
}

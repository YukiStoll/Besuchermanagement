<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class history_action_log extends Model
{
    use HasFactory, Sortable;
    protected $fillable =
        [
            'userID',
            'action',
            'forProcessID',
        ];
    public $sortable = ['userID',
                        'action',
                        'forProcessID'];
    public $sortableAs = ['User', 'date'];
    protected $table = 'history_action_log';
    public $timestamps = true;
}

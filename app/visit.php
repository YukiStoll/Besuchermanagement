<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class visit extends Model
{
    use Sortable;
    protected $fillable =
    [
        'startDate',
        'endDate',
        'visitorallocationid',
        'userId',
        'visitId',
        'canteenId',
        'parkingManagementId',
        'advanceRegistrationId',
        'created_at',
        'updated_at',
        'deleted_at',
        'deleted_from_id',
        'isgroup',
        'orderNumber',
        'vehicleRegistrationNumber',
        'cargo',
        'reasonForVisit',
        'contactPossibility',
        'entrypermission',
        'workPermission',
        'entrypermissionID',
        'workPermissionID',
    ];
    public $sortable = [
        'startDate',
        'endDate',
        'visitId',
    ];
    public $sortableAs = ['Visitor', 'Company', 'visitorCategory', 'name'];
    //protected $dates = ['startDate', 'endDate'];
}

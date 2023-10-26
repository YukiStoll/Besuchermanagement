<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class advanceRegistration extends Model
{
    use Sortable;
    protected $fillable =
    [
        'startDate',
        'endDate',
        'roadmap',
        'contactPossibility',
        'userId',
        'allocationid',
        'visitId',
        'visitorId',
        'deleted_at',
        'deleted_from_id',
        'party',
        'hygieneRegulations',
        'reasonForVisit',
        'entryPermissionText',
        'workPermissionApprovalText',
        'entrypermission',
        'workPermission',
        'entrypermissionID',
        'workPermissionID',
    ];
    public $sortable = [
        'startDate',
        'endDate',
        'visitId',
        'created_at',
        'updated_at',
    ];
    public $sortableAs = ['Visitor', 'Company', 'visitorCategory', 'name'];
}

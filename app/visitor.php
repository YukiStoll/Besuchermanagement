<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class visitor extends Model
{

    use Sortable;
    protected $fillable =
        [
            'forename',
            'surname',
            'dateOfBirth',
            'salutation',
            'title',
            'email',
            'language',
            'citizenship',
            'visitorCategory',
            'visitorDetail',
            'company',
            'landlineNumber',
            'mobileNumber',
            'confidentialityAgreement',
            'safetyInstruction',
            'companyStreet',
            'companyCountry',
            'companyZipCode',
            'companyCity',
            'creator',
            'deleted_at',
            'deleted_from_id',
            'questionsSafetyInstructions',
        ];

    public $sortable = [
        'forename',
        'surname',
        'email',
        'visitorCategory',
        'company',
        'landlineNumber',
        'mobileNumber',
        'created_at'
    ];
    public $sortableAs = [
        'visitorDetail',
    ];
}

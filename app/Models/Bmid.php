<?php

namespace App\Models;

use App\Models\ApihouseModel;

class Bmid extends ApihouseModel
{
    protected $table = 'bmid';

    protected $casts = [
        'showers'               => 'bool',
        'org_vehicle_insurance' => 'bool',
        'create_datetime'       => 'datetime',
        'modified_datetime'     => 'timestamp'
    ];

    public static function findForPersonYear($personId, $year) {
        return self::where('person_id', $personId)->where('year', $year)->first();
    }
}

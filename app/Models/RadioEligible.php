<?php

namespace App\Models;

use App\Models\ApihouseModel;

class RadioEligible extends ApihouseModel
{
    protected $table = 'radio_eligible';

    protected $fillable = [
        'person_id',
        'year',
        'max_radios'
    ];

    public static function findForPersonYear($personId, $year) {
        return self::where('person_id', $personId)->where('year', $year)->first();
    }
}

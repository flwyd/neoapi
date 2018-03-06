<?php

namespace App\Models;

use App\Models\ApihouseModel;

class PersonPosition extends ApihouseModel
{
    protected $table = 'person_position';

    protected $fillable = [
        'person_id',
        'position_id'
    ];

    public static function havePosition($personId, $positionId) {
        return self::where('person_id', $personId)
                   ->where('position_id', $positionId)->exists();
    }
}

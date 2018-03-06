<?php

namespace App\Models;

use Carbon\Carbon;

use App\Models\ApihouseModel;
use App\Models\PositionCredit;
use App\Models\ApihouseResult;
use App\Helpers\DateHelper;

use Illuminate\Support\Facades\DB;

class PersonSlot extends ApihouseModel
{
    protected $table = 'person_slot';

    protected $fillable = [
        'person_id',
        'slot_id'
    ];

    protected $casts = [
        'timestamp' => 'timestamp'
    ];

    public static function haveSlot($personId, $slotId) {
        return self::where('person_id', $personId)->where('slot_id', $slotId)->exists();
    }
}

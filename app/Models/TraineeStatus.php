<?php

namespace App\Models;

use App\Models\ApihouseModel;

class TraineeStatus extends ApihouseModel
{
    protected $table = 'trainee_status';

    protected $casts = [
        'passed'    => 'bool',
        'begins'    => 'datetime',
        'ends'      => 'datetime',
    ];

    protected $training_location;
    protected $training_date;

    /*
     * A the trainee_status record with joined slot for a person & year.
     * (Note: record returned will be a merged trainee_state & slot row.
     */

    static public function findForPersonYear($personId, $year) {
        return self::join('slot', 'slot.id', 'trainee_status.slot_id')
                ->where('person_id', $personId)
                ->whereRaw('YEAR(slot.begins)=?', $year)->first();

    }
}

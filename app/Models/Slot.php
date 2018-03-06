<?php

namespace App\Models;

use App\Models\ApihouseModel;
use Illuminate\Support\Facades\DB;

class Slot extends ApihouseModel
{
    protected $table = 'slot';

    protected $fillable = [
        'begins',
        'ends',
        'position_id',
        'description',
        'signed_up',
        'max',
        'url',
        'trainer_slot_id',
        'min',
        //'training_id', -- no longer used
    ];

    protected $casts = [
        'begins'    => 'datetime',
        'ends'      => 'datetime'
    ];

    public static function findForQuery($query) {
        $sql = self::select('*');

        if ($query['year']) {
            $sql = $sql->whereRaw('YEAR(begins)=?', $query['year']);
        }

        if ($query['type']) {
            $sql = $sql->where('type', $query['type']);
        }

        if ($query['position_id']) {
            $sql = $sql->where('position_id', $query['position_id']);
        }

        return $sql->get();
    }

    public static function findSignUps($slotId) {
        return DB::table('person_slot')
            ->select('person.id', 'person.callsign')
            ->join('person', 'person.id', '=', 'person_slot.person_id')
            ->where('person_slot.slot_id', $slotId)
            ->orderBy('person.callsign', 'asc')
            ->get();
    }
}

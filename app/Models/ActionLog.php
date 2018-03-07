<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActionLog extends Model
{
    protected $table = 'action_logs';

    public static function record($personId, $area, $event, $message, $data=null, $targetPersonId=null) {
        $log = new ActionLog;
        $log->area = $area;
        $log->event = $event;
        $log->person_id = $personId;
        $log->message = $message;
        $log->target_person_id = $targetPersonId;

        if ($data) {
            $log->data = json_encode($data);
        }

        $log->save();
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\ApihouseModel;
use App\Helpers\DateHelper;

class PersonMessage extends ApihouseModel
{
    /**
     * The database table name.
     * @var string
     */
    protected $table = 'person_message';

    protected $fillable = [
        'recipient_callsign',
        'message_from',
        'subject',
        'body',
    ];

    protected $results = [
        'person_id',
        'creator_callsign',
        'creator_person_id',
        'sender_person_id',
        'sender_callsign',
        'subject',
        'body',
        'delivered',
        'sent_at'
    ];

    public $recipient_callsign;

    protected $casts = [
        'delivered' => 'bool',
        'timestamp' => 'timestamp'
    ];

    protected $saveRules = [
        'subject'   => 'required',
        'body'      => 'required',
        'recipient_callsign'    => 'required'
    ];

    public static function findForPerson($personId) {
        return self::where('person_id', $personId)
            ->leftJoin('person as creator', 'creator.id', '=', 'person_message.creator_person_id')
            ->leftJoin('person as sender', 'sender.callsign', '=', 'person_message.message_from')
            ->orderBy('person_message.timestamp', 'desc')
            ->get(['person_message.*', 'creator.callsign as creator_callsign', 'sender.id as sender_person_id']);
    }

    public static function countUnread($personId)
    {
        return PersonMessage::where('person_id', $personId)->where('delivered', false)->count();
    }

    public function getSentAtAttribute() {
        return DateHelper::formatDateTime($this->timestamp);
    }

    public function getSenderCallsignAttribute() {
        return $this->message_from;
    }
}

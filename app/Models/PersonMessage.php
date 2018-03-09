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

    /*
     * To make some sense of the person_message table, some
     * columns are remapped to saner names.
     *
     *  sender_callsign -> message_from
     *  sent_at -> timestamp
     *
     * The following are readonly fields returned for queries:
     * creator_callsign: person.callsign looked up from creator_person_id
     * sender_person_id: person.id looked up from message_from
     *
     * The following are used for message creation:
     * recipient_callsign: used during creation to setup person_message.person_id
     * sender_callsign: used to set message_from during validation
     */

    protected $fillable = [
        'recipient_callsign',
        'sender_callsign',
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
        'sent_at',
    ];

    public $recipient_callsign;

    protected $casts = [
        'delivered' => 'bool',
        'timestamp' => 'datetime'
    ];

    protected $createRules = [
        'recipient_callsign'    => 'required',
        'sender_callsign'       => 'required',
        'subject'               => 'required',
        'body'                  => 'required',
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
        return $this->timestamp;
    }

    public function getSenderCallsignAttribute() {
        return $this->message_from;
    }

    public function setSenderCallsignAttribute($value) {
        $this->message_from = $value;
    }

    /*
     * validate does triple duty here.
     * - validate the required columns are present
     * - make sure the recipient & sender callsigns are present
     * - setup appropriate fields based on the callsigns
     *
     * @param array $rules array to override class $rules
     * @param return bool true if model is valid
     */

    public function validate($rules=null): bool {
        if (!parent::validate($rules)) {
            return false;
        }

        /* Find callsigns and verify contents */

        $recipient = Person::findCallsign($this->recipient_callsign);

        if (!$recipient) {
            $this->addError('recipient_callsign', 'Callsign does not exist');
            return false;
        }

        $this->person_id = $recipient->id;

        /* And make sure sender exists */
        $sender = Person::findCallsign($this->sender_callsign);

        if (!$sender) {
            $this->addError('sender_callsign', 'Callsign does not exist');
            return false;
        }

        $this->sender_callsign = $sender->callsign;
        return true;
    }

    /*
     * Mark a message as read
     */

     public function markRead() {
         $this->delivered = true;
         return $this->saveWithoutValidation();
     }
}

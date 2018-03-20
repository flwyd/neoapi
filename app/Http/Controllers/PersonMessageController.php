<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiHouseController;
use App\Models\Person;
use App\Models\PersonMessage;
use App\Models\Role;

class PersonMessageController extends ApiHouseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index(Request $request)
    {
        $query = request()->validate([
            'person_id' => 'required|integer',
        ]);

        $personId = $query['person_id'];

        $this->authorize('index', [PersonMessage::class, $personId ]);

        return $this->jsonApi(PersonMessage::findForPerson($personId), false);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $person_message = new PersonMessage;
        $person_message->fromJsonApi($request);

        // Message created by logged in user
        $person_message->creator_person_id = $this->user->id;

        // Override message_from if user does not appropriate privileges
        if (!$this->userHasRole([ Role::ADMIN, Role::MANAGE])) {
            $person_message->message_from = $this->user->callsign;
        }

        if ($person_message->save()) {
            return $this->success($person_message);
        }

        return $this->errorJsonApi($person_message);
    }

    /**
     *  Delete a message
     *
     * @param  PersonMessage $person_message the message to delete
     * @return \Illuminate\Http\Response
     */
    public function destroy(PersonMessage $person_message)
    {
        $this->authorize('delete', $person_message);
        $person_message->delete();

        return $this->deleteSuccess();
    }

    /**
     * Mark message as read.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function markread(PersonMessage $person_message)
    {
        $this->authorize('markread', $person_message);

        if (!$person_message->markRead()) {
            return $this->jsonError('Cannot mark message as read');
        }

        return $this->success();
    }

}

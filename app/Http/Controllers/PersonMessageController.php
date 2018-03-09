<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\ApihouseController;
use App\Models\Person;
use App\Models\PersonMessage;
use App\Models\Role;

class PersonMessageController extends ApihouseController
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

        return $this->jsonApi(PersonMessage::findForPerson($query['person_id']), false);
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
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(PersonMessage $message)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(PersonMessage $message)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(PersonMessage $message)
    {
        //
    }

    /**
     * Mark message as read.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function markread(PersonMessage $person_message)
    {
        if (!$person_message->markRead()) {
            return $this->jsonError('Cannot mark message as read');
        }

        return $this->success();
    }

}

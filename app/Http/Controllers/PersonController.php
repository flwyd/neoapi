<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use App\Http\Controllers\ApiHouseController;
use App\Http\JsonApi;

use App\Models\Person;
use App\Models\PersonMessage;
use App\Models\Timesheet;
use App\Models\PersonLanguage;
use App\Models\PersonRole;
use App\Models\PersonYearInfo;
use App\Models\Role;

class PersonController extends ApiHouseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('store');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Person $person)
    {
        $personId = $person->id;
        $person->retrieveRoles();

        $person->years_rangered = Timesheet::yearsRangered($personId);
        $person->unread_message_count = PersonMessage::countUnread($personId);
        $person->languages = PersonLanguage::retrieveForPerson($personId);

        return $this->jsonApi($person);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Person $person)
    {
        $this->authorize('update', $person);

        $person->fromJsonApi(request(), $this->user);

        if (!$person->save()) {
            return $this->errorJsonApi($person);
        }

        error_log("PERSON LANGUAGE ".json_encode($person->languages));
        if ($person->languages !== null) {
            PersonLanguage::updateForPerson($id, $person->languages);
        }

        $this->log('person', 'update', 'Information updated');

        return $this->success($person);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Person $person)
    {
        $person = $this->findPerson($id);
        $this->authorize('delete', $person);
        $this->log('person', 'delete', 'Person deletedd');
    }

    /*
     * Returning the person's training status, BMID & radio privs
     * for a given year.
     */

    public function yearInfo(Person $person)
    {
        $year = $this->getYear();
        $yearInfo = PersonYearInfo::findForPersonYear($person->id, $year);
        if ($yearInfo) {
            return $this->toJson([ 'year_info' => $yearInfo->toJson()]);
        }

        return $this->notFound('The person or year could not be found.');
    }

    /*
     * Change password
     */

    public function password(Request $request, Person $person)
    {
        $this->authorize('password', $person);

        // Require the old password if person == user
        // and are not an admin.
        // The policy will only allow a change issued by the user or an admin
        $requireOld = $this->isUser($person) && !$this->userHasRole(Role::ADMIN);

        $rules = [
              'password' => 'required|confirmed',
              'password_confirmation' => 'required'
          ];

        if ($requireOld) {
            $rules['password_old'] = 'required';
        }

        $passwords = $request->validate($rules);

        if ($requireOld && !$person->isValidPassword($passwords['password_old'])) {
            return $this->jsonError('The old password does not match.');
        }

        $this->log('person', 'password', 'Password changed', null, $person->id);
        $person->changePassword($passwords['password']);

        return $this->success();
    }

}

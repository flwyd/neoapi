<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Controllers\ApihouseController;
use App\Models\Schedule;
use App\Models\Person;
use App\Models\Role;
use App\Models\Slot;

class PersonScheduleController extends ApihouseController
{
    /**
     * Return an array of PesronSchedule for a given person & year
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $personId)
    {
        $query = $request->validate([
            'year'    => 'required|digits:4',
            'signups' => 'boolean',
        ]);

        $query['person_id'] = $personId;
        return $this->jsonApi(Schedule::findForQuery($query), false);
    }

    /*
     * Add a person to a slot schedule
     */

     public function store(Person $person) {
         $data = request()->validate([
             'slot_id'  => 'required|integer',
         ]);

         $slotId = $data['slot_id'];
         $force = $this->userHasRole([Role::ADMIN, Role::MANAGE]);

         $result = Schedule::addToSchedule($person->id, $slotId, $force);

         if ($result['status'] == 'success') {
             $slot = Slot::find($slotId);
             $this->log('person-slot', 'add',
                    "Slot added to schedule {$slot->begins}: {$slot->description}",
                    [ 'slot_id' => $slotId],
                    $person->id);
             return response()->json($result);
         } else {
             return response()->json($result, 422);
         }
     }

    /**
     * Remove the slot from the person's schedule
     *
     * @param int $personId slot to delete for person
     * @param int $slotId to delete
     * @return \Illuminate\Http\Response
     */

    public function destroy(Person $person, $slotId)
    {
        $result = Schedule::deleteFromSchedule($person->id, $slotId);
        if ($result['status'] == 'success') {
            $slot = Slot::find($slotId);
            $this->log('person-slot', 'remove',
                    "Slot removed from schedule {$slot->begins}: {$slot->description}",
                    [ 'slot_id' => $slotId],
                    $person->id);
        }
        return response()->json($result);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\Person;
use App\Models\Role;
use App\Models\Slot;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiHouseController;

class PersonScheduleController extends ApiHouseController
{
    /**
     * Return an array of PesronSchedule for a given person & year
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Person $person)
    {
        $this->authorize('index', [ Schedule::class, $person]);

        $query = request()->validate([
            'year'    => 'required|digits:4',
            'signups' => 'boolean',
        ]);

        $query['person_id'] = $person->id;
        return $this->jsonApi(Schedule::findForQuery($query), false);
    }

    /*
     * Add a person to a slot schedule
     */

     public function store(Person $person) {
         $data = request()->validate([
             'slot_id'  => 'required|integer',
         ]);

         $this->authorize('create', [ Schedule::class, $person ]);

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
        $this->authorize('delete', [ Schedule::class, $person ]);

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

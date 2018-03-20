<?php

namespace App\Http\Controllers;

use App\Http\Controllers\ApiHouseController;
use App\Models\Person;

class CallsignsController extends ApiHouseController
{

    public function index() {
        $query = request()->validate([
            'q' => 'required|string',
            'active'    => 'bool'
        ]);

        return response()->json([ 'callsigns' => Person::searchCallsigns($query['q'], $query['active'])]);
    }
}

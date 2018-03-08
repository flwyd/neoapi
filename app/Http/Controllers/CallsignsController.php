<?php

namespace App\Http\Controllers;

use App\Http\Controllers\ApihouseController;
use App\Models\Person;

class CallsignsController extends ApihouseController
{

    public function index() {
        $query = request()->validate([
            'q' => 'required|string',
            'active'    => 'bool'
        ]);

        return response()->json([ 'callsigns' => Person::searchCallsigns($query['q'], $query['active'])]);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PersonRole extends Model
{

    protected $table = 'person_role';

    public static function retrieveForPerson($personId)
    {
        return PersonRole::where('person_id', $personId)->pluck('role_id')->toArray();
    }
}

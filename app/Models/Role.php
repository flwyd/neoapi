<?php

namespace App\Models;

use App\Models\ApihouseModel;

class Role extends ApihouseModel
{
    protected $table = 'role';

    const ADMIN            = 1;   // Super user! Change anything
    const VIEW_PII         = 2;   // See email, address, phone
    const VIEW_EMAIL       = 3;   // See email
    const GRANT_POSITION   = 4;   // Grand/Revoke Positions
    const EDIT_ACCESS_DOCS = 5;   // Edit Access Documents
    const EDIT_BMIDS       = 6;   // Edit BMIDs
    const LOGIN            = 11;  // Person allowed to login
    const MANAGE           = 12;  // Ranger HQ: access other schedule, asset checkin/out, send messages
    const MENTOR           = 101; // Mentor - access mentor section
    const TRAINER          = 102; // Trainer
    const VC               = 103; // Volunteer Coordinator -
    const ART_TRAINER      = 104; // ART trainer

    protected $casts = [
        'new_user_eligible' => 'bool'
    ];

    protected $rules = [
        'title' => 'required'
    ];

    protected $fillable = [
        'title',
        'new_user_eligible'
    ];
}

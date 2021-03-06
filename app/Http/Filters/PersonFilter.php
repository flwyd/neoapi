<?php

namespace App\Http\Filters;

use App\Models\Person;
use App\Models\Role;

class PersonFilter
{
    protected $record;

    public function __construct(Person $record)
    {
        $this->record = $record;
    }

    const STATUS_FIELDS = [
        'status',
        'status_date',
        'timestamp',
        'user_authorized',
        'date_verified',
        'create_date',
        'photo_url'
    ];

    const ROLES_FIELDS = [
        'roles',
    ];

    const NAME_GENDER_FIELDS = [
        'first_name',
        'mi',
        'last_name',
        'gender',
    ];

    const BARCODE_FIELDS = [
        'barcode',
    ];


    const CALLSIGNS_FIELDS = [
        'callsign',
        'callsign_approved',
        'formerly_known_as',
    ];

    const META_FIELDS = [
        'unread_message_count',
        'years_rangered',
    ];

    const EMAIL_FIELDS = [
        'email',
    ];

    const PERSONAL_INFO_FIELDS = [
        'street1',
        'street2',
        'apt',
        'city',
        'state',
        'zip',
        'country',

        'birthdate',

        'home_phone',
        'alt_phone',

        'camp_location',
        'on_site',

        'longsleeveshirt_size_style',
        'teeshirt_size_style',

        'languages'
    ];

    const EMERGENCY_CONTACT_FIELDS = [
        'emergency_contact',

        'em_first_name',
        'em_mi',
        'em_last_name',
        'em_handle',

        'em_home_phone',
        'em_alt_phone',
        'em_email',
        'em_camp_location',
    ];

    const EVENT_FIELDS = [
        'asset_authorized',

        'vehicle_blacklisted',
        'vehicle_paperwork',
        'vehicle_insurance_paperwork',

        'lam_status',
        'bpguid',
        'sfuid',

        'active_next_event',
    ];

    const MENTOR_FIELDS = [
        'has_note_on_file',
        'mentors_flag',
        'mentors_flag_note',
        'mentors_notes',
    ];

    //
    // FIELDS_SERIALIZE & FIELDS_DESERIALIZE elements are
    // 0: array of field names
    // 1: allow fields if the person is the authorized user
    // 2: which roles are allowed the field (if null, allow any)
    //

    const FIELDS_SERIALIZE = [
        [ self::NAME_GENDER_FIELDS ],
        [ self::STATUS_FIELDS ],
        [ self::ROLES_FIELDS ],
        [ self::CALLSIGNS_FIELDS ],
        [ self::META_FIELDS ],
        [ self::BARCODE_FIELDS, true, [ Role::VIEW_PII, Role::MANAGE, Role::VC ] ],
        [ self::EMAIL_FIELDS, true, [ Role::VIEW_PII, Role::VIEW_EMAIL, Role::VC ] ],
        [ self::PERSONAL_INFO_FIELDS, true, [ Role::VIEW_PII,  Role::VC ] ],
        [ self::EMERGENCY_CONTACT_FIELDS, true, [ Role::VIEW_PII,  Role::VC ] ],
        [ self::EVENT_FIELDS, true, [ Role::VIEW_PII,  Role::MANAGE, Role::VC, Role::EDIT_BMIDS ] ],
        // Note: self is not allowed to see mentor notes
        [ self::MENTOR_FIELDS, false, [ Role::MENTOR, Role::TRAINER, Role::VC ] ]
    ];

    const FIELDS_DESERIALIZE = [
        [ self::NAME_GENDER_FIELDS, true, [ Role::VC ] ],
        [ self::STATUS_FIELDS, false, [  Role::VC ] ],
        [ self::ROLES_FIELDS, false, [ Role::MENTOR, Role::VC ] ],
        [ self::CALLSIGNS_FIELDS, false, [ Role::MENTOR, Role::VC] ],
        [ self::BARCODE_FIELDS, true, [ Role::VIEW_PII, Role::MANAGE, Role::VC ] ],
        [ self::EMAIL_FIELDS, true, [ Role::VC ] ],
        [ self::PERSONAL_INFO_FIELDS, true, [ Role::VC ] ],
        [ self::EMERGENCY_CONTACT_FIELDS, true, [ Role::VC ]],
        [ self::EVENT_FIELDS, true, [ Role::VC ]],
        [ self::MENTOR_FIELDS, false, [ Role::MENTOR, Role::TRAINER, Role::VC ] ]
    ];

    public function buildFields(array $fieldGroups, $authorizedUser): array
    {
        $fields = [ ];

        if ($authorizedUser) {
            $isUser = ($this->record->id == $authorizedUser->id);
            $isAdmin = $authorizedUser->hasRole(Role::ADMIN);
        } else {
            $isUser = false;
            $isAdmin = false;
        }

        foreach ($fieldGroups as $group) {
            $anyone = count($group) == 1;
            $roles = null;

            if (count($group) == 1) {
                $allow = true;
            } else {
                if ($isUser && $group[1] == true) {
                    $allow = true;
                } else {
                    $allow = false;
                }
                if (isset($group[2])) {
                    $roles = $group[2];
                }
            }

            if ($allow        // anyone can use this
            || $isAdmin                 // the admin can do anything
            || ($authorizedUser && $roles && $authorizedUser->hasRole($roles))) {
                $fields = array_merge($fields, $group[0]);
            }
        }

        return $fields;
    }

    public function serializerFilter(Person $authorizedUser = null): array
    {
        return $this->buildFields(self::FIELDS_SERIALIZE, $authorizedUser);
    }

    public function deserializerFilter(Person $authorizedUser = null): array
    {
        return $this->buildFields(self::FIELDS_DESERIALIZE, $authorizedUser);
    }
}

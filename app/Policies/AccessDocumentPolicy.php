<?php

namespace App\Policies;

use App\Models\Person;
use App\Models\AccessDocument;
use App\Models\Role;
use Illuminate\Auth\Access\HandlesAuthorization;

class AccessDocumentPolicy
{
    use HandlesAuthorization;

    public function before(Person $user) {
        if ($user->hasRole(Role::ADMIN)) {
            return true;
        }
    }
    /**
     * Determine whether the user can view the AccessDocument.
     *
     * @param  \App\Models\Person  $user
     * @param  \App\Models\AccessDocument  $AccessDocument
     * @return mixed
     */
    public function index(Person $user, $personId)
    {
        return $user->hasRole(Role::MANAGE) || ($user->id == $personId);
    }

    /**
     * Determine whether the user can create AccessDocuments.
     *
     * @param  \App\Models\Person  $user
     * @return mixed
     */
    public function create(Person $user)
    {
        //
    }

    /**
     * Determine whether the user can update the AccessDocument.
     *
     * @param  \App\Models\Person  $user
     * @param  \App\AccessDocument  $AccessDocument
     * @return mixed
     */
    public function update(Person $user, AccessDocument $AccessDocument)
    {
        //
    }

    /**
     * Determine whether the user can delete the AccessDocument.
     *
     * @param  \App\Models\Person  $user
     * @param  \App\AccessDocument  $AccessDocument
     * @return mixed
     */
    public function delete(Person $user, AccessDocument $AccessDocument)
    {
        //
    }
}

<?php

namespace App\Policies;

use App\Models\Person;
use App\Models\Role;

use Illuminate\Auth\Access\HandlesAuthorization;

class PersonPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the person.
     *
     * @param  \App\Models\Person  $user
     * @param  \App\Person  $person
     * @return mixed
     */
    public function view(Person $person)
    {
        return true;
    }

    /**
     * Determine whether the user can create people.
     *
     * @param  \App\Models\Person  $user
     * @return mixed
     */
    public function create(Person $user)
    {
        return $this->user->hasRole(Role::ADMIN);
        //
    }

    /**
     * Determine whether the user can update the person.
     *
     * @param  \App\Models\Person  $user
     * @param  \App\Person  $person
     * @return mixed
     */
    public function update(Person $user, Person $person)
    {
        if ($user->id == $person->id) {
            return true;
        }

        return $user->hasRole([Role::ADMIN, Role::TRAINER, Role::MENTOR, Role::VC]);
    }

    /**
     * Determine whether the user can delete the person.
     *
     * @param  \App\Models\Person  $user
     * @param  \App\Person  $person
     * @return mixed
     */
    public function delete(Person $user, Person $person)
    {
        return $this->user->hasRole(Role::ADMIN);
    }

    /*
     * Determine wheter the user can change the password
     *
     * @param \App\Person $person
     */

     public function password(Person $user, Person $person) {
         if ($user->id == $person->id) {
             return true;
         }

         return $user->hasRole(Role::ADMIN);
     }
}

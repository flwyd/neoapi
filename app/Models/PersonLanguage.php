<?php

namespace App\Models;

use App\Models\ApihouseModel;

class PersonLanguage extends ApihouseModel
{
    protected $table = 'person_language';

    /*
     * All fields are mass-assignable
      * @var string
      */
    protected $guarded = [];

    /**
     * Don't use created_at/updated_at.
     * @var bool
     */
    public $timestamps = false;

    /*
     * Retrieve a comma-separated list of language spoken by a person
     * @var integer $person_id Person to lookup
     * @return string a command list of languages
     */

    public static function retrieveForPerson($person_id): string {
        $languages = self::where('person_id', $person_id)->pluck('language_name')->toArray();

        return join(', ', $languages);
    }

    /*
     * Update the languages spoken by a person
     * @var integer $person_id Person to update
     * @var string $spoken a comman separated language list
     */

    public static function updateForPerson($person_id, $language) {
        self::where('person_id', $person_id)->delete();

        $languages = explode(',', $language);

        foreach ($languages as $name) {
            $tongue = trim($name);

            if (empty($name)) {
                next;
            }

            self::create([ 'person_id' => $person_id, 'language_name' => $name]);
        }
    }
}

<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\Authenticatable;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

use App\Models\ApihouseModel;
use App\Models\PersonRole;

class Person extends ApihouseModel implements JWTSubject, AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable, Notifiable;

    const RESET_PASSWORD_EXPIRE = (3600 * 48);

    /**
     * The database table name.
     * @var string
     */
    protected $table = 'person';

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'active_next_event'           => 'bool',
        'asset_authorized'            => 'bool',
        'callsign_approved'           => 'bool',
        'has_note_on_file'            => 'bool',
        'on_site'                     => 'bool',
        'user_authorized'             => 'bool',
        'vehicle_blacklisted'         => 'bool',
        'vehicle_insurance_paperwork' => 'bool',
        'vehicle_paperwork'           => 'bool',

        'create_date'                 => 'datetime',
        'date_verified'               => 'date',
        'status_date'                 => 'date',
        'timestamp'                   => 'timestamp',
    ];

    protected $fillable = [
        'first_name',
        'mi',
        'last_name',
        'gender',

        'callsign',
        'callsign_approved',
        'formerly_known_as',

        'barcode',
        'status',
        'status_date',
        'timestamp',
        'user_authorized',


        'date_verified',
        'create_date',
        'email',
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
        'emergency_contact',

        'em_first_name',
        'em_mi',
        'em_last_name',
        'em_handle',

        'em_home_phone',
        'em_alt_phone',
        'em_email',
        'em_camp_location',
        'asset_authorized',

        'vehicle_blacklisted',
        'vehicle_paperwork',
        'vehicle_insurance_paperwork',

        'lam_status',
        'bpguid',
        'sfuid',

        'active_next_event',
        'has_note_on_file',
        'mentors_flag',
        'mentors_flag_note',
        'mentors_notes',

        // 'meta' objects
       'languages',
    ];

    protected $appends = [
        'years_rangered',
        'unread_message_count',
        'roles',
        'photo_url'
    ];

    /*
     * The years rangered (computed)
     * @var number
     */
     public $years_rangered;

    /*
     * Unread message count (computed)
     * @var number
     */

    public $unread_message_count;

    /*
     * The roles the person holds
     * @var array
     */

    public $roles;

    /*
     * The languages the person speaks. (handled thru class PersonLanguage)
     * @var string
     */

    public $languages;

    /*
     * holds the URL to the person's photo/mugshot
     */

     public $photo_url;

    /**
      * Get the identifier that will be stored in the subject claim of the JWT.
      *
      * @return mixed
      */
    public function getJWTIdentifier(): string
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims(): array
    {
        return [];
    }

    public static function findEmailOrFail(string $email)
    {
        return self::where('email', $email)->firstOrFail();
    }

    public static function findByCallsign(string $callsign)
    {
        return self::where('callsign', $callsign)->first();
    }

    /*
     * Search for matching callsigns
     *
     * @param string $query string to match against callsigns
     * @return array person id & callsigns which match
     */

    public static function searchCallsigns($query, $active)
    {
        $sql = DB::table('person')->where('callsign', 'like', '%'.$query.'%');

        if ($active) {
            $sql = $sql->whereIn('status', [ 'active', 'vintage', 'alpha']);
        }

        return $sql->limit(10)->get(['id', 'callsign']);
    }

    public function isValidPassword(string $password): bool
    {
        if (self::passwordMatch($this->password, $password)) {
            return true;
        }

        if ($this->tpassword_expire < time()) {
            return false;
        }

        return self::passwordMatch($this->tpassword, $password);
    }

    public static function passwordMatch($encyptedPw, $password): bool
    {
        list($salt, $sha) = explode(':', $encyptedPw);
        $hashedPw = sha1($salt.$password);

        return ($hashedPw == $sha);
    }

    public function changePassword(string $password): bool
    {
        $salt = self::generateRandomString();
        $sha = sha1($salt.$password);

        $this->password = "$salt:$sha";
        $this->tpassword = '';
        $this->tpassword_expire = 1;
        return $this->save();
    }

    public function createResetPassword(): string
    {
        $resetPassword = self::generateRandomString();
        $salt = self::generateRandomString();
        $sha = sha1($salt.$resetPassword);

        $this->tpassword = "$salt:$sha";
        $this->tpassword_expire = time() + self::RESET_PASSWORD_EXPIRE;
        $this->save();

        return $resetPassword;
    }

    public static function findForAuthentication(array $credentials)
    {
        $person = Person::where('email', $credentials['identification'])->first();

        if ($person && $person->isValidPassword($credentials['password'])) {
            return $person;
        }

        return false;
    }

    public function getRolesAttribute() {
        return $this->roles;
    }

    public function retrieveRoles(): void
    {
        $this->roles = PersonRole::retrieveForPerson($this->id);
    }

    public function hasRole($role): bool
    {
        if ($this->roles === null) {
            $this->retrieveRoles();
        }

        if (is_array($role)) {
            foreach ($role as $r) {
                if (in_array($r, $this->roles)) {
                    return true;
                }
            }
        } else {
            return in_array($role, $this->roles);
        }

//     if ($role != Role::ADMIN)
//        return in_array(Role::ADMIN, $this->roles);

        return false;
    }

    /*
     * Normalize the country
     */

    public function setCountryAttribute($country)
    {
        $c = strtoupper($country);
        $c = str_replace('.', '', $c);

        switch ($c) {
            case "US":
            case "USA":
            case "UNITED STATES":
            case "UNITED STATES OF AMERICA":
                $country = "USA";
                break;

            case "CA":
                $country = "Canada";
                break;

            case "FR":
                $country = "France";
                break;

            case "GB":
            case "UK":
                $country = "United Kingdom";
                break;
        }

        $this->attributes['country'] = $country;
    }

    /*
     * creates a random string by calling random.org, and falls back on a home-rolled.
     * @return the string.
     */

    public static function generateRandomString(): string
    {
        $length = 20;
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $max = strlen($characters)-1;
        $token = '';
        for ($i = 0; $i < $length; $i++) {
            $token .= $characters[mt_rand(0, $max)];
        }

        return $token;
    }

    public function getUnreadMessageCountAttribute() {
        return $this->unread_message_count;
    }

    public function getLanguagesAttribute() {
        return $this->languages;
    }

    public function getYearsRangeredAttribute() {
        return $this->years_rangered;
    }

    public function getPhotoUrlAttribute() {
        return $this->photo_url;
    }

}

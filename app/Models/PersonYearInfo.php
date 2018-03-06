<?php

namespace App\Models;

use App\Models\ApihouseResult;
use App\Models\TraineeStatus;
use App\Models\Slot;
use App\Models\RadioEligible;
use App\Models\Bmid;

use App\Helpers\DateHelper;

use Carbon\Carbon;

class PersonYearInfo extends ApihouseResult
{
    protected $results = [
        'person_id',
        'year',
        'training_status',
        'training_location',
        'training_date',
        'radio_eligible',
        'radio_max',
        'meals',
        'showers'
    ];

    public $person_id;
    public $year;

    public $training_status;
    public $training_date;
    public $training_location;
    public $radio_eligible;
    public $radio_max;
    public $meals;
    public $showers;

    /*
     * Gather all information related to a given year for a person
     * - training status & location (if any)
     * - radio eligibility
     * - meals & shower privileges
     * @var $personId - person to lookup
     * @var $year -
     * @return PersonYearInfo
     */

    static public function findForPersonYear($personId, $year) {
        $yearInfo = new PersonYearInfo();

        $yearInfo->person_id = $personId;
        $yearInfo->year = $year;
        $trainingStatus = TraineeStatus::findForPersonYear($personId, $year);

        if ($trainingStatus) {
            $yearInfo->training_location = $trainingStatus->description;
            if ($trainingStatus->begins) {
                $yearInfo->training_date = DateHelper::formatDate($trainingStatus->begins);

                if (Carbon::parse($trainingStatus->begins)->gt(Carbon::now())) {
                    $yearInfo->training_status = 'pending';
                }
            }

            if (!$yearInfo->training_status) {
                $yearInfo->training_status = ($trainingStatus->passed ? 'pass' : 'fail');
            }
        } else {
            $yearInfo->training_status = 'no-shift';
        }

        $radio = RadioEligible::findForPersonYear($personId, $year);
        $yearInfo->radio_max = $radio ? $radio->max_radios : 0;
        $yearInfo->radio_eligible = $yearInfo->radio_max > 0 ? true : false;

        $bmid = Bmid::findForPersonYear($personId, $year);
        if ($bmid) {
            $yearInfo->meals = $bmid->meals;
            $yearInfo->showers = $bmid->showers;
        } else {
            $yearInfo->meals = '';
            $yearInfo->showers = false;
        }

        return $yearInfo;
    }
}

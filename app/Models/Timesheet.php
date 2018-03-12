<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Models\ApihouseModel;
use App\Helpers\DateHelper;

class Timesheet extends ApihouseModel
{
    const EXCLUDE_POSITIONS_FOR_YEARS = [
      1,     // Alpha
      13,    // Training
      29,    // Not in databse?!
      30,    // Not in database?
    ];

    protected $table = 'timesheet';

    protected $fillable = [
        'person_id',
        'position_id',
        'on_duty',
        'off_duty',
    ];

    protected $appends = [
        'position_title',
        'duration',
        'credits',
        'past_start_time',
    ];

    protected $casts = [
        'on_duty'   => 'datetime',
        'off_duty'  => 'datetime',
    ];

    public $credits;

    public static function findForQuery($query)
    {
        $year = 0;
        $sql = self::select('timesheet.*', 'position.title as position_title');

        if (isset($query['year'])) {
            $year = $query['year'];
            $sql = $sql->whereRaw('YEAR(on_duty)=?', $year);
        }

        if (isset($query['person_id'])) {
            $sql = $sql->where('person_id', $query['person_id']);
        }

        $rows =  $sql->join('position', 'position.id', '=', 'timesheet.position_id')
                ->orderBy('on_duty', 'asc', 'off_duty', 'asc')
                ->get();


        $creditCache = $year ? PositionCredit::findForYear($year) : null;

        foreach ($rows as $row) {
            if ($row->off_duty) {
                $row->credits = PositionCredit::computeCredits(
                        $row->position_id,
                        $row->getOriginal('on_duty'),
                        $row->getOriginal('off_duty'),
                        $creditCache
                    );
            } else {
                $row->credits = 0;
            }
        }

        return $rows;
    }

    /*
     * Find the years a person was on working
     * @var integer $id person to lookup
     */

    public static function yearsRangered($personId, $everything=false)
    {
        $query = self::selectRaw("YEAR(on_duty) as year")
                ->where('person_id', $personId)
                ->groupBy("year")
                ->orderBy("year", "asc");

        if (!$everything) {
            $query = $query->whereNotIn("position_id", self::EXCLUDE_POSITIONS_FOR_YEARS);
        }

        return $query->pluck('year')->toArray();
    }

    public function getDurationAttribute()
    {
        $on_duty = $this->getOriginal('on_duty');
        $off_duty = $this->getOriginal('off_duty');

        if ($on_duty) {
            if ($off_duty) {
                return Carbon::parse($off_duty)->diffInSeconds(Carbon::parse($on_duty));
            }

            return Carbon::now()->diffInSeconds(Carbon::parse($on_duty));
        } else {
            return 0;
        }
    }

    public function getOnDutyAttribute()
    {
        $on_duty = $this->getOriginal('on_duty');
        if ($on_duty) {
            return DateHelper::formatShift($on_duty);
        } else {
            return "N/A";
        }
    }

    public function getOffDutyAttribute()
    {
        $off_duty = $this->getOriginal('off_duty');
        if ($off_duty) {
            return DateHelper::formatShift($off_duty);
        } else {
            return "On Duty";
        }
    }

}

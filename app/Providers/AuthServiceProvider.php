<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\Person;
use App\Models\PersonMessage;
use App\Models\Position;
use App\Models\Schedule;
use App\Models\Timesheet;

use App\Policies\PersonPolicy;
use App\Policies\PersonMessagePolicy;
use App\Policies\PositionPolicy;
use App\Policies\SchedulePolicy;
use App\Policies\TimesheetPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Person::class => PersonPolicy::class,
        PersonMessage::class => PersonMessagePolicy::class,
        Position::class => PositionPolicy::class,
        Schedule::class => SchedulePolicy::class,
        Timesheet::class => TimesheetPolicy::class,
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::resource('person', 'PersonPolicy');
        //
    }
}

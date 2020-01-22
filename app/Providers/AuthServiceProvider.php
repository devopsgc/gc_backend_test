<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Record;
use App\Models\Campaign;
use App\Policies\UserPolicy;
use App\Policies\RecordPolicy;
use App\Policies\CampaignPolicy;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        User::class => UserPolicy::class,
        Record::class => RecordPolicy::class,
        Campaign::class => CampaignPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //Gate::resource('users', 'UserPolicy');
        //Gate::resource('records', 'RecordPolicy');
    }
}

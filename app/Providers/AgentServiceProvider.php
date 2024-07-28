<?php

namespace App\Providers;

use App\Helpers\AgentHelper;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;

class AgentServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(AgentHelper::class, function ($app) {
            $userAgent = $app->make(Request::class)->header('User-Agent');
            return new AgentHelper($userAgent);
        });
    }

    public function boot()
    {
        //
    }
}

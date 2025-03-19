<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})
    ->purpose('Display an inspiring quote')
    ->hourly();

/*
|--------------------------------------------------------------------------
| Application Status Commands
|--------------------------------------------------------------------------
|
| Useful diagnostic commands to check system health and configuration
|
*/

Artisan::command('app:status', function () {
    $this->info('Application Environment: ' . app()->environment());
    $this->info('Cache Driver: ' . config('cache.default'));
    $this->info('Database Connection: ' . config('database.default'));
    $this->info('Total Users: ' . User::count());
})
    ->purpose('Display application status and configuration');

/*
|--------------------------------------------------------------------------
| Database Maintenance Commands
|--------------------------------------------------------------------------
|
| Commands for regular database maintenance tasks
|
*/

Artisan::command('db:optimize', function () {
    $this->call('optimize:clear');
    $this->info('Running database maintenance...');
    // Add your database optimization logic here
    $this->info('Database optimization complete.');
})
    ->purpose('Perform database optimization tasks')
    ->daily();

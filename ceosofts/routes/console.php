<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Inspire Command
|--------------------------------------------------------------------------
|
| This command will display an inspiring quote when executed.
| It is also scheduled to run hourly.
|
*/

Artisan::command('inspire', function () {
    // Output an inspiring quote using the Inspiring facade
    $this->comment(Inspiring::quote());
})
    ->purpose('Display an inspiring quote')
    ->hourly();

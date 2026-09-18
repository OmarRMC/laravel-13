<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('app:recordar-eventos')->dailyAt('08:00');
Schedule::command('app:cerrar-eventos-pasados')->hourly();

// Schedule::exec("curl --location 'http://localhost:8000/api/v1/eventos' --header 'Accept: application/json'")->everySecond();
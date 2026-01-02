<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule commands
Schedule::command('invoices:mark-overdue')->dailyAt('08:00');

// Payment reminder emails (staggered to prevent database contention)
Schedule::command('invoices:send-reminders --days=7')->dailyAt('09:00');
Schedule::command('invoices:send-reminders --days=14')->dailyAt('09:05');
Schedule::command('invoices:send-reminders --days=30')->dailyAt('09:10');

// Mark expired quotes
Schedule::command('quotes:expire')->dailyAt('08:00');

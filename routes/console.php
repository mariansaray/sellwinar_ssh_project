<?php

use Illuminate\Support\Facades\Schedule;

// Process email and SMS reminders every minute
Schedule::command('sellwinar:process-emails')->everyMinute();
Schedule::command('sellwinar:process-sms')->everyMinute();

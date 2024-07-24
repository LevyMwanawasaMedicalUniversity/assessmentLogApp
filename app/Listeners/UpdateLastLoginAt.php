<?php

namespace App\Listeners;

use DateTime;
use DateTimeZone;
use Illuminate\Auth\Events\Authenticated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UpdateLastLoginAt
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Authenticated $event)
    {

        $dateTime = new DateTime();
        $dateTime->setTimezone(new DateTimeZone('+02:00'));

        // Set the last_login_at field to the current date and time with the specified time zone
        $event->user->last_login_at = $dateTime->format('Y-m-d H:i:s');
        $event->user->save();
    }
}

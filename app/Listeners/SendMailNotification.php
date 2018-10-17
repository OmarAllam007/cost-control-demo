<?php

namespace App\Listeners;

use App\Events\ChangeRequestCreated;
use Illuminate\Mail\Message;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendMailNotification implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  ChangeRequestCreated $event
     * @return void
     */
    public function handle(ChangeRequestCreated $event)
    {

    }
}

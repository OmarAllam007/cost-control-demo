<?php

namespace App\Listeners;

use App\BudgetChangeRequest;
use Illuminate\Mail\Message;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ChangeRequestSubscriber
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public $change_request;

    const Create = 'mail.change-request.create';
    const Reassign = 'mail.change-request.reassign';
    const Close = 'mail.change-request.close';


    public function createChangeRequest($event)
    {
        $change_request = $event->change_request;
        $this->sendEmail(self::Create, $change_request, [$change_request->assigned_to()->first()->email]);
    }

    public function reassignChangeRequest($event)
    {
        $change_request = $event->change_request;
        $this->sendEmail(self::Reassign, $change_request, [$change_request->assigned_to()->first()->email]);
    }

    public function closeChangeRequest($event)
    {
        $change_request = $event->change_request;
        /** @var BudgetChangeRequest $change_request */
        $to = [
            $change_request->assigned_to()->first()->email,
            $change_request->project->owner()->first()->email,
            $change_request->created_by()->first()->email,
            $change_request->project->cost_owner()->first()->email,
        ];
        $this->sendEmail(self::Close, $change_request, $to);
    }

    public function sendEmail($view, $change_request, $to)
    {
        foreach ($to as $user) {
            \Mail::send($view, [
                'change_request' => $change_request,
                'user' => $user,
            ], function (Message $message) use ($change_request, $user) {
                $message->to($user);
                $message->subject('[KPS] Change Request  #' . $change_request->id . ' Closed');
            });
        }
    }


    /**
     * Handle the event.
     *
     * @param  object $event
     * @return void
     */

    public function subscribe($events)
    {
        $events->listen(
            'App\Events\ChangeRequestCreated',
            'App\Listeners\ChangeRequestSubscriber@createChangeRequest'
        );

        $events->listen(
            'App\Events\ChangeRequestReassign',
            'App\Listeners\ChangeRequestSubscriber@reassignChangeRequest'
        );

        $events->listen(
            'App\Events\ChangeRequestClosed',
            'App\Listeners\ChangeRequestSubscriber@closeChangeRequest'
        );

    }
//
//    public function handle($event)
//    {
//        //
//    }
}

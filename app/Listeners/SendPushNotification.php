<?php

namespace App\Listeners;

use App\Events\PushNotification;
use App\Services\PushNotification as ServicesPushNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendPushNotification
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
    public function handle(PushNotification $event)
    {
        $image = $event->image;
        if (!empty($image)) {
            (new ServicesPushNotification())->sendToOne($event->device_id, $event->title, $event->message, $image);
        }else{
            (new ServicesPushNotification())->sendToOne($event->device_id, $event->title, $event->message);
        }
    }
}

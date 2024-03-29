<?php

namespace App\Listeners;

use App\Contracts\ShouldDeferRateLimitedNotification;
use App\Models\DeferredNotification;
use Illuminate\Support\Facades\Log;
use Jamesmills\LaravelNotificationRateLimit\Events\NotificationRateLimitReached;

class DeferRateLimitedNotificationListener
{
    /**
     * Handle the event.
     */
    public function handle(NotificationRateLimitReached $event): void
    {
        if ($event->notification instanceof ShouldDeferRateLimitedNotification) {
            $deferredNotification = DeferredNotification::create([
                'key' => $event->key,
                'notify_at' => now('UTC')->addSeconds($event->availableIn),
                'notification_payload' => serialize($event->notification),
                'notifiable_payload' => serialize($event->notifiable),
            ]);

            Log::debug('DeferRateLimitedNotificationListener: Deferred notification ' . $deferredNotification->id . ' created for ' . $event->key);
        }
    }
}

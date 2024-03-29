<?php

namespace App\Services;

use App\Models\DeferredNotification;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Date;

class DeferredNotificationDispatchService
{
    public function dispatchDeferredNotifications(): void
    {
        $dispatchableNotifications = $this->getDispatchableNotifications();

        // Grouping by cache key will lump all notifications of the same
        // type (i.e. same notification type, same recipient) together into a group.
        foreach($dispatchableNotifications->groupBy('key') as $cache_key => $notificationGroup)
        {
            try {
                $this->dispatchGroup($notificationGroup);
            } catch (\Exception $e) {
                Log::error('Failed to dispatch deferred notifications for group ' . $cache_key . ': ' . $e->getMessage());
                continue;
            }

            $notificationGroup->each(function (DeferredNotification $deferredNotification) use ($cache_key) {
                try {
                    $deferredNotification->delete();
                } catch (\Exception $e) {
                    Log::error('Failed to delete deferred (now sent) notification ' . $deferredNotification->id . ' in group ' . $cache_key . ': ' . $e->getMessage());
                }
            });
        }
    }

    protected function getDispatchableNotifications(): Collection
    {
        return DeferredNotification::query()
            ->where('notify_at', '<=', Date::now('UTC'))
            ->orderBy('key')
            ->orderBy('notify_at', 'asc')
            ->orderBy('created_at', 'asc')
            ->get();
    }

    /**
     * @param Collection<DeferredNotification> $notificationGroup
     * @return void
     */
    protected function dispatchGroup(Collection $notificationGroup): void
    {
        // Actually dispatch only the most recently added notification
        $deferredNotification = $notificationGroup->last();

        $notification = unserialize($deferredNotification->notification_payload);
        $notifiable = unserialize($deferredNotification->notifiable_payload);

        Log::debug('DeferredNotificationDispatchService: Sending deferred notification ' . $deferredNotification->id . ' created for ' . $deferredNotification->key);
        Notification::sendNow($notifiable, $notification);
    }
}

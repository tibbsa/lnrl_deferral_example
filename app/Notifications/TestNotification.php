<?php
/*
 * Dummy test notification.  It simply includes a 'dispatchDate' that tells us the
 * date/time that the notification was actually created (although it may only
 * be sent some time later).
 */
namespace App\Notifications;

use App\Contracts\ShouldDeferRateLimitedNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Date;
use Jamesmills\LaravelNotificationRateLimit\RateLimitedNotification;
use Jamesmills\LaravelNotificationRateLimit\ShouldRateLimit;

class TestNotification extends Notification implements ShouldRateLimit, ShouldDeferRateLimitedNotification
{
    use Queueable;
    use RateLimitedNotification;

    // By default, cache keys include a complete serialization of the notification
    // itself.  Since there may be some slight differences in notifications from one
    // to the next, we turn this off so that each will be considered.
    protected bool $shouldRateLimitUniqueNotifications = false;

    // Rate limit for 2 minutes for demonstration purposes
    protected int $rateLimitForSeconds = 120;

    public function __construct(public ?Carbon $dispatchTime = null)
    {
        $this->dispatchTime ??= Date::now('UTC');
    }

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)->line(
            '- Test notification sent at ' . $this->dispatchTime->toDateTimeLocalString(),
        );
    }
}

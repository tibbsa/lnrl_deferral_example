# Laravel Notification Rate Limit 'Deferred Notifications' Example App

The [Laravel Notification Rate Limit package](https://github.com/jamesmills/laravel-notification-rate-limit), originally 
developed by [@jamesmills](https://github.com/jamesmills) allows for noficiations to be 
skipped if a certain rate limit has been exceeded (for example, if the last notification was sent
too recently).

A user queried whether it was possible to not simply discard rate limited notifications, but instead to wait until
the limiter had expired and *then* deliver the (most recently attempted) notification: see [Issue 33](https://github.com/jamesmills/laravel-notification-rate-limit/issues/33). 
While that use case may be beyond the scope of what Laravel-Notification-Rate-Limit should do, this repo contains
a sample implementation demonstrating how the package could be used to facilitate such a system.

_This repo is provided for demonstration purposes only. It has not been extensively tested and caution should be
exercised before deploying into production code._

## Requirements

- PHP 8.2
- Laravel 11 (although there is no reason this could not be implemented in earlier versions)
- Laravel-Notification-Rate-Limit package (v3.0.0+)

## How It Works

When a notification is being rate limited, Laravel-Notification-Rate-Limit generates a `NotificationRateLimitedEvent`.
This app intercepts that event, and instead of simply discarding the notification, it stores a serialized copy of
the notification and the intended recipient (notifiable) into the database, with a `notify_at` dater/time indicating 
when the earliest time is that the notification may be dispatched.

Using the Laravel scheduler, once per minute, a dispatch service is executed to check for any notifications that
are waiting to be sent whose `notify_at` property is in the past. The most recent of those notifications is 
reconstituted from the database, and sent to the recipient. All other past notifications of the same type
are then discarded.

To facilitate testing, an Artisan command is also provided (`app:dispatch-deferred-notifications`) to trigger
the dispatch service. To generate test notifications, you can run the app (e.g. with `php artisan serve`) and
then visit `http://127.0.0.1:8000/send-notification-to-user` or `http://127.0.0.1:8000/send-notification-to-anonymous`.

## License

This repository contains open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

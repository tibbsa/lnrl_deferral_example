<?php

namespace App\Console\Commands;

use App\Services\DeferredNotificationDispatchService;
use Illuminate\Console\Command;

class DispatchDeferredNotificationsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:dispatch-deferred-notifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dispatches any deferred notifications that are now ready for sending';

    public function handle(DeferredNotificationDispatchService $service): int
    {
        $service->dispatchDeferredNotifications();

        return 0;
    }
}

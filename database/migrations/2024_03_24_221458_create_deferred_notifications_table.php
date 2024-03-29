<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deferred_notifications', function (Blueprint $table) {
            $table->id();

            $table->longText('key');
            $table->timestamp('notify_at')->index();

            $table->longText('notification_payload');
            $table->longText('notifiable_payload');

            $table->timestamps();
        });
    }
};

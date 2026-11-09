<?php

namespace App\Listeners;

use App\Models\Admin;
use App\Services\DeviceTrackingService;
use Illuminate\Auth\Events\Login;

class RecordLoginHistory
{
    public function __construct(
        protected DeviceTrackingService $deviceTrackingService
    ) {
    }

    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        try {
            $user = $event->user;
            $guard = $event->guard ?? 'web';
            $userType = ($guard === 'admin' || $user instanceof Admin) ? 'admin' : 'user';

            $this->deviceTrackingService->recordLogin(
                $userType,
                $user?->id,
                $user?->email,
                request(),
                'successful'
            );
        } catch (\Throwable $e) {
            // Non-blocking log recording
        }
    }
}

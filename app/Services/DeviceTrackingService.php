<?php

namespace App\Services;

use App\Models\LoginHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Laravel\Jetstream\Agent;

class DeviceTrackingService
{
    /**
     * Record a login event into login_histories.
     */
    public function recordLogin(
        string $userType,
        ?int $userId,
        ?string $email,
        Request $request,
        string $status = 'successful'
    ): LoginHistory {
        $agent = $this->createAgent($request);

        $platform = $agent->platform() ?: 'Unknown OS';
        $browser = $agent->browser() ?: 'Unknown Browser';

        $deviceType = 'Desktop';
        if ($agent->isPhone() || $agent->isMobile()) {
            $deviceType = 'Mobile';
        } elseif ($agent->isTablet()) {
            $deviceType = 'Tablet';
        }

        return LoginHistory::create([
            'user_type' => $userType,
            'user_id' => $userId,
            'email' => $email,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'device_type' => $deviceType,
            'browser' => $browser,
            'platform' => $platform,
            'status' => $status,
        ]);
    }

    /**
     * Get active sessions for a user or admin from the database sessions table.
     */
    public function getActiveSessions(?int $userId, Request $request): array
    {
        if (!Schema::hasTable('sessions') || config('session.driver') !== 'database') {
            return [];
        }

        $rawSessions = DB::table('sessions')
            ->where('user_id', $userId)
            ->orderBy('last_activity', 'desc')
            ->get();

        $currentSessionId = $request->hasSession() ? $request->session()->getId() : null;

        return $rawSessions->map(function ($session) use ($currentSessionId) {
            $agent = new Agent();
            $agent->setUserAgent($session->user_agent);

            $deviceType = 'Desktop';
            if ($agent->isPhone() || $agent->isMobile()) {
                $deviceType = 'Mobile';
            } elseif ($agent->isTablet()) {
                $deviceType = 'Tablet';
            }

            return (object) [
                'id' => $session->id,
                'ip_address' => $session->ip_address,
                'is_current_device' => $session->id === $currentSessionId,
                'last_active' => Carbon::createFromTimestamp($session->last_activity)->diffForHumans(),
                'last_active_exact' => Carbon::createFromTimestamp($session->last_activity)->toDateTimeString(),
                'platform' => $agent->platform() ?: 'Unknown OS',
                'browser' => $agent->browser() ?: 'Unknown Browser',
                'device_type' => $deviceType,
            ];
        })->all();
    }

    /**
     * Terminate an active session by its ID.
     */
    public function terminateSession(string $sessionId, ?int $userId): bool
    {
        if (!Schema::hasTable('sessions')) {
            return false;
        }

        $query = DB::table('sessions')->where('id', $sessionId);
        if ($userId !== null) {
            $query->where('user_id', $userId);
        }

        return (bool) $query->delete();
    }

    /**
     * Terminate all other browser sessions for a user except the current one.
     */
    public function terminateOtherSessions(int $userId, string $currentSessionId): int
    {
        if (!Schema::hasTable('sessions')) {
            return 0;
        }

        return DB::table('sessions')
            ->where('user_id', $userId)
            ->where('id', '!=', $currentSessionId)
            ->delete();
    }

    /**
     * Get recent login history for a user or admin.
     */
    public function getRecentHistory(string $userType, ?int $userId, int $limit = 10)
    {
        return LoginHistory::where('user_type', $userType)
            ->when($userId, fn($q) => $q->where('user_id', $userId))
            ->latest()
            ->take($limit)
            ->get();
    }

    /**
     * Create an Agent instance from request.
     */
    protected function createAgent(Request $request): Agent
    {
        $agent = new Agent();
        $agent->setUserAgent($request->userAgent());
        return $agent;
    }
}

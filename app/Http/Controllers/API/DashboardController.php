<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function stats(Request $request): JsonResponse
    {
        $user = $request->user();

        $stats = Cache::remember("dashboard:{$user->id}", 60, function () use ($user) {
            $subscription = $user->getOrCreateSubscription();

            return [
                'total_messages'      => $user->messages()->count(),
                'completed_messages'  => $user->messages()->where('status', 'completed')->count(),
                'pending_messages'    => $user->messages()->whereIn('status', ['pending', 'processing'])->count(),
                'failed_messages'     => $user->messages()->where('status', 'failed')->count(),
                'subscription'        => [
                    'plan'        => $subscription->plan_name,
                    'limit'       => $subscription->request_limit,
                    'used'        => $subscription->used_requests,
                    'remaining'   => $subscription->remainingRequests(),
                    'reset_at'    => $subscription->reset_at?->toIso8601String(),
                ],
                'messages_by_category' => $user->messages()
                    ->where('status', 'completed')
                    ->selectRaw('category, COUNT(*) as count')
                    ->groupBy('category')
                    ->pluck('count', 'category'),
            ];
        });

        return response()->json(['data' => $stats]);
    }
}

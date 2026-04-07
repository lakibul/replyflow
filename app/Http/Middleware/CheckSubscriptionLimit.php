<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscriptionLimit
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $subscription = $this->getCachedSubscription($user->id);

        if ($subscription->hasReachedLimit()) {
            return response()->json([
                'message' => 'Monthly request limit reached.',
                'data'    => [
                    'plan'            => $subscription->plan_name,
                    'limit'           => $subscription->request_limit,
                    'used'            => $subscription->used_requests,
                    'remaining'       => 0,
                    'upgrade_url'     => '/api/subscription/upgrade',
                ],
            ], JsonResponse::HTTP_TOO_MANY_REQUESTS);
        }

        return $next($request);
    }

    private function getCachedSubscription(int $userId): \App\Models\Subscription
    {
        $cacheKey = "subscription:{$userId}";

        // Cache for 5 minutes to reduce DB hits
        return Cache::remember($cacheKey, 300, function () use ($userId) {
            $user = \App\Models\User::find($userId);

            return $user->getOrCreateSubscription();
        });
    }
}

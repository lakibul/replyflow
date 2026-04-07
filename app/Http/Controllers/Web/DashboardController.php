<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user         = $request->user();
        $subscription = $user->getOrCreateSubscription();

        $stats = [
            'total'      => $user->messages()->count(),
            'completed'  => $user->messages()->where('status', 'completed')->count(),
            'pending'    => $user->messages()->whereIn('status', ['pending', 'processing'])->count(),
            'failed'     => $user->messages()->where('status', 'failed')->count(),
            'categories' => $user->messages()
                ->where('status', 'completed')
                ->whereNotNull('category')
                ->selectRaw('category, COUNT(*) as count')
                ->groupBy('category')
                ->orderByDesc('count')
                ->pluck('count', 'category'),
        ];

        $recentMessages = $user->messages()->latest()->limit(5)->get();

        return view('dashboard.index', compact('stats', 'subscription', 'recentMessages'));
    }
}

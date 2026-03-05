<?php

namespace App\Http\Controllers;

use App\Models\AiUsageLog;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $hour = now()->hour;

        $greeting = match(true) {
            $hour < 12 => 'morning',
            $hour < 17 => 'afternoon',
            default    => 'evening',
        };

        $claudeTokens   = $user->aiUsageLogs()->whereMonth('created_at', now()->month)->where('model', 'claude')->sum('total_tokens');
        $deepseekTokens = $user->aiUsageLogs()->whereMonth('created_at', now()->month)->where('model', 'deepseek')->sum('total_tokens');

        return view('dashboard.index', [
            'greeting'          => $greeting,
            'total_projects'    => $user->projects()->count(),
            'deployed_projects' => $user->projects()->where('status', 'deployed')->count(),
            'today_requests'    => $user->aiUsageLogs()->whereDate('created_at', today())->count(),
            'month_tokens'      => $claudeTokens + $deepseekTokens,
            'claude_tokens'     => $claudeTokens,
            'deepseek_tokens'   => $deepseekTokens,
            'recent_projects'   => $user->projects()->latest()->limit(5)->get(),
        ]);
    }
}

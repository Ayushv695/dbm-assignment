<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    //private const CACHE_TTL = 300; // 5 minutes

    public function getAnalytics(): array
    {
        // Cache::forget('dashboard_analytics');
        return Cache::remember('dashboard_analytics', config('cache.ttl') * 5, function () {
            return [
                'total_projects'       => Project::count(),
                'total_tasks'          => Task::count(),
                'tasks_by_status'      => Task::select('status', DB::raw('count(*) as count'))
                                            ->groupBy('status')->pluck('count', 'status'),

                'tasks_by_priority'    => Task::select('priority', DB::raw('count(*) as count'))
                                            ->groupBy('priority')->pluck('count', 'priority'),

                'overdue_tasks'        => Task::where('due_date', '<', now())
                                            ->whereNotIn('status', ['completed'])->count(),

                'top_employees'        => User::where('role', 'employee')->select('id', 'name', 'email')
                                            ->withCount(['tasks as completed_tasks' => fn ($q) => $q->where('status', 'completed')])
                                            ->orderByDesc('completed_tasks')
                                            ->limit(5)
                                            ->get(),
            ];
        });
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Resources\TaskResource;
use App\Models\Task;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    //
    public function index()
    {
        $totalPendingTasks = Task::where('status', 'pending')->count();
        $myPendingTasks = Task::where('status', 'pending')->where('assigned_user_id', auth()->id())->count();
        $totalInProgressTasks = Task::where('status', 'in_progress')->count();
        $myInProgressTasks = Task::where('status', 'in_progress')->where('assigned_user_id', auth()->id())->count();
        $totalCompletedTasks = Task::where('status', 'completed')->count();
        $myCompletedTasks = Task::where('status', 'completed')->where('assigned_user_id', auth()->id())->count();

        $activeTasks = Task::whereIn('status', ['pending', 'in_progress'])->where('assigned_user_id', auth()->id())->limit(10)->get();
        $activeTasks = TaskResource::collection($activeTasks);
        return Inertia::render('Dashboard', [
            'totalPendingTasks' => $totalPendingTasks,
            'myPendingTasks' => $myPendingTasks,
            'totalInProgressTasks' => $totalInProgressTasks,
            'myInProgressTasks' => $myInProgressTasks,
            'totalCompletedTasks' => $totalCompletedTasks,
            'myCompletedTasks' => $myCompletedTasks,
            'activeTasks' => $activeTasks,
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\TaskResource;
use App\Http\Resources\UserResource;
use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $query = Task::query()->with('project');

        $sortField = request('sort_field', 'created_at');
        $sortDirection = request('sort_direction', 'desc');

        if (request('name')) {
            $query = $query->where('name', 'like', '%' . request('name') . '%');
        }
        if (request('status')) {
            $query = $query->where('status', request('status'));
        }
        $tasks = $query->orderBy($sortField, $sortDirection)->paginate(10)->onEachSide(1);

        return Inertia::render('Task/Index', [
            'tasks' => TaskResource::collection($tasks),
            'queryParams' => request()->query() ?: null,
            'success' => session('success')
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        $projects = Project::orderBy('name', 'asc')->get();
        $users = User::all();
        return Inertia::render('Task/Create', [
            'projects' => ProjectResource::collection($projects),
            'users' => UserResource::collection($users),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaskRequest $request)
    {
        //
        $data = $request->validated();
        $image = $data['image'] ?? null;
        $data['created_by'] = auth()->id();
        $data['updated_by'] = auth()->id();
        if ($image) {
            $data['image'] = $image->store('tasks', 'public');
        }
        $task = Task::create($data);

        return to_route('task.index')->with('success', 'Task created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        //
        return Inertia::render('Task/Show', [
            'task' => new TaskResource($task)
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task)
    {
        //
        $projects = Project::orderBy('name', 'asc')->get();
        $users = User::all();
        return Inertia::render('Task/Edit', [
            'task' => new TaskResource($task),
            'projects' => ProjectResource::collection($projects),
            'users' => UserResource::collection($users),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaskRequest $request, Task $task)
    {
        //
        $data = $request->validated();
        $image = $data['image'] ?? null;
        $data['updated_by'] = auth()->id();
        if ($image) {
            if ($task->image) {
                Storage::disk('public')->delete($task->image);
            }
            $data['image'] = $image->store('tasks', 'public');
        }
        $task->update($data);

        return to_route('task.index')->with('success', 'Task updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        //
        if ($task->image) {
            Storage::disk('public')->delete($task->image);
        }
        $task->delete();
        return to_route('task.index')->with('success', 'Task deleted successfully');
    }

    public function myTasks()
    {
        $user = auth()->user();
        $query = Task::query()->with('project')->where('assigned_user_id', $user->id);

        $sortField = request('sort_field', 'created_at');
        $sortDirection = request('sort_direction', 'desc');

        if (request('name')) {
            $query = $query->where('name', 'like', '%' . request('name') . '%');
        }
        if (request('status')) {
            $query = $query->where('status', request('status'));
        }
        $tasks = $query->orderBy($sortField, $sortDirection)->paginate(10)->onEachSide(1);
        return Inertia::render('Task/Index', [
            'tasks' => TaskResource::collection($tasks),
            'queryParams' => request()->query() ?: null,
            'success' => session('success')
        ]);
    }
}

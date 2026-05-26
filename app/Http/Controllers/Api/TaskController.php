<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    private TaskService $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    public function index()
    {
        $tasks = $this->taskService->getTasks(request()->all());

        return response()->json([
            'success' => true,
            'message' => 'Tasks fetched successfully',
            'data' => $tasks,
        ]);
    }

    public function store(CreateTaskRequest $request) {
        return $this->taskService->create($request,$request->validated());
    }

    public function show(string $task_id)
    {
        return $this->taskService->view($task_id);
    }

    public function update(UpdateTaskRequest $request,String $task_id) {
        return $this->taskService->update($request , $task_id, $request->validated());
    }

    public function destroy(string $task_id)
    {
        return $this->taskService->delete($task_id);
    }
}

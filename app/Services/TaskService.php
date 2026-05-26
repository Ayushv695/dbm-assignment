<?php

namespace App\Services;

use App\Models\Task;
use App\Traits\AttachmentUploadTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Jobs\SendTaskAssignedJob;

class TaskService
{
    use AttachmentUploadTrait;

    // public function getTasks(array $filters)
    // {
    //     $query = Task::with(['project', 'assignedTo']);

    //     // Employee can only see assigned tasks
    //     if (auth()->user()->role === 'employee') {
    //         $query->where('assigned_to',auth()->id());
    //     }

    //     if (!empty($filters['status'])) {
    //         $query->where('status', $filters['status']);
    //     }

    //     if (!empty($filters['priority'])) {
    //         $query->where('priority', $filters['priority']);
    //     }

    //     if (!empty($filters['assigned_to'])) {
    //         $query->where('assigned_to', $filters['assigned_to']);
    //     }

    //     if (!empty($filters['search'])) {
    //         $query->where(function ($q) use ($filters) {

    //             $q->where('title', 'LIKE', '%' . $filters['search'] . '%')
    //               ->orWhere('description', 'LIKE', '%' . $filters['search'] . '%');
    //         });
    //     }

    //     $sortBy = $filters['sort_by'] ?? 'created_at';

    //     $sortOrder = $filters['sort_order'] ?? 'desc';

    //     $allowedSorts = [
    //         'id',
    //         'title',
    //         'status',
    //         'priority',
    //         'due_date',
    //         'created_at'
    //     ];

    //     if (!in_array($sortBy, $allowedSorts)) {
    //         $sortBy = 'created_at';
    //     }

    //     $query->orderBy($sortBy, $sortOrder);

    //     return $query->paginate(
    //         $filters['per_page'] ?? 10
    //     );
    // }

    
    public function getTasks(array $filters)
    {
        $cacheKey = 'tasks_' . md5(json_encode($filters));
        $keys = Cache::get('task_cache_keys', []);
        $keys[] = $cacheKey;
        Cache::forever('task_cache_keys',array_unique($keys));

        return Cache::remember($cacheKey,config('cache.ttl') * 5, function() use($filters){

            $query = Task::with(['project', 'assignedTo']);

            if (auth()->user()->role === 'employee') {
                $query->where('assigned_to',auth()->id());
            }

            if (!empty($filters['status'])) {
                $query->where('status', $filters['status']);
            }

            if (!empty($filters['priority'])) {
                $query->where('priority', $filters['priority']);
            }

            if (!empty($filters['assigned_to'])) {
                $query->where('assigned_to', $filters['assigned_to']);
            }

            if (!empty($filters['search'])) {
                $query->where(function ($q) use ($filters) {

                    $q->where('title', 'LIKE', '%' . $filters['search'] . '%')
                    ->orWhere('description', 'LIKE', '%' . $filters['search'] . '%');
                });
            }

            $sortBy = $filters['sort_by'] ?? 'created_at';

            $sortOrder = $filters['sort_order'] ?? 'desc';

            $allowedSorts = [
                'id',
                'title',
                'status',
                'priority',
                'due_date',
                'created_at'
            ];

            if (!in_array($sortBy, $allowedSorts)) {
                $sortBy = 'created_at';
            }

            $query->orderBy($sortBy, $sortOrder);

            return $query->paginate(
                $filters['per_page'] ?? 10
            );

        });
    }

    public function create(Request $request, array $data)
    {
        $data['attachment'] = $this->uploadAttachment($request,'attachment',Task::ATTACHMENT);
        $task = Task::create($data);

        SendTaskAssignedJob::dispatch($task);

        return response()->json([
            'success' => true,
            'message' => 'Task created successfully',
            'data' => $task,
        ], 201);
    }

    public function update(Request $request, string $task_id, array $data)
    {
        try {

            $task = Task::findOrFail($task_id);
            $oldAssignedUser = $task->assigned_to;
            $data['attachment'] = $this->updateAttachment($request,$task,'attachment',Task::ATTACHMENT);
            $task->update($data);

            // If reassigned
            if (isset($data['assigned_to']) && $oldAssignedUser != $data['assigned_to']) {
                SendTaskAssignedJob::dispatch($task);
            }

            return response()->json([
                'status' => true,
                'message' => 'Task updated successfully',
                'data' => $task
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 404);
        }
    }

    public function delete(string $task_id)
    {
        try {

            $task = Task::findOrFail($task_id);
            $this->deleteAttachment($task->attachment,Task::ATTACHMENT);
            $task->delete();

            return response()->json([
                'status' => true,
                'message' => 'Task deleted successfully'
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 404);
        }
    }

    public function view(string $task_id)
    {
        try {
            $query = Task::with('project:id,name,description,created_by','project.creator:id,name,role','assignedTo:id,name,role');
            
            if (auth()->user()->role === 'employee') {
                $query->where('assigned_to',auth()->id());
            }

            $task = $query->findOrFail($task_id);

            return response()->json([
                'status' => true,
                'data' => $task
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 404);
        }
    }
}
<?php

namespace App\Jobs;

use App\Models\Task;
use App\Models\User;
use App\Notifications\TaskAssignedNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
class SendTaskAssignedJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private Task $task;

    // Retry 3 times
    public int $tries = 3;

    // Timeout after 120 sec
    public int $timeout = 120;
    
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Task $task)
    {
        $this->task = $task;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $user = User::find($this->task->assigned_to);

        if (!$user) {
            return;
        }

        $user->notify(new TaskAssignedNotification($this->task));
    }

    public function failed(\Throwable $exception)
    {
        Log::error(
            'Task assignment job failed',
            [
                'task_id' => $this->task->id,
                'error' => $exception->getMessage(),
            ]
        );
    }
}

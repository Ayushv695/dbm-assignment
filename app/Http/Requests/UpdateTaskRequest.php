<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'project_id' => 'sometimes|exists:projects,id',

            'assigned_to' => 'sometimes|exists:users,id',

            'title' => 'sometimes|string|max:255',

            'description' => 'nullable|string',

            'status' => 'sometimes|in:pending,in_progress,completed',

            'priority' => 'sometimes|in:low,medium,high',

            'due_date' => 'nullable|date',

            'attachment' => 'nullable|file|mimes:jpg,png,pdf,docx|max:2048',
        ];
    }
}

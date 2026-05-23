<?php

namespace App\Http\Requests\TaskAssignment;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskAssignmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'agent_id' => ['required', 'exists:users,id'],
            'task' => ['required', 'string'],
            'block_data' => ['required', 'array'],
            'block_data.*' => ['required', 'exists:blocks,id'],
        ];
    }
}

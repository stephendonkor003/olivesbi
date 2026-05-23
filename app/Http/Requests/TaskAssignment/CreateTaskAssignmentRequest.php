<?php

namespace App\Http\Requests\TaskAssignment;

use Illuminate\Foundation\Http\FormRequest;

class CreateTaskAssignmentRequest extends FormRequest
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
            'supervisor_id' => ['required', 'exists:users,id'],
            'agent_id' => ['required', 'exists:users,id'],
            'assembly_code' => ['required', 'exists:assemblies,assembly_code'],
            'task' => ['required', 'array'],
            'task.*' => ['required', 'string'],
            'block_data' => ['required', 'array'],
            'block_data.*' => ['required', 'exists:blocks,id'],
        ];
    }
}

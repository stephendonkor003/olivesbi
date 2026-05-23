<?php

namespace App\Http\Requests\API\TaskAssignment;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskAssignment extends FormRequest
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
            'block_id' => ['required', 'exists:blocks,id'],
            'status' => ['required', 'string', 'in:Completed,Pending'],
        ];
    }
}

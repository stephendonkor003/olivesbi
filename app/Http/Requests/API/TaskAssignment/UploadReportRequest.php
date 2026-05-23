<?php

namespace App\Http\Requests\API\TaskAssignment;

use Illuminate\Foundation\Http\FormRequest;

class UploadReportRequest extends FormRequest
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
            'task_id' => ['required', 'exists:task_assignments,id'],
            'description' => ['required', 'string'],
            'file_path' => ['required', 'mimes:jpg,jpeg,png,gif,pdf,doc,docx,txt,xlsx,xls,ppt,pptx,mp3,mp4'],
        ];
    }
}

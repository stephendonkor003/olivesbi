<?php

namespace App\Http\Requests\API\Bill;

use App\Support\UserAccountAccess;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class IssueBillRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return UserAccountAccess::hasManagementPermission($this->user(), 'bills.update');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'bills_id' => ['required', 'string'],
            'code' => ['required', 'string', 'digits:6'],
        ];
    }
}

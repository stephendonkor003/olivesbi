<?php

namespace App\Http\Requests\RevenueStreamType;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRevenueStreamTypeRequest extends FormRequest
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
        $item = $this->route('revenueStreamType');

        return [
            'name' => [
                'required', 'string',
                Rule::unique('revenue_stream_types', 'name')->ignore($item->id),
            ],
        ];
    }
}

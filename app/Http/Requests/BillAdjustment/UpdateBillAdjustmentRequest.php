<?php

namespace App\Http\Requests\BillAdjustment;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBillAdjustmentRequest extends FormRequest
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
            'revenue_stream_type_id' => ['required', 'string', 'exists:revenue_stream_types,id'],
            'discount_year' => ['required', 'string'],
            'discount_amount' => ['required', 'numeric'],
            'discount_type' => ['required', 'string', 'in:Assembly Discount,Division Discount,Block Discount,Property Discount,BOP Discount'],
            'assembly_code' => ['required', 'string', 'exists:assemblies,assembly_code'],
            'division_id' => [
                'required_if:discount_type,Division Discount,Block Discount',
                'nullable',
                'exists:divisions,id',
            ],
            'block_id' => [
                'required_if:discount_type,Block Discount',
                'nullable',
                'exists:blocks,id',
            ],
            'erms_id' => [
                'required_if:discount_type,Property Discount,BOP Discount',
                'nullable',
                'array',
                'min:1',
            ],
            'erms_id.*' => ['nullable', 'string'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'discount_type.in' => 'The selected discount type is invalid.',
            'division_id.required_if' => 'Division is required for Division Discount and Block Discount types.',
            'block_id.required_if' => 'Block is required for Block Discount type.',
            'erms_id.required_if' => 'At least one Property/BOP must be selected for Property Discount and BOP Discount types.',
            'erms_id.min' => 'At least one Property/BOP must be selected.',
        ];
    }
}

<?php

namespace App\Http\Requests\API\Payment;

use Illuminate\Foundation\Http\FormRequest;

class CreatePaymentRequest extends FormRequest
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
            'bills_id' => ['required', 'string', 'exists:bills,bills_id'],
            'payment_mode' => ['required', 'string', 'in:momo,cash'],
            'amount' => ['required', 'numeric'],
            'phone' => ['required_if:payment_mode,momo', 'string', 'phone:GH'],
            'network' => ['required_if:payment_mode,momo', 'string', 'in:MTN,AIRTELTIGO,TELECEL'],
            'assembly_code' => ['required', 'string', 'exists:assemblies,assembly_code'],
            'receipt' => ['required_if:payment_mode,cash', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
        ];
    }
}

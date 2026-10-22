<?php

namespace App\Http\Requests\API\Payment;

use App\Support\PaymentAmounts;
use App\Support\UserAccountAccess;
use Illuminate\Foundation\Http\FormRequest;

class CreatePaymentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() && $this->user()->status === 'Active'
            && ($this->input('payment_mode') !== 'cash'
                || UserAccountAccess::hasManagementPermission($this->user(), 'payments.create'));
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
            'amount' => ['bail', 'required', 'numeric', PaymentAmounts::rule()],
            'phone' => ['required_if:payment_mode,momo', 'string', 'phone:GH'],
            'network' => ['required_if:payment_mode,momo', 'string', 'in:MTN,AIRTELTIGO,TELECEL'],
            'assembly_code' => ['sometimes', 'string', 'exists:assemblies,assembly_code'],
            'receipt' => ['required_if:payment_mode,cash', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
        ];
    }
}

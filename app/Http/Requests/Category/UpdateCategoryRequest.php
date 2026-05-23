<?php

namespace App\Http\Requests\Category;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends FormRequest
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
        $hasAssemblyCode = ! empty($this->user()->assembly_code);
        $assemblyCode = $this->user()->assembly_code ?? $this->get('assembly_code');

        $item = $this->route('category');

        return [
            'assembly_code' => [Rule::excludeIf($hasAssemblyCode), 'required', Rule::exists('assemblies', 'assembly_code')],
            'name' => [
                'required', 'string',
                Rule::unique('categories', 'name')
                    ->ignore($item->id)
                    ->when(! empty($assemblyCode), function ($query) use ($assemblyCode) {
                        return $query->where('assembly_code', $assemblyCode);
                    }),
            ],
        ];
    }
}

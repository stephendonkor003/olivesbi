<?php

namespace App\Http\Requests\Block;

use App\Models\Division;
use App\Rules\BlockGeofenceNotIntersecting;
use App\Services\GeofenceService;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class UpdateBlockRequest extends FormRequest
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
        $divisionId = $this->input('division_id');
        $blockId = $this->route('block');

        return [
            'code' => ['required', 'string'],
            'name' => ['required', 'string'],
            'assembly_code' => ['required', 'string', 'exists:assemblies,assembly_code'],
            'division_id' => ['required', 'string', 'exists:divisions,id'],
            'geofence' => [
                'required',
                'string',
                new BlockGeofenceNotIntersecting($divisionId, $blockId),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'geofence.required' => 'Please draw a geofence for the block on the map.',
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @return void
     */
    public function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {
            if ($this->has('division_id') && $this->has('geofence')) {
                $division = Division::find($this->division_id);

                if ($division && $division->geofence) {
                    $geofenceService = new GeofenceService;

                    if (! $geofenceService->isBlockContainedInDivision($this->geofence, $division->geofence)) {
                        $validator->errors()->add(
                            'geofence',
                            $geofenceService->getValidationErrorMessage()
                        );
                    }
                }
            }
        });
    }
}

<?php

namespace App\Http\Requests\Property;

use App\Services\PropertyValidationService;
use App\Support\GlobalLocation;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePropertyRequest extends FormRequest
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
            'hseno' => 'nullable|string',
            'digitaladdress' => 'nullable|string',
            'location' => 'required|string',
            'streetname' => 'nullable|string',
            ...GlobalLocation::rules(),
            'is_rated' => 'nullable|string',
            'valuation_number' => 'required_if:is_rated,1|nullable|string',
            'rateable_value' => 'required_if:is_rated,1|nullable|numeric|min:0',
            'owner_id' => 'nullable|string|exists:entity_owners,erms_id',
            'assembly_code' => 'required|string|exists:assemblies,assembly_code',
            'longitude' => 'required|numeric|between:-180,180',
            'latitude' => 'required|numeric|between:-90,90',
            'division_id' => 'required|string',
            'block_id' => 'required|string',
            'geofence' => 'required|string|json',
            'cntr_geom' => 'nullable|string',
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
            // Only validate administrative area assignment for rated properties
            if ($this->input('is_rated') == '1' &&
                $this->filled('assembly_code') &&
                $this->filled('division_id') &&
                $this->filled('block_id') &&
                $this->filled('latitude') &&
                $this->filled('longitude')) {

                $validationService = new PropertyValidationService;

                $result = $validationService->validateRatedPropertyAssignment(
                    $this->input('valuation_number'),
                    $this->input('assembly_code'),
                    $this->input('division_id'),
                    $this->input('block_id'),
                    (float) $this->input('latitude'),
                    (float) $this->input('longitude')
                );

                if (! $result['valid']) {
                    foreach ($result['errors'] as $error) {
                        $validator->errors()->add('valuation_number', $error);
                    }
                }
            }

            // Validate coordinates are within block and division for all properties
            if ($this->filled('latitude') &&
                $this->filled('longitude') &&
                $this->filled('division_id') &&
                $this->filled('block_id')) {

                $validationService = new PropertyValidationService;

                // Check if coordinates are in division
                if (! $validationService->validateCoordinatesInDivision(
                    (float) $this->input('latitude'),
                    (float) $this->input('longitude'),
                    $this->input('division_id')
                )) {
                    $validator->errors()->add(
                        'latitude',
                        'The property coordinates must be within the selected division boundary.'
                    );
                }

                // Check if coordinates are in block
                if (! $validationService->validateCoordinatesInBlock(
                    (float) $this->input('latitude'),
                    (float) $this->input('longitude'),
                    $this->input('block_id')
                )) {
                    $validator->errors()->add(
                        'longitude',
                        'The property coordinates must be within the selected block boundary.'
                    );
                }
            }
        });
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'valuation_number.required_if' => 'Valuation number is required for rated properties.',
            'rateable_value.required_if' => 'Rateable value is required for rated properties.',
            'rateable_value.min' => 'Rateable value must be greater than or equal to zero.',
            'longitude.between' => 'Longitude must be between -180 and 180 degrees.',
            'latitude.between' => 'Latitude must be between -90 and 90 degrees.',
            'geofence.required' => 'Property geofence is required. Please draw the property boundary on the map.',
            'geofence.json' => 'Invalid geofence format. Please redraw the property boundary.',
            'owner_id.exists' => 'The selected owner does not exist in the system.',
            'assembly_code.exists' => 'The selected assembly does not exist in the system.',
            'division_id.exists' => 'The selected division does not exist in the system.',
            'block_id.exists' => 'The selected block does not exist in the system.',
        ];
    }
}

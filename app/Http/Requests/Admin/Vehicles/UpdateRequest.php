<?php

namespace App\Http\Requests\Admin\Vehicles;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
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
        $vehicleId = $this->route('vehicle')->id;

        $rules = [
            // 'date' => 'required|date',
            'ilab_id' => 'sometimes|string',
            'vehicle_type_id' => 'sometimes|exists:vehicle_types,id',
            'vehicle_plate_number' => 'sometimes|string|unique:vehicles,vehicle_plate_number,' . $vehicleId,
            'make_model_id' => 'sometimes|exists:make_models,id',
            'rental_company_id' => 'sometimes|exists:rental_companies,id',
            'color' => 'sometimes|string',
            'status' => 'sometimes|in:0,1,3',
            'istimarah_expiry_date' => 'nullable|date',
            'istimarah' => 'nullable|file|mimes:png,jpg,jpeg,svg,pdf,doc,docx|max:2048',
            'tamm_report' => 'nullable|file|mimes:png,jpg,jpeg,svg,pdf,doc,docx|max:2048',
            'other_report' => 'nullable|file|mimes:png,jpg,jpeg,svg,pdf,doc,docx|max:2048'
        ];

        if ($this->status == 3) {
            $rules['replacement_date'] = 'required|date';
            $rules['replacement_reason'] = 'required|string';
        }

        return $rules;
    }
}

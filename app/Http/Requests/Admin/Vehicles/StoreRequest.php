<?php

namespace App\Http\Requests\Admin\Vehicles;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
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
        $rules = [
            'date' => 'required|date',
            'ilab_id' => 'required|string',
            'vehicle_type_id' => 'required|exists:vehicle_types,id',
            'vehicle_plate_number' => 'required|string|unique:vehicles,vehicle_plate_number',
            'make_model_id' => 'required|exists:make_models,id',
            'rental_company_id' => 'required|exists:rental_companies,id',
            'color' => 'required|string',
            'status' => 'required|in:0,1,3',
        ];
    
        if ($this->status == 3) {
            $rules['replacement_date'] = 'required|date';
            $rules['replacement_reason'] = 'required|string';
        }
    
        return $rules;
    }
}

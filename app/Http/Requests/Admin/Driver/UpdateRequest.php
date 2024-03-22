<?php

namespace App\Http\Requests\Admin\Driver;

use App\Models\Driver;
use App\Http\Requests\CoreRequest;
use App\Traits\CustomFieldsRequestTrait;

class UpdateRequest extends CoreRequest
{
    use CustomFieldsRequestTrait;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $setting = company();

        $rules = [
            'nationality_id' => 'nullable|exists:countries,id',
            'address' => 'nullable',
            'insurance_expiry_date' => 'nullable',
            'iqaama_expiry_date' => 'nullable',
            'license_expiry_date' => 'nullable',
            'stc_pay' => 'nullable',
            'bank_name' => 'nullable',
            'iban' => 'nullable',
            'contract_period_in_months' => 'nullable|numeric',
            'job_position' => 'nullable',
            'department' => 'nullable',
            'joining_date' => 'nullable|date_format:"' . $setting->date_format . '"',
            'basic_salary' => 'nullable|numeric',
            'housing_allowance' => 'nullable|numeric',
            'transportation_allowance' => 'nullable|numeric',
            'performance_allowance' => 'nullable|numeric',
            'other_allowance' => 'nullable|numeric',
            'total_salary' => 'nullable|numeric',
        ];

        $rules = $this->customFieldRules($rules);

        return $rules;
    }

    public function attributes()
    {
        $attributes = [];

        $attributes = $this->customFieldsAttributes($attributes);

        return $attributes;
    }
}

<?php

namespace App\Http\Requests\Admin\CoordinatorReport;

use App\Http\Requests\CoreRequest;
use App\Traits\CustomFieldsRequestTrait;
use Illuminate\Validation\Rule;

class StoreRequest extends CoreRequest
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
            'business_id' => 'required|exists:businesses,id',
            'driver_id' => 'required|exists:drivers,id',
            'fields.*.field_id' => 'required|exists:business_fields,id',
            'fields.*.value' => 'nullable',
            'report_date' => 'required|date_format:"' . $setting->date_format . '"',
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

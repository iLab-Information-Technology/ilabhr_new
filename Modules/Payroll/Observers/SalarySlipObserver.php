<?php

namespace Modules\Payroll\Observers;

use Modules\Payroll\Entities\SalarySlip;

class SalarySlipObserver
{
    public function creating(SalarySlip $model)
    {
        if (company()) {
            $model->company_id = company()->id;
        }
    }
}

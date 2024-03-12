<?php

namespace App\Models;

use App\Traits\CustomFieldsTrait;
use App\Traits\HasCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Driver extends BaseModel
{
    use CustomFieldsTrait, HasCompany;

    protected $table = 'drivers';

}

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

    protected $appends = [ 'image_url' ];

    protected $casts = [
        'insurance_expiry_date' => 'datetime',
        'license_expiry_date' => 'datetime',
        'iqaama_expiry_date' => 'datetime',
        'date_of_birth' => 'datetime'
    ];

    public function getImageUrlAttribute()
    {
        $gravatarHash = !is_null($this->email) ? md5(strtolower(trim($this->email))) : md5($this->id);

        return ($this->image) ? asset_url_local_s3('avatar/' . $this->image) : 'https://www.gravatar.com/avatar/' . $gravatarHash . '.png?s=200&d=mp';
    }
}

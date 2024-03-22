<?php

namespace App\Models;

use App\Traits\CustomFieldsTrait;
use App\Traits\HasCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Business extends BaseModel
{
    use CustomFieldsTrait, HasCompany;

    protected $table = 'businesses';

    protected $appends = [  ];

    protected $casts = [
    ];

    public function drivers(): BelongsToMany
    {
        return $this->belongsToMany(Driver::class);
    }
}

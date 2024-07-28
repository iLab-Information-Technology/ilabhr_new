<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    protected $guarded = ['id', '_token', '_method'];

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function vehicleType()
    {
        return $this->belongsTo(VehicleType::class);
    }

    public function rentalCompany()
    {
        return $this->belongsTo(RentalCompany::class);
    }

    public function makeModel()
    {
        return $this->belongsTo(MakeModel::class);
    }

    public function images(){
        return $this->hasMany(VehicleImage::class);
    }
}

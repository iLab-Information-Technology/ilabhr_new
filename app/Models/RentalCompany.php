<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RentalCompany extends Model
{
    use HasFactory;
    
    protected $guarded = ['id', '_token', '_method'];
}

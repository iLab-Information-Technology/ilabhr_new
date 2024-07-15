<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReceiptVoucher extends Model
{
    use HasFactory;


    protected $guarded = ['id', '_token', '_method'];

    protected $casts = [
        'voucher_date' => 'datetime',
        'start_date' => 'datetime',
        'end_date' => 'datetime'
    ];

    /**
     * Get the driver that owns the ReceiptVoucher
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }


    /**
     * Get the business that owns the ReceiptVoucher
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function business()
    {
        return $this->belongsTo(Business::class);
    }
}

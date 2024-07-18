<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
class ReceiptVoucher extends Model
{
    use HasFactory;


    protected $guarded = ['id', '_token', '_method'];

    protected $appends = ['signature'];

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


    public function getSignatureAttribute()
    {
        return asset_url_local_s3('ReceiptVoucherSigns/', $this->signature);
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

    /**
     * Get the business that owns the ReceiptVoucher
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
}

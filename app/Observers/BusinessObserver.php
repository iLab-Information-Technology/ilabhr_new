<?php

namespace App\Observers;

use App\Models\Business;

class BusinessObserver
{
    /**
     * Handle the Business "creating" event.
     */
    public function creating(Business $driverProject): void
    {
        if (company()) {
            $driverProject->company_id = company()->id;
        }
    }

    /**
     * Handle the Business "created" event.
     */
    public function created(Business $driverProject): void
    {
        //
    }

    /**
     * Handle the Business "updated" event.
     */
    public function updated(Business $driverProject): void
    {
        //
    }

    /**
     * Handle the Business "deleted" event.
     */
    public function deleted(Business $driverProject): void
    {
        //
    }

    /**
     * Handle the Business "restored" event.
     */
    public function restored(Business $driverProject): void
    {
        //
    }

    /**
     * Handle the Business "force deleted" event.
     */
    public function forceDeleted(Business $driverProject): void
    {
        //
    }
}

<?php

namespace App\Observers;

use App\Jobs\ProcessActionIdentification;
use App\Models\Identification;
use App\Repositories\Device\DeviceRepositoryInterface;
use App\Repositories\Identification\IdentificationRepositoryInterface;
use Illuminate\Support\Facades\Log;

class DatabaseIdentificationObserver
{
    public function __construct(
    ) {
    }
    // public function created(Identification $identification)
    // {
    //     if ($identification->status == 1 && $identification->type == 2) {
    //         ProcessActionIdentification::dispatch($identification->id);
    //     }
    // }

    // public function updated(Identification $identification)
    // {
    //     if ($identification->status == 2) {
    //         ProcessActionIdentification::dispatch($identification->id);
    //     }
    // }

}
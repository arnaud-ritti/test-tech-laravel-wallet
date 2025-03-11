<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Wallet;
use App\Notifications\LowBalance;

class WalletObserver
{
    /**
     * Handle the Wallet "updated" event.
     */
    public function updated(Wallet $wallet): void
    {
        if ($wallet->balance < 10_00 && $wallet->getOriginal('balance') >= 10_00) {
            $wallet->user->notify(new LowBalance);
        }
    }
}

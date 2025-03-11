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
        if (($wallet->getOriginal('balance', 0) - $wallet->balance) < 10) {
            $wallet->user->notify(new LowBalance);
        }
    }
}

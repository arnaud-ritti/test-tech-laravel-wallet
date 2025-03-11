<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Models\Wallet;
use Illuminate\Auth\Events\Registered;

class CreateUserWallet
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Registered $event): void
    {
        $user = $event->user;

        $wallet = new Wallet;

        $user->wallet()->save($wallet);
    }
}

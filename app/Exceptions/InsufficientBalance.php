<?php

declare(strict_types=1);

namespace App\Exceptions;

use App\Models\Wallet;
use App\Models\WalletTransfer;

class InsufficientBalance extends ApiException
{
    public function __construct(public readonly Wallet $wallet, public readonly int $amount)
    {
        parent::__construct(message: 'Insufficient balance in wallet.', code: 'INSUFFICIENT_BALANCE', status: 400);
    }
}

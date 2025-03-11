<?php

declare(strict_types=1);

namespace App\Enums;

enum WalletTransfertType: string
{
    case RECURRING = 'recurring';
    case SINGLE = 'single';
}

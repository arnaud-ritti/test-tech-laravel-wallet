<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Models\WalletTransfer;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DeleteRecurringTransfert
{
    public function __invoke(Request $request, WalletTransfer $transfer): Response
    {
        $transfer->delete();

        return response()->noContent(204);
    }
}

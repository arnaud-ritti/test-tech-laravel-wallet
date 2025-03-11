<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Enums\WalletTransfertType;
use App\Models\WalletTransfer;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class GetRecurringTransferts
{
    public function __invoke(Request $request): Response
    {
        $user = $request->user();
        $recurringTransferts = WalletTransfer::where([
            'source_id' => $user->wallet,
            'type' => WalletTransfertType::RECURRING,
        ]);

        return response()->json($recurringTransferts);
    }
}

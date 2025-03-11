<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Actions\PerformWalletTransfer;
use App\Enums\WalletTransfertType;
use App\Http\Requests\Api\V1\SendMoneyRequest;
use Illuminate\Http\Response;

class SendMoneyController
{
    public function __invoke(SendMoneyRequest $request, PerformWalletTransfer $performWalletTransfer): Response
    {
        $recipient = $request->getRecipient();

        $performWalletTransfer->execute(
            sender: $request->user(),
            recipient: $recipient,
            amount: $request->input('amount'),
            reason: $request->input('reason'),
            type: $request->getType(),
            startDate: $request->date('start_date'),
            endDate: $request->date('end_date'),
            frequency: $request->integer('frequency'),
        );

        return response()->noContent(201);
    }
}

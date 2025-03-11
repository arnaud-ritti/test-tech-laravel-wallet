<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\PerformWalletTransfer;
use App\Exceptions\InsufficientBalance;
use App\Http\Requests\SendMoneyRequest;

class SendMoneyController
{
    public function __invoke(SendMoneyRequest $request, PerformWalletTransfer $performWalletTransfer)
    {
        $recipient = $request->getRecipient();

        try {
            $performWalletTransfer->execute(
                sender: $request->user(),
                recipient: $recipient,
                amount: $request->getAmountInCents(),
                reason: $request->input('reason'),
                type: $request->getType(),
                startDate: $request->date('start_date'),
                endDate: $request->date('end_date'),
                frequency: $request->integer('frequency'),
            );

            return redirect()->back()
                ->with('money-sent-status', 'success')
                ->with('money-sent-recipient-name', $recipient->name)
                ->with('money-sent-type', $request->getType())
                ->with('money-sent-amount', $request->getAmountInCents());
        } catch (InsufficientBalance $exception) {
            return redirect()->back()->with('money-sent-status', 'insufficient-balance')
                ->with('money-sent-recipient-name', $recipient->name)
                ->with('money-sent-amount', $request->getAmountInCents());
        }
    }
}

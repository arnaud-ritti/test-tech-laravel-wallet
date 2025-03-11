<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Actions\PerformWalletTransaction;
use App\Enums\WalletTransactionType;
use App\Enums\WalletTransfertType;
use App\Exceptions\InsufficientBalance;
use App\Models\WalletTransfer;
use App\Notifications\UnprocessableTransation;
use Illuminate\Console\Command;

class DispatchRecurringTransaction extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:dispatch-recurring-transaction';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(PerformWalletTransaction $performWalletTransaction)
    {
        $today = now();
        $tranferts = WalletTransfer::where('type', WalletTransfertType::RECURRING)
            ->whereTodayOrBefore('start_date')
            ->whereTodayOrAfter('end_date')
            ->get();

        $tranferts->filter(function (WalletTransfer $walletTransfer) use ($today) {
            return $today->diffInDays($walletTransfer->start_date, true) % $walletTransfer->frequency == 0;
        })->each(function (WalletTransfer $walletTransfer) use ($performWalletTransaction) {
            try {
                $sender = $walletTransfer->source;
                $recipient = $walletTransfer->target;

                $performWalletTransaction->execute(
                    wallet: $sender,
                    type: WalletTransactionType::DEBIT,
                    amount: $walletTransfer->amount,
                    reason: $walletTransfer->reason,
                    transfer: $walletTransfer
                );

                $performWalletTransaction->execute(
                    wallet: $recipient,
                    type: WalletTransactionType::CREDIT,
                    amount: $walletTransfer->amount,
                    reason: $walletTransfer->reason,
                    transfer: $walletTransfer
                );
            } catch (InsufficientBalance $exception) {
                $exception->wallet->user->notify(new UnprocessableTransation($walletTransfer));
            }
        });
    }
}

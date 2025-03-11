<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\WalletTransactionType;
use App\Enums\WalletTransfertType;
use App\Exceptions\InsufficientBalance;
use App\Models\User;
use App\Models\WalletTransfer;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;

readonly class PerformWalletTransfer
{
    public function __construct(protected PerformWalletTransaction $performWalletTransaction) {}

    /**
     * @throws InsufficientBalance
     */
    public function execute(
        User $sender,
        User $recipient,
        int $amount,
        string $reason,
        WalletTransfertType $type,
        ?CarbonInterface $startDate = null,
        ?CarbonInterface $endDate = null,
        ?int $frequency = null
    ): WalletTransfer {
        return DB::transaction(function () use ($sender, $recipient, $amount, $reason, $type, $startDate, $endDate, $frequency) {
            $transfer = WalletTransfer::create([
                'amount' => $amount,
                'source_id' => $sender->wallet->id,
                'target_id' => $recipient->wallet->id,
                'type' => $type,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'frequency' => $frequency,
            ]);

            $this->performWalletTransaction->execute(
                wallet: $sender->wallet,
                type: WalletTransactionType::DEBIT,
                amount: $amount,
                reason: $reason,
                transfer: $transfer
            );

            $this->performWalletTransaction->execute(
                wallet: $recipient->wallet,
                type: WalletTransactionType::CREDIT,
                amount: $amount,
                reason: $reason,
                transfer: $transfer
            );

            return $transfer;
        });
    }
}

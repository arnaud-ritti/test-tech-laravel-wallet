<?php

use App\Actions\PerformWalletTransaction;
use App\Actions\PerformWalletTransfer;
use App\Console\Commands\DispatchRecurringTransaction;
use App\Enums\WalletTransactionType;
use App\Enums\WalletTransfertType;
use App\Exceptions\InsufficientBalance;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransfer;
use App\Notifications\LowBalance;
use App\Notifications\UnprocessableTransation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Pest\Laravel\mock;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\fake;

beforeEach(function () {
    $this->action = app(PerformWalletTransfer::class);
});

it('executes recurring transactions', function () {
    Notification::fake();

    $sender = User::factory()->create();
    $recipient = User::factory()->create();

    $source = Wallet::factory()->for($sender)->richChillGuy()->create();
    $target = Wallet::factory()->for($recipient)->create();

    $transfer = $this->action->execute($sender, $recipient, 100, 'test', WalletTransfertType::RECURRING, now(), now()->addDays(10), 1);

    assertDatabaseHas('wallet_transactions', [
        'amount' => 100,
        'wallet_id' => $target->id,
        'type' => WalletTransactionType::CREDIT,
        'transfer_id' => $transfer->id,
    ]);

    assertDatabaseHas('wallet_transactions', [
        'amount' => 100,
        'wallet_id' => $sender->id,
        'type' => WalletTransactionType::DEBIT,
        'transfer_id' => $transfer->id,
    ]);

    assertDatabaseHas('wallet_transfers', [
        'amount' => 100,
        'source_id' => $source->id,
        'target_id' => $target->id,
        'type' => WalletTransfertType::RECURRING,
    ]);

    $performWalletTransaction = app(PerformWalletTransaction::class);
    $command = new DispatchRecurringTransaction();
    $command->handle($performWalletTransaction);

    Notification::assertNothingSent();
});

it('notifies user on insufficient balance', function () {
    Notification::fake();

    $sender = User::factory()->create();
    $recipient = User::factory()->create();

    $source = Wallet::factory()->for($sender)->balance(1500)->create();
    $target = Wallet::factory()->for($recipient)->create();

    $this->action->execute($sender, $recipient, 1000, 'test', WalletTransfertType::RECURRING, now(), now()->addDays(10), 1);

    $performWalletTransaction = app(PerformWalletTransaction::class);
    $command = new DispatchRecurringTransaction();
    $command->handle($performWalletTransaction);

    Notification::assertSentTo($sender, UnprocessableTransation::class);
    Notification::assertSentTo($sender, LowBalance::class);
});

<?php

declare(strict_types=1);

use App\Actions\PerformWalletTransfer;
use App\Enums\WalletTransactionType;
use App\Exceptions\InsufficientBalance;
use App\Models\User;
use App\Models\Wallet;

use App\Notifications\LowBalance;
use Illuminate\Support\Facades\Notification;
use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseHas;

beforeEach(function () {
    $this->action = app(PerformWalletTransfer::class);
});

test('perform a transfer', function () {
    $sender = User::factory()->create();
    $recipient = User::factory()->create();

    $source = Wallet::factory()->for($sender)->richChillGuy()->create();
    $target = Wallet::factory()->for($recipient)->create();

    $transfer = $this->action->execute($sender, $recipient, 100, 'test');

    expect($source->refresh()->balance)->toBe(999_900);
    expect($target->refresh()->balance)->toBe(100);

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
    ]);
});

test('cannot perform a transfer with insufficient balance', function () {
    $sender = User::factory()->create();
    $recipient = User::factory()->create();

    $source = Wallet::factory()->balance(90)->for($sender)->create();
    $target = Wallet::factory()->for($recipient)->create();

    expect(function () use ($sender, $recipient) {
        $this->action->execute($sender, $recipient, 100, 'test');
    })->toThrow(InsufficientBalance::class);

    expect($source->refresh()->balance)->toBe(90);
    expect($target->refresh()->balance)->toBe(0);

    assertDatabaseCount('wallet_transfers', 0);
});


test('trigger a notification when balance is under 10', function () {
    Notification::fake();
    $sender = User::factory()->create();
    $recipient = User::factory()->create();

    $source = Wallet::factory()->balance(1000)->for($sender)->create();
    $target = Wallet::factory()->for($recipient)->create();

    $this->action->execute($sender, $recipient, 500, 'test');

    expect($source->refresh()->balance)->toBe(500);
    expect($target->refresh()->balance)->toBe(500);

    assertDatabaseCount('wallet_transfers', 1);

    Notification::assertSentTo(
        [$sender], LowBalance::class
    );
});

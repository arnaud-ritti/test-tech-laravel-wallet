<?php

declare(strict_types=1);

use App\Enums\WalletTransfertType;
use App\Http\Controllers\Api\V1\SendMoneyController;
use App\Models\User;
use App\Models\Wallet;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\postJson;

test('send money to a friend', function () {
    $user = User::factory()
        ->has(Wallet::factory()->richChillGuy())
        ->create();

    $recipient = User::factory()
        ->has(Wallet::factory())
        ->create();

    actingAs($user);

    postJson(action(SendMoneyController::class), [
        'recipient_email' => $recipient->email,
        'amount' => 100,
        'reason' => 'Just a chill guy gift',
    ])
        ->assertNoContent(201);

    expect($recipient->refresh()->wallet->balance)->toBe(100);

    assertDatabaseHas('wallet_transfers', [
        'amount' => 100,
        'source_id' => $user->wallet->id,
        'target_id' => $recipient->wallet->id,
    ]);

    assertDatabaseCount('wallet_transactions', 3);
});

test('send recurrently money to a friend', function () {
    $user = User::factory()
        ->has(Wallet::factory()->richChillGuy())
        ->create();

    $recipient = User::factory()
        ->has(Wallet::factory())
        ->create();

    actingAs($user);

    postJson(action(SendMoneyController::class), [
        'recipient_email' => $recipient->email,
        'amount' => 100,
        'reason' => 'Just a chill guy gift',
        'type' => WalletTransfertType::RECURRING,
        'start_date' => now(),
        'end_date' => now()->addMonth(),
        'frequency' => 1,
    ])->assertNoContent(201);

    expect($recipient->refresh()->wallet->balance)->toBe(100);

    assertDatabaseHas('wallet_transfers', [
        'amount' => 100,
        'source_id' => $user->wallet->id,
        'target_id' => $recipient->wallet->id,
        'type' => WalletTransfertType::RECURRING,
        'frequency' => 1,
    ]);

    assertDatabaseCount('wallet_transactions', 3);
});

test('cannot send money to a friend with insufficient balance', function () {
    $user = User::factory()
        ->has(Wallet::factory())
        ->create();

    $recipient = User::factory()
        ->has(Wallet::factory())
        ->create();

    actingAs($user);

    postJson(action(SendMoneyController::class), [
        'recipient_email' => $recipient->email,
        'amount' => 100,
        'reason' => 'Just a chill guy gift',
    ])
        ->assertBadRequest()
        ->assertJson([
            'code' => 'INSUFFICIENT_BALANCE',
            'message' => 'Insufficient balance in wallet.',
        ]);

    expect($recipient->refresh()->wallet->balance)->toBe(0);
});

<?php

declare(strict_types=1);

use App\Enums\WalletTransfertType;
use App\Http\Controllers\Api\V1\DeleteRecurringTransfert;
use App\Http\Controllers\Api\V1\GetRecurringTransferts;
use App\Models\User;
use App\Models\Wallet;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\delete;
use function Pest\Laravel\get;
use function Pest\Laravel\postJson;

test('get recurring transferts', function () {
    $user = User::factory()
        ->has(Wallet::factory()->richChillGuy())
        ->create();

    $recipient = User::factory()
        ->has(Wallet::factory())
        ->create();

    actingAs($user);

    $user->wallet->transfers()->create([
        'amount' => 100,
        'target_id' => $recipient->wallet->id,
        'type' => WalletTransfertType::RECURRING,
        'start_date' => now(),
        'end_date' => now()->addMonth(),
        'frequency' => 1,
    ]);

    assertDatabaseHas('wallet_transfers', [
        'amount' => 100,
        'source_id' => $user->wallet->id,
        'target_id' => $recipient->wallet->id,
        'type' => WalletTransfertType::RECURRING,
    ]);

    get(action(GetRecurringTransferts::class))
        ->assertOk()
        ->assertJsonCount(1);
});

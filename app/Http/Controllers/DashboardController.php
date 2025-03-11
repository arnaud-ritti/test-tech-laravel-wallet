<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\WalletTransfertType;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController
{
    public function __invoke(Request $request)
    {
        $transactions = optional($request->user(), fn (User $user) => $user->wallet?->transactions()->with('transfer')->orderByDesc('id')->get());
        $transfers = optional($request->user(), fn (User $user) => $user->wallet?->transfers()->where('type', WalletTransfertType::RECURRING)->orderByDesc('id')->get());
        $balance = $request->user()->wallet?->balance;

        return view('dashboard', compact('transactions', 'balance', 'transfers'));
    }
}

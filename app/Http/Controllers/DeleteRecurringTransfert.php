<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\WalletTransfer;
use Illuminate\Http\Request;

class DeleteRecurringTransfert
{
    public function __invoke(Request $request)
    {
        WalletTransfer::findOrFail($request->get('id'))->delete();

        return redirect()->back();
    }
}

<?php

declare(strict_types=1);

use App\Livewire\SendMoney;
use Livewire\Livewire;

it('renders successfully', function () {
    Livewire::test(SendMoney::class)
        ->assertStatus(200);
});

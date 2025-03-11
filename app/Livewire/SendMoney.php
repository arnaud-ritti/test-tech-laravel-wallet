<?php

declare(strict_types=1);

namespace App\Livewire;

use Livewire\Component;

class SendMoney extends Component
{
    public bool $recurring = false;

    public function mount()
    {
        $this->recurring = old('recurring', false);
    }

    public function render()
    {
        return view('livewire.send-money');
    }
}

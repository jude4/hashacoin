<?php

namespace App\Http\Livewire;

use Livewire\Component;

class PaymentLinks extends Component
{
    private $links;
    public function render()
    {
        return view('livewire.payment-links', ['links' => $this->links]);
    }
}

<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;

class DeansPerSchool extends Component
{
    public $deans = [];
    public $isLoading = true;
    public $hasError = false;

    public function mount()
    {
        // We'll use AJAX in the blade view
    }

    public function render()
    {
        return view('livewire.dashboard.deans-per-school');
    }
}

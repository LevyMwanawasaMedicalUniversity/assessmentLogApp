<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;

class CaPerSchool extends Component
{
    public $schoolsData = [];
    public $isLoading = true;
    public $hasError = false;

    public function mount()
    {
        // We'll use AJAX in the blade view
    }

    public function render()
    {
        return view('livewire.dashboard.ca-per-school');
    }
}

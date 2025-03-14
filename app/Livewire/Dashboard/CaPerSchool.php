<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;

class CaPerSchool extends Component
{
    public $schoolsData = [];
    public $isLoading = true;
    public $hasError = false;
    public $isRefreshing = false;
    public $lastUpdated = null;

    public function mount()
    {
        // State management will be handled by Alpine.js in the blade view
    }

    public function render()
    {
        return view('livewire.dashboard.ca-per-school');
    }
}
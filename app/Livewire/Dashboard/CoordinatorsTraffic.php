<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;

class CoordinatorsTraffic extends Component
{
    public $coordinatorsData = [];
    public $totalCoordinators = 0;
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
        return view('livewire.dashboard.coordinators-traffic');
    }
}
<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;

class CoordinatorsTraffic extends Component
{
    public $coordinatorsCount = 0;
    public $schoolNames = [];
    public $userCounts = [];
    public $isLoading = true;
    public $hasError = false;

    public function mount()
    {
        // We'll use AJAX in the blade view
    }

    public function render()
    {
        return view('livewire.dashboard.coordinators-traffic');
    }
}

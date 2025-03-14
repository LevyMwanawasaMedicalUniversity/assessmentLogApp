<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;

class CoordinatorsTable extends Component
{
    public $coordinatorsData = [];
    public $schoolId = null;
    public $schoolName = null;
    public $isLoading = true;
    public $hasError = false;
    public $isRefreshing = false;
    public $lastUpdated = null;

    public function mount($schoolId = null, $schoolName = null)
    {
        $this->schoolId = $schoolId;
        $this->schoolName = $schoolName;
    }

    public function render()
    {
        return view('livewire.dashboard.coordinators-table');
    }
}

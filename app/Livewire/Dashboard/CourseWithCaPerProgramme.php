<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;

class CourseWithCaPerProgramme extends Component
{
    public $programmeData = [];
    public $isLoading = true;
    public $hasError = false;
    public $isRefreshing = false;
    public $lastUpdated = null;

    public function mount()
    {
        // We'll use Alpine.js with localStorage in the blade view
    }

    public function render()
    {
        return view('livewire.dashboard.course-with-ca-per-programme');
    }
}

<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;

class CoursesFromEdurole extends Component
{
    public $totalCoursesCoordinated = 0;
    public $isLoading = true;
    public $hasError = false;

    public function mount()
    {
        // We'll use AJAX in the blade view
    }

    public function render()
    {
        return view('livewire.dashboard.courses-from-edurole');
    }
}

<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;

class CourseWithCaPerProgramme extends Component
{
    public $programmeData = [];
    public $isLoading = true;
    public $hasError = false;

    public function mount()
    {
        // We'll use AJAX in the blade view
    }

    public function render()
    {
        return view('livewire.dashboard.course-with-ca-per-programme');
    }
}

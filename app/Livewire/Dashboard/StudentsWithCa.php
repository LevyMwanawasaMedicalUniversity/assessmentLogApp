<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use Illuminate\Support\Facades\Http;

class StudentsWithCa extends Component
{
    public $studentCount = 0;
    public $isLoading = true;
    public $hasError = false;

    public function mount()
    {
        // We'll trigger the AJAX request from JavaScript in the view
    }

    public function render()
    {
        return view('livewire.dashboard.students-with-ca');
    }
}

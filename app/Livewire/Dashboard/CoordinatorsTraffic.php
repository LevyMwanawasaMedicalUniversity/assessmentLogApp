<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Http\Controllers\PagesController;
use App\Models\EduroleBasicInformation;

class CoordinatorsTraffic extends Component
{
    public $coordinatorsCount = 0;
    public $schoolNames = [];
    public $userCounts = [];
    public $readyToLoad = false;

    public function loadData()
    {
        $this->readyToLoad = true;
        $this->loadCoordinatorsTraffic();
    }

    public function mount()
    {
        // Defer loading until the page is rendered
    }

    public function loadCoordinatorsTraffic()
    {
        if (!$this->readyToLoad) {
            return;
        }

        try {
            $controller = new PagesController();
            $coursesFromEdurole = $controller->getCoursesFromEdurole()
                ->join('users', 'users.basic_information_id', '=', 'basic-information.ID')
                ->addSelect('users.email as username')
                ->get();
            
            // Get total number of unique coordinators
            $this->coordinatorsCount = $coursesFromEdurole->unique('username')->count();
            
            // Aggregate the number of unique usernames per SchoolName
            $userCountsPerSchool = $coursesFromEdurole->groupBy('SchoolName')->map(function ($group) {
                return $group->unique('username')->count();
            });

            // Convert to arrays for JavaScript
            $this->schoolNames = $userCountsPerSchool->keys()->toArray();
            $this->userCounts = $userCountsPerSchool->values()->toArray();
        } catch (\Exception $e) {
            $this->coordinatorsCount = 0;
            $this->schoolNames = [];
            $this->userCounts = [];
        }
    }

    public function render()
    {
        return view('livewire.dashboard.coordinators-traffic');
    }
}

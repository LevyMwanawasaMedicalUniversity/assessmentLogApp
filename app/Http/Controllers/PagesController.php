<?php

namespace App\Http\Controllers;

use App\Models\EduroleBasicInformation;
use App\Models\EduroleStudy;
use App\Models\StudentsContinousAssessment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PagesController extends Controller
{
    public function upload()
    {
        $user = auth()->user();
        try {
            $role = $user->roles->first()->name;
        } catch (\Exception $e) {
            return redirect()->route('dashboard');
        }
        $userBasicInformation = $user->basic_information_id;

        $results = $this->getCoursesFromEdurole()
            ->where('basic-information.ID', $userBasicInformation)
            ->orderBy('programmes.Year')
            ->orderBy('courses.Name')
            ->orderBy('study.Delivery')            
            ->get();
        return view('coordinator.viewCoordinatorsCourses', compact('results'));
    }

    public function dashboard()
    {
        $coursesFromLMMAX = $this->getCoursesFromLMMAX();
        // return $coursesFromLMMAX;
        $coursesFromEdurole = $this->getCoursesFromEdurole()->get();            
        // return $coursesWithCA;
        $filteredResults = $coursesFromEdurole->filter(function ($item) use ($coursesFromLMMAX) {
            foreach ($coursesFromLMMAX as $course) {
                if ($item->CourseName == $course['course_code'] && $item->Delivery == $course['delivery_mode'] && $item->ProgrammesAvailable != 1 && $item->StudyID == $course['study_id']) {
                    return true;
                }
            }
            return false;
        });

        $coursesWithCA = $filteredResults;
        $deansDataGet = EduroleBasicInformation::join('access', 'access.ID', '=', 'basic-information.ID')
            ->join('roles', 'roles.ID', '=', 'access.RoleID')
            ->join('schools', 'schools.Dean', '=', 'basic-information.ID')
            ->join('study', 'study.ParentID', '=', 'schools.ID')
            ->select('basic-information.FirstName', 'basic-information.Surname', 'basic-information.ID', 'roles.RoleName', 'schools.ID as ParentID', 'study.ID as StudyID', 'schools.Name as SchoolName','study.Delivery')
            ->get();
        $deansData= $deansDataGet->unique('ID');
        // $results= $deansDataGet->unique('ID');
        $counts = $deansDataGet->countBy('ID');;
        // return $coursesWithCA;
        return view('dashboard', compact('counts','coursesWithCA', 'coursesFromEdurole','deansData'));
    }

    

    public function viewCoordinatorsCourses(Request $request){

        $basicInformationId = $request->basicInformationId;
        $results = EduroleStudy::join('basic-information', 'basic-information.ID', '=', 'study.ProgrammesAvailable')
            ->join('study-program-link', 'study-program-link.StudyID', '=', 'study.ID')
            ->join('programmes', 'programmes.ID', '=', 'study-program-link.ProgramID')
            ->join('program-course-link', 'program-course-link.ProgramID', '=', 'programmes.ID')
            ->join('courses', 'courses.ID', '=', 'program-course-link.CourseID')
            ->select('courses.ID','basic-information.Firstname', 'basic-information.Surname', 'basic-information.PrivateEmail', 'study.ProgrammesAvailable', 'study.Name', 'courses.Name as CourseName','courses.CourseDescription')
            ->where('basic-information.ID', $basicInformationId)
            ->get();
        
        return view('coordinator.viewCoordinatorsCourses', compact('results'));

    }
}

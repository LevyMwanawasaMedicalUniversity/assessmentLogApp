<?php

namespace App\Http\Controllers;

use App\Models\CourseComponentAllocation;
use App\Models\EduroleBasicInformation;
use App\Models\EduroleCourseElective;
use App\Models\EduroleCourses;
use App\Models\EduroleStudy;
use App\Models\StudentsContinousAssessment;
use App\Models\CourseAssessment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
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
        try{
            $studyId = $results->first()->StudyID;
        }catch(\Exception $e){
            return redirect()->back()->with('error', 'No courses found');
        }
        
        return view('coordinator.viewCoordinatorsCourses', compact('results','studyId'));
    }

    public function uploadFinalExam(Request $request)
    {
        $user = auth()->user();
        try {
            $role = $user->roles->first()->name;
        } catch (\Exception $e) {
            return redirect()->route('dashboard');
        }
        if($request->basicInformationId){
            $userBasicInformation = decrypt($request->basicInformationId);
        }else{
            $userBasicInformation = $user->basic_information_id;
        }

        $results = $this->getCoursesFromEdurole()
            ->where('basic-information.ID', $userBasicInformation)
            ->orderBy('programmes.Year')
            ->orderBy('courses.Name')
            ->orderBy('study.Delivery')            
            ->get();
        try{
            $studyId = $results->first()->StudyID;
        }catch(\Exception $e){
            return redirect()->back()->with('error', 'No courses found');
        }
        
        return view('coordinator.viewCoordinatorsCoursesFinalExam', compact('results','studyId'));
    }    

    public function uploadFinalExamAndCa(Request $request)
    {
        $user = auth()->user();
        try {
            $role = $user->roles->first()->name;
        } catch (\Exception $e) {
            return redirect()->route('dashboard');
        }
        if($request->basicInformationId){
            $userBasicInformation = decrypt($request->basicInformationId);
        }else{
            $userBasicInformation = $user->basic_information_id;
        }

        $results = $this->getCoursesFromEdurole()
            ->where('basic-information.ID', $userBasicInformation)
            ->orderBy('programmes.Year')
            ->orderBy('courses.Name')
            ->orderBy('study.Delivery')            
            ->get();
        try{
            $studyId = $results->first()->StudyID;
        }catch(\Exception $e){
            return redirect()->back()->with('error', 'No courses found');
        }
        
        return view('coordinator.viewCoordinatorsCoursesFinalExamAndCa', compact('results','studyId'));
    }

    public function uploadCourseWithComponents($courseId,$basicInformationId,$delivery,$studyId)
    {
        $user = auth()->user();
        try {
            $role = $user->roles->first()->name;
        } catch (\Exception $e) {
            return redirect()->route('dashboard');
        }
        $userBasicInformation = $user->basic_information_id;
        $courseId = Crypt::decrypt($courseId);
        $basicInformationId = Crypt::decrypt($basicInformationId);
        $delivery = Crypt::decrypt($delivery);
        $studyId = Crypt::decrypt($studyId);
        $academicYear = 2024;

        $results = $this->getAllocatedCourses($courseId,$basicInformationId,$delivery,$studyId, $academicYear);
        $getCoure = EduroleCourses::where('ID', $courseId)->first();
        $courseCode = $getCoure->Name;
        $getStudy = EduroleStudy::where('ID', $studyId)->first();
        $studyName = $getStudy->Name;
        $deliveryMode = $getStudy->Delivery;

        return view('coordinator.caComponents.viewCoordinatorsCourses', compact('results','courseCode','studyName','deliveryMode','basicInformationId'));
    }

    public function showChangeForm()
    {
        return view('auth.passwords.change');
    }

    public function dashboard()
    {
        // The dashboard now uses Livewire components for all statistics
        // Each component loads its own data, reducing the initial load time
        return view('dashboard');
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

    /**
     * Get courses from LMMAX
     * 
     * @return array
     */
    public function getCoursesFromLMMAX()
    {
        return CourseAssessment::select('course_id', 'delivery_mode', 'study_id')
            ->selectRaw('course_id as course_code')
            ->distinct()
            ->get()
            ->toArray();
    }

    /**
     * Get courses from Edurole
     * 
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getCoursesFromEdurole()
    {
        return EduroleStudy::join('basic-information', 'basic-information.ID', '=', 'study.ProgrammesAvailable')
            ->join('study-program-link', 'study-program-link.StudyID', '=', 'study.ID')
            ->join('programmes', 'programmes.ID', '=', 'study-program-link.ProgramID')
            ->join('program-course-link', 'program-course-link.ProgramID', '=', 'programmes.ID')
            ->join('courses', 'courses.ID', '=', 'program-course-link.CourseID')
            ->join('schools', 'schools.ID', '=', 'study.ParentID')
            ->select(
                'courses.ID',
                'basic-information.Firstname', 
                'basic-information.Surname', 
                'basic-information.PrivateEmail', 
                'study.ProgrammesAvailable', 
                'study.Name', 
                'courses.Name as CourseName',
                'courses.CourseDescription',
                'study.Delivery',
                'study.ID as StudyID',
                'study.ShortName as ProgrammeCode',
                'schools.Name as SchoolName',
                'schools.Description as SchoolDescription'
            );
    }

    /**
     * Get allocated courses
     * 
     * @param int $courseId
     * @param int $basicInformationId
     * @param string $delivery
     * @param int $studyId
     * @param int $academicYear
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllocatedCourses($courseId, $basicInformationId, $delivery, $studyId, $academicYear)
    {
        return CourseComponentAllocation::where('course_id', $courseId)
            ->where('basic_information_id', $basicInformationId)
            ->where('delivery_mode', $delivery)
            ->where('study_id', $studyId)
            ->where('academic_year', $academicYear)
            ->get();
    }
}

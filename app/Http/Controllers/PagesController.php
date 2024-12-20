<?php

namespace App\Http\Controllers;

use App\Models\CourseComponentAllocation;
use App\Models\EduroleBasicInformation;
use App\Models\EduroleCourseElective;
use App\Models\EduroleCourses;
use App\Models\EduroleStudy;
use App\Models\StudentsContinousAssessment;
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
        $coursesFromLMMAX = $this->getCoursesFromLMMAX();
        // return $coursesFromLMMAX;
        $coursesFromEdurole = $this->getCoursesFromEdurole();

        $resultsForCount = $coursesFromEdurole
                // ->where('basic-information.ID', $basicInformationId)
                // ->whereIn('courses.ID', $coursesFromCourseElectives)
                ->orderBy('programmes.Year')
                ->orderBy('courses.Name')
                ->orderBy('study.Delivery')
                ->get();
        
        $coursesFromEdurole =  $coursesFromEdurole->get();
        
        
        // return $coursesFromEdurole;
        $filteredResults = $coursesFromEdurole->filter(function ($item) use ($coursesFromLMMAX) {
            foreach ($coursesFromLMMAX as $course) {
                if ($item->CourseName == $course['course_code'] && $item->Delivery == $course['delivery_mode'] && $item->ProgrammesAvailable != 1 && $item->StudyID == $course['study_id']) {
                    return true;
                }
            }
            return false;
        });
        

        $coursesWithCA = $filteredResults;
        // return $coursesWithCA->count();
        $deansDataGet = EduroleBasicInformation::join('access', 'access.ID', '=', 'basic-information.ID')
            ->join('roles', 'roles.ID', '=', 'access.RoleID')
            ->join('schools', 'schools.Dean', '=', 'basic-information.ID')
            ->join('study', 'study.ParentID', '=', 'schools.ID')
            ->select('basic-information.FirstName', 'basic-information.Surname', 'basic-information.ID', 'roles.RoleName', 'schools.ID as ParentID', 'study.ID as StudyID', 'schools.Name as SchoolName','study.Delivery')
            ->get();
        $deansData= $deansDataGet->unique('ID');
        // $results= $deansDataGet->unique('ID');
        // $counts = $deansDataGet->countBy('ID');;
        // return $coursesWithCA;
        $coursesFromCourseElectivesQuery = EduroleCourseElective::select('course-electives.CourseID')
                ->join('courses', 'courses.ID', '=', 'course-electives.CourseID')
                ->join('program-course-link', 'program-course-link.CourseID', '=', 'courses.ID')
                ->join('student-study-link', 'student-study-link.StudentID', '=', 'course-electives.StudentID')
                ->join('study', 'study.ID', '=', 'student-study-link.StudyID')
                ->where('course-electives.Year', 2024)
                ->where('course-electives.Approved', 1);

        
        return view('dashboard', compact('coursesFromCourseElectivesQuery','resultsForCount','coursesWithCA', 'coursesFromEdurole','deansData'));
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

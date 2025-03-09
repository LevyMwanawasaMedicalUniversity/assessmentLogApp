<?php

namespace App\Http\Controllers;

use App\Models\CourseAssessment;
use App\Models\SisReportsStudent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\CourseComponentAllocation;
use App\Models\EduroleBasicInformation;
use App\Models\EduroleCourseElective;
use App\Models\EduroleCourses;
use App\Models\EduroleStudy;
use App\Models\StudentsContinousAssessment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Inertia\Inertia;
use Illuminate\Support\Collection;

class PagesController extends Controller
{
    public function upload()
    {
        $user = Auth::user();
        try {
            $role = $user->roles->first()->name;
        } catch (\Exception $e) {
            return redirect()->route('dashboard');
        }
        $userBasicInformation = $user->basic_information_id;

        $query = EduroleStudy::join('basic-information', 'basic-information.ID', '=', 'study.ProgrammesAvailable')
            ->join('study-program-link', 'study-program-link.StudyID', '=', 'study.ID')
            ->join('programmes', 'programmes.ID', '=', 'study-program-link.ProgramID')
            ->join('program-course-link', 'program-course-link.ProgramID', '=', 'programmes.ID')
            ->join('courses', 'courses.ID', '=', 'program-course-link.CourseID');
            
        $results = $query->where('basic-information.ID', $userBasicInformation)
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
        $user = Auth::user();
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

        $query = EduroleStudy::join('basic-information', 'basic-information.ID', '=', 'study.ProgrammesAvailable')
            ->join('study-program-link', 'study-program-link.StudyID', '=', 'study.ID')
            ->join('programmes', 'programmes.ID', '=', 'study-program-link.ProgramID')
            ->join('program-course-link', 'program-course-link.ProgramID', '=', 'programmes.ID')
            ->join('courses', 'courses.ID', '=', 'program-course-link.CourseID');
            
        $results = $query->where('basic-information.ID', $userBasicInformation)
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
        $user = Auth::user();
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

        $query = EduroleStudy::join('basic-information', 'basic-information.ID', '=', 'study.ProgrammesAvailable')
            ->join('study-program-link', 'study-program-link.StudyID', '=', 'study.ID')
            ->join('programmes', 'programmes.ID', '=', 'study-program-link.ProgramID')
            ->join('program-course-link', 'program-course-link.ProgramID', '=', 'programmes.ID')
            ->join('courses', 'courses.ID', '=', 'program-course-link.CourseID');
            
        $results = $query->where('basic-information.ID', $userBasicInformation)
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
        $user = Auth::user();
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

        $results = parent::getAllocatedCourses($courseId,$basicInformationId,$delivery,$studyId, $academicYear);
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
        try {
            // Get basic statistics for the dashboard
            $studentsWithCA = SisReportsStudent::distinct()
                ->where('status', 6)
                ->count();
                
            // Get total courses coordinated
            $totalCoursesCoordinated = 0;
            try {
                $totalCoursesCoordinated = DB::table('courses')
                    ->join('program-course-link', 'program-course-link.CourseID', '=', 'courses.ID')
                    ->join('programmes', 'programmes.ID', '=', 'program-course-link.ProgramID')
                    ->join('study-program-link', 'study-program-link.ProgramID', '=', 'programmes.ID')
                    ->join('study', 'study.ID', '=', 'study-program-link.StudyID')
                    ->join('basic-information', 'basic-information.ID', '=', 'study.ProgrammesAvailable')
                    ->distinct()
                    ->count();
            } catch (\Exception $e) {
                Log::error('Error getting total courses coordinated: ' . $e->getMessage());
            }
            
            // Get total courses with CA
            $totalCoursesWithCA = 0;
            try {
                $totalCoursesWithCA = CourseAssessment::distinct(['course_id', 'delivery_mode', 'study_id'])
                    ->count();
            } catch (\Exception $e) {
                Log::error('Error getting total courses with CA: ' . $e->getMessage());
            }
            
            // Prepare data for charts
            $programmeCodes = ['BBS', 'NS', 'TP1', 'TP2', 'TP3', 'TP4', 'TP5', 'TP6', 'TP7'];
            $coursesWithCAProgrammeCountsArray = array_fill(0, count($programmeCodes), 0);
            $coursesFromEduroleProgrammeCountsArray = array_fill(0, count($programmeCodes), 0);
            
            // Return the view with the data
            return view('dashboard', [
                'studentsWithCA' => $studentsWithCA,
                'totalCoursesCoordinated' => $totalCoursesCoordinated,
                'totalCoursesWithCA' => $totalCoursesWithCA,
                'programmeCodes' => $programmeCodes,
                'coursesWithCAProgrammeCountsArray' => $coursesWithCAProgrammeCountsArray,
                'coursesFromEduroleProgrammeCountsArray' => $coursesFromEduroleProgrammeCountsArray
            ]);
            
        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('Dashboard error: ' . $e->getMessage());
            
            // Return a simple dashboard view with error message
            return view('dashboard', [
                'error' => $e->getMessage(),
                'studentsWithCA' => 0,
                'totalCoursesCoordinated' => 0,
                'totalCoursesWithCA' => 0,
                'programmeCodes' => [],
                'coursesWithCAProgrammeCountsArray' => [],
                'coursesFromEduroleProgrammeCountsArray' => []
            ]);
        }
    }

    public function testDashboard()
    {
        try {
            // Get basic statistics for the dashboard
            $studentsWithCA = SisReportsStudent::distinct()
                ->where('status', 6)
                ->count();
                
            // Get total courses coordinated
            $totalCoursesCoordinated = 0;
            try {
                $totalCoursesCoordinated = DB::table('courses')
                    ->join('program-course-link', 'program-course-link.CourseID', '=', 'courses.ID')
                    ->join('programmes', 'programmes.ID', '=', 'program-course-link.ProgramID')
                    ->join('study-program-link', 'study-program-link.ProgramID', '=', 'programmes.ID')
                    ->join('study', 'study.ID', '=', 'study-program-link.StudyID')
                    ->join('basic-information', 'basic-information.ID', '=', 'study.ProgrammesAvailable')
                    ->distinct()
                    ->count();
            } catch (\Exception $e) {
                Log::error('Error getting total courses coordinated: ' . $e->getMessage());
            }
            
            // Get total courses with CA
            $totalCoursesWithCA = 0;
            try {
                $totalCoursesWithCA = CourseAssessment::distinct(['course_id', 'delivery_mode', 'study_id'])
                    ->count();
            } catch (\Exception $e) {
                Log::error('Error getting total courses with CA: ' . $e->getMessage());
            }
            
            // Return the simplified test dashboard view with the data
            return view('test-dashboard', [
                'studentsWithCA' => $studentsWithCA,
                'totalCoursesCoordinated' => $totalCoursesCoordinated,
                'totalCoursesWithCA' => $totalCoursesWithCA
            ]);
            
        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('Dashboard error: ' . $e->getMessage());
            
            // Return a simple dashboard view with error message
            return view('test-dashboard', [
                'error' => $e->getMessage(),
                'studentsWithCA' => 0,
                'totalCoursesCoordinated' => 0,
                'totalCoursesWithCA' => 0
            ]);
        }
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

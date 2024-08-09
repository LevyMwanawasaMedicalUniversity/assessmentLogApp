<?php

namespace App\Http\Controllers;

use App\Models\CATypeMarksAllocation;
use App\Models\CourseAssessment;
use App\Models\CourseAssessmentScores;
use App\Models\EduroleBasicInformation;
use App\Models\EduroleStudy;
use App\Models\StudentsContinousAssessment;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;
use OwenIt\Auditing\Models\Audit;

class AdministratorController extends Controller
{
    //
    public function index()
    {
        return view('admin.index');
    }

    public function refreshCAs(Request $request)
    {
        set_time_limit(12000000);
        $cooedinatorController = new CoordinatorController();
        $courseAssessments = CourseAssessment::all();   

        $academicYear = 2024;
        // foreach ($courseAssessments as $courseAssessment) {
        //     $coursesInEdurole = $this->getCoursesFromEdurole()
        //         ->where('courses.ID', $courseAssessment->course_id)
        //         ->where('study.ProgrammesAvailable', $courseAssessment->basic_information_id)
        //         ->where('study.Delivery', $courseAssessment->delivery_mode)
        //         ->first();            
            
        //     if ($coursesInEdurole) {
        //         $user = User::where('basic_information_id', $courseAssessment->basic_information_id)->first();
        //         if ($user) {
        //             $courseAssessment->study_id = $coursesInEdurole->StudyID;
        //             $courseAssessment->save();            

        //             $courseAssessmentsScore = CourseAssessmentScores::where('course_assessment_id', $courseAssessment->course_assessments_id)->get();
        //             foreach ($courseAssessmentsScore as $courseAssessmentScore) {
        //                 $courseAssessmentScore->study_id = $coursesInEdurole->StudyID;
        //                 $courseAssessmentScore->save();
        //             }
                    
        //         }
                
        //     }
        // }

        // foreach ($courseAssessments as $courseAssessment) {
        //     $studentAssessments = StudentsContinousAssessment::where('course_assessment_id', $courseAssessment->course_assessments_id)->get();
        //     foreach ($studentAssessments as $studentAssessment) {
        //         $studentAssessment->study_id = $courseAssessment->study_id;
        //         $studentAssessment->save();
        //     }
        // } 

        $caTypeAllocation = CATypeMarksAllocation::all();

        // foreach ($caTypeAllocation as $caType) {

        //     $user = User::where('id', $caType->user_id)->first();
        //     $basicInformationId = $user->basic_information_id;
        //     $coursesInEdurole = $this->queryCourseFromEdurole()
        //         ->where('study.ProgrammesAvailable', $basicInformationId)
        //         ->where('study.Delivery', $caType->delivery_mode)
        //         ->where('courses.ID', $caType->course_id)
        //         ->first();
        //     try{
        //         $caType->study_id = $coursesInEdurole->StudyID;
        //     }catch (Exception $e) {
        //         Log::error('Error refreshing student marks: ' . $e->getMessage());
        //         continue;
        //     }
        //     $caType->save();
        // }

        foreach ($courseAssessments as $courseAssessment) {
            $coursesInEdurole = $this->getCoursesFromEdurole()
                ->where('courses.ID', $courseAssessment->course_id)
                ->where('study.ProgrammesAvailable', $courseAssessment->basic_information_id)
                ->where('study.Delivery', $courseAssessment->delivery_mode)
                ->first();
            $studentAssessments = StudentsContinousAssessment::where('course_assessment_id', $courseAssessment->course_assessments_id)->get();
            $studentInCourseAssessment = $studentAssessments->unique('student_id');
            try{
                foreach ($studentInCourseAssessment as $student) {
                    $cooedinatorController->refreshAllStudentsMarks(
                        $courseAssessment->course_id, 
                        $academicYear, 
                        $courseAssessment->ca_type, 
                        $student->student_id, 
                        $courseAssessment->course_assessments_id, 
                        $courseAssessment->delivery_mode, 
                        $coursesInEdurole->StudyID
                    );
                }
            }catch (Exception $e) {
                Log::error('Error refreshing student marks: ' . $e->getMessage());
                continue;
            }
        }

        return redirect()->back()->with('success', 'Course Assessments refreshed successfully');
    }


    public function importCoordinators(){
        set_time_limit(1200000);
    
        $results = EduroleStudy::join('basic-information', 'basic-information.ID', '=', 'study.ProgrammesAvailable')
            ->select('basic-information.ID','basic-information.Firstname', 'basic-information.Surname', 'basic-information.PrivateEmail', 'study.ProgrammesAvailable', 'study.Name','study.ParentID')
            ->get();
    
        foreach ($results as $result) {
            $email = trim($result->PrivateEmail);
            if (User::where('email', $email)->where('basic_information_id', '!=',$result->ID )->exists()) {
                $email = $result->ID . '@lmmu.ac.zm';
            }
    
            try {
                // Check if user already exists
                $existingUser = User::where('basic_information_id', $result->ID)->first();
                $password = $existingUser ? null : $this->generateRandomPassword();
    
                $user = User::updateOrCreate(
                    [
                        'basic_information_id' => $result->ID
                    ],
                    [
                        'name' => $result->Firstname . ' ' . $result->Surname,
                        'password' => $password ? bcrypt($password) : $existingUser->password,
                        'school_id' => $result->ParentID,
                        'email' => $email
                    ]
                );
    
                $coordinatorRole = Role::firstOrCreate(['name' => 'Coordinator']);
                $coordinatorPermission = Permission::firstOrCreate(['name' => 'Coordinator']);
                $user->assignRole($coordinatorRole);
                $user->givePermissionTo($coordinatorPermission);
    
                if ($password) {
                    $this->sendCredentialsEmail($user, $password);
                }
    
            } catch (\Exception $e) {
                Log::error('Error creating user: ' . $e->getMessage());
                continue;
            }
        }        
    
        return redirect()->back()->with('success', 'Coordinators imported successfully');
    }    

    public function importDeans(){
        set_time_limit(1200000);
    
        $results = EduroleBasicInformation::join('access', 'access.ID', '=', 'basic-information.ID')
            ->join('roles', 'roles.ID', '=', 'access.RoleID')
            ->join('schools', 'schools.Dean', '=', 'basic-information.ID')
            ->select('basic-information.FirstName', 'basic-information.Surname','basic-information.PrivateEmail', 'basic-information.ID', 'roles.RoleName', 'schools.ID as ParentID')
            ->get();
    
        foreach ($results as $result) {
            $email = trim($result->PrivateEmail);
            if (User::where('email', $email)->where('basic_information_id', '!=', $result->ID)->exists()) {
                $email = $result->ID . '@lmmu.ac.zm';
            }
    
            try {
                // Check if user already exists
                $existingUser = User::where('basic_information_id', $result->ID)->first();
                $password = $existingUser ? null : $this->generateRandomPassword();
    
                $user = User::updateOrCreate(
                    [
                        'basic_information_id' => $result->ID
                    ],
                    [
                        'name' => $result->FirstName . ' ' . $result->Surname,
                        'password' => $password ? bcrypt($password) : $existingUser->password,
                        'school_id' => $result->ParentID,
                        'email' => $email
                    ]
                );
    
                $deanRole = Role::firstOrCreate(['name' => 'Dean']);
                $deanPermission = Permission::firstOrCreate(['name' => 'Dean']);
                $user->assignRole($deanRole);
                $user->givePermissionTo($deanPermission);
    
                if ($password) {
                    $this->sendCredentialsEmail($user, $password);
                }
    
            } catch (\Exception $e) {
                Log::error('Error creating user: ' . $e->getMessage());
                continue;
            }
        }        
    
        return redirect()->back()->with('success', 'Deans imported successfully');
    }    
    
    private function generateRandomPassword($length = 10) {
        return Str::random($length);
    }
    
    private function sendCredentialsEmail($user, $password) {
        $details = [
            'title' => 'Account Created',
            'body' => 'Your account has been created. Your login details are:',
            'email' => $user->email,
            // 'email' => 'azwelsimwinga@gmail.com',
            'password' => $password
        ];
    
        Mail::to($user->email)->send(new \App\Mail\UserCredentialsMail($details));
    }
    
    public function auditTrails(){

        $audits = Audit::with('user')
        ->orderBy('created_at', 'desc')
        ->paginate(15); // Eager load the related user

        return view('admin.users.audits', compact('audits'));
    }

    public function editCourseAssessmentDescription($courseAssessmentId,$statusId){

        $courseAssessmentId = Crypt::decrypt($courseAssessmentId);
        $statusId = Crypt::decrypt($statusId);
        $result = CourseAssessment::where('course_assessments_id',$courseAssessmentId)
                ->first();
        // return $result;
        return view('coordinator.editCourseAssessmentDescription',compact('result'));
    }

    public function updateCourseAssessmentDescription(Request $request, $courseAssessmentId){

        $description = $request->description;
        
        if($description){
            $courseAssessment = CourseAssessment::find($courseAssessmentId);
            $courseAssessment->description = $description;
            $courseAssessment->save();
        }       

        return back()->with('success', 'Updated successfully click "View CA"');
    }

    public function viewCoordinators(){
        // $naturalScienceCourses = $this->getNSAttachedCourses();
        $results = $this->getCoursesFromEdurole()
            // ->groupBy('basic-information.ID')
            ->orderBy('study.Name')
            ->get();

        // return $results;
        

        // return $results;
        $coursesWithCA = $this->getCoursesFromLMMAX();
        // return $getoursesWithCA;
        // $coursesWithCA = array_column($getoursesWithCA, 'course_code');
        // $deliveryModes = array_column($getoursesWithCA, 'delivery_mode');
        // return $results;
        // return $coursesWithCA;
        $counts = $results->unique('ID')->count();

        $filteredResults = $results->filter(function ($item) use ($coursesWithCA) {
            foreach ($coursesWithCA as $course) {
                if ($item->CourseName == $course['course_code'] && $item->Delivery == $course['delivery_mode'] && $item->ProgrammesAvailable != 1 && $item->ProgrammesAvailable == $course['basic_information_id']) {
                    return true;
                }
            }
            return false;
        });
        
        $withCa = $filteredResults->countBy('basicInformationId');
        
        // return $results;
        
        $totalCoursesCoordinated = (ceil($counts/ 3));
        // $totalCoursesCoordinated = $counts;
        $counts = $results->countBy('basicInformationId');
        $totalCoursesWithCA = $withCa->sum();
        $results= $results->unique('basicInformationId', 'Name');
        return view('dean.viewCoordinators', compact('results', 'counts','withCa','totalCoursesCoordinated','totalCoursesWithCA'));
    }

    public function viewCoordinatorsUnderDean($schoolId){
        $schoolId = Crypt::decrypt($schoolId);
        // $naturalScienceCourses = $this->getNSAttachedCourses();
        $results = $this->getCoursesFromEdurole()
            ->where('study.ParentID', $schoolId)
            ->get();
        $coursesWithCA = $this->getCoursesFromLMMAX();
        $filteredResults = $results->filter(function ($item) use ($coursesWithCA) {
            foreach ($coursesWithCA as $course) {
                if ($item->CourseName == $course['course_code'] && $item->Delivery == $course['delivery_mode'] && $item->ProgrammesAvailable != 1 && $item->ProgrammesAvailable == $course['basic_information_id']) {
                    return true;
                }
            }
            return false;
        });
            
        $counts = $results->unique('ID')->count();
        $withCa = $filteredResults->countBy('basicInformationId');
        
        
        $totalCoursesCoordinated = (ceil($counts/ 3));
        // $totalCoursesCoordinated = ($counts);
        $counts = $results->countBy('basicInformationId');
        $results= $results->unique('username');
        $totalCoursesWithCA = $withCa->sum();
        return view('dean.viewCoordinators', compact('schoolId','results', 'counts','withCa','totalCoursesCoordinated','totalCoursesWithCA'));
    }

    public function viewDeans(){
        $results = EduroleBasicInformation::join('access', 'access.ID', '=', 'basic-information.ID')
            ->join('roles', 'roles.ID', '=', 'access.RoleID')
            ->join('schools', 'schools.Dean', '=', 'basic-information.ID')
            ->join('study', 'study.ParentID', '=', 'schools.ID')
            ->select('basic-information.FirstName', 'basic-information.Surname', 'basic-information.ID as basicInformationId', 'roles.RoleName', 'schools.ID as ParentID', 'study.ID as StudyID', 'schools.Name as SchoolName')
            // ->groupBy('basic-information.ID')
            ->get();
        $counts = $results->countBy('basicInformationId');
        $results= $results->unique('basicInformationId');
        
        // return $results;
        return view('registrar.viewDeans', compact('results', 'counts'));
    }

    public function viewCoordinatorsCourses($basicInformationId){

        $basicInformationId = Crypt::decrypt($basicInformationId);
        $naturalScienceCourses = $this->getNSAttachedCourses();
        $results = $this->getCoursesFromEdurole()
            ->where('basic-information.ID', $basicInformationId)
            ->orderBy('programmes.Year')
            ->orderBy('courses.Name')
            ->orderBy('study.Delivery')
            
            ->get();

            
        // return $results;
        
        return view('coordinator.viewCoordinatorsCourses', compact('results'));

    }

    public function viewCourse(Request $request){

        $courseId = $request->courseId;
        $results = EduroleStudy::join('basic-information', 'basic-information.ID', '=', 'study.ProgrammesAvailable')
            ->join('study-program-link', 'study-program-link.StudyID', '=', 'study.ID')
            ->join('programmes', 'programmes.ID', '=', 'study-program-link.ProgramID')
            ->join('program-course-link', 'program-course-link.ProgramID', '=', 'programmes.ID')
            ->join('courses', 'courses.ID', '=', 'program-course-link.CourseID')
            ->select('courses.ID','basic-information.Firstname', 'basic-information.Surname', 'basic-information.PrivateEmail', 'study.ProgrammesAvailable', 'study.Name', 'courses.Name as CourseName','courses.CourseDescription')
            ->where('courses.ID', $courseId)
            ->first();
        
        return view('coordinator.viewCourse', compact('results'));

    }

    
}

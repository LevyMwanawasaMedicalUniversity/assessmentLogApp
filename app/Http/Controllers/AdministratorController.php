<?php

namespace App\Http\Controllers;

use App\Models\CATypeMarksAllocation;
use App\Models\CourseAssessment;
use App\Models\CourseAssessmentScores;
use App\Models\CourseComponentAllocation;
use App\Models\EduroleBasicInformation;
use App\Models\EduroleCourseElective;
use App\Models\EduroleCourses;
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

        $courseAssessments = CourseAssessment::leftJoin('students_continous_assessments as ca', 'ca.course_assessment_id', '=', 'course_assessments.course_assessments_id')
            ->whereNull('ca.course_assessment_id')
            ->select('course_assessments.*')
            ->get();

        foreach ($courseAssessments as $courseAssessment) {
            $courseAssessment->delete();
        }

        $courseAssessments = CourseAssessment::all(); 

        $assessmentsToDelete = StudentsContinousAssessment::leftJoin('course_assessments', 'students_continous_assessments.course_assessment_id', '=', 'course_assessments.course_assessments_id')
            ->whereNull('course_assessments.course_assessments_id')
            ->select('students_continous_assessments.students_continous_assessment_id')
            ->get();

            // return $assessmentsToDelete;

        // Loop through the assessments and delete them
        foreach ($assessmentsToDelete as $assessment) {
            $assessmentInstance = StudentsContinousAssessment::find($assessment->students_continous_assessment_id);
            if ($assessmentInstance) {
                $assessmentInstance->delete();
            }
        }

        $assessmentsToDelete = CourseAssessment::leftJoin('students_continous_assessments', 'course_assessments.course_assessments_id', '=', 'students_continous_assessments.course_assessment_id')
            ->whereNull('students_continous_assessments.course_assessment_id')
            ->select('course_assessments.course_assessments_id')
            ->get();

        // Loop through the assessments and delete them
        foreach ($assessmentsToDelete as $assessment) {
            $assessmentInstance = CourseAssessment::find($assessment->course_assessments_id);
            if ($assessmentInstance) {
                $assessmentInstance->delete();
            }
        }

        $courseAssessments = CourseAssessment::all(); 
        $courseAssessmenetTypes = CATypeMarksAllocation::all();
        // return $courseAssessmenetTypes;

        foreach($courseAssessments as $courseAssessment){
            $courseId = $courseAssessment->course_id;
            $basicInformationId = $courseAssessment->basic_information_id;
            $delivery = $courseAssessment->delivery_mode;
            $studyId = $courseAssessment->study_id;
            $componentId = $courseAssessment->component_id;
            $course_assessmet_id = $courseAssessment->course_assessments_id;
            $assessmentTypes = $courseAssessment->ca_type;
            $courseAssessmenetTypes = CATypeMarksAllocation::where('course_id', $courseId)
                ->where('study_id', $studyId)
                ->where('delivery_mode', $delivery)
                ->where('component_id', $componentId)
                ->join('assessment_types', 'assessment_types.id', '=', 'c_a_type_marks_allocations.assessment_type_id')
                ->get();

            $academicYear = 2024;
            $getCoure = EduroleCourses::where('ID', $courseId)->first();
            $courseCode = $getCoure->Name;

            foreach ($courseAssessmenetTypes as $courseAssessmentType) {
                $studentsInAssessmentType = CourseAssessmentScores::where('course_code', $courseCode)
                    ->where('delivery_mode', $delivery)
                    ->where('study_id', $studyId)
                    ->where('course_assessment_id', $course_assessmet_id)
                    ->where('component_id', $componentId)
                    ->get();

                foreach ($studentsInAssessmentType as $studentNumber) {
                    $cooedinatorController->refreshAllStudentsMarks(
                        $courseId,
                        $academicYear,
                        $courseAssessmentType->assessment_type_id,
                        $studentNumber->student_id,
                        $studentNumber->course_assessment_id,
                        $delivery,
                        $studentNumber->study_id,
                        $componentId
                    );
                }
            }
        }

        

        // foreach ($courseAssessmenetTypes as $courseAssessmentType) {
        //     // $coursesInEdurole = $this->getCoursesFromEdurole()
        //     //     ->where('courses.ID', $courseAssessment->course_id)
        //     //     ->where('study.ProgrammesAvailable', $courseAssessment->basic_information_id)
        //     //     ->where('study.Delivery', $courseAssessment->delivery_mode)
        //     //     ->first();
        //     $getCoure = EduroleCourses::where('ID', $courseAssessmentType->course_id)->first();
        //     $courseCode = $getCoure->Name;
        //     $studentsInAssessmentType = CourseAssessmentScores::where('course_code', $courseCode)->get();
        //     // $studentInCourseAssessment = $studentAssessments->unique('student_id');
        //     try{
        //         foreach ($studentsInAssessmentType as $student) {
        //             $cooedinatorController->refreshAllStudentsMarks(
        //                 $courseAssessmentType->course_id, 
        //                 $academicYear, 
        //                 $courseAssessmentType->assessment_type_id, 
        //                 $student->student_id, 
        //                 $student->course_assessment_id, 
        //                 $courseAssessmentType->delivery_mode, 
        //                 $student->study_id,
        //                 $courseAssessment->component_id
        //             );
        //         }
        //     }catch (Exception $e) {
        //         Log::error('Error refreshing student marks: ' . $e->getMessage());
        //         continue;
        //     }
        // }

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
        ->paginate(100); // Eager load the related user

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

    public function viewCoordinators()
    {
        return $this->generateCoordinatorsView();
    }

    public function viewCoordinatorsUnderDean($schoolId)
    {
        $schoolId = Crypt::decrypt($schoolId);
        return $this->generateCoordinatorsView($schoolId);
    }

    private function generateCoordinatorsView($schoolId = null)
    {
        // Fetch courses from Edurole, optionally filtering by school ID
        $coursesFromEduroleQuery = $this->getCoursesFromEdurole()
            ->orderBy('schools.Name')
            ->orderBy('study.Name')
            ->orderBy('basic-information.FirstName');

        if ($schoolId) {
            $coursesFromEduroleQuery->where('study.ParentID', $schoolId);
        }

        $coursesFromEdurole = $coursesFromEduroleQuery->get();
        
        // Generate necessary data for view
        $resultsForCount = $coursesFromEduroleQuery
            ->orderBy('programmes.Year')
            ->orderBy('courses.Name')
            ->orderBy('study.Delivery')
            ->get();
        $coursesFromCourseElectivesQuery =EduroleCourseElective::select('course-electives.CourseID')
        ->join('courses', 'courses.ID', '=', 'course-electives.CourseID')
        ->join('program-course-link', 'program-course-link.CourseID', '=', 'courses.ID')
        ->join('student-study-link', 'student-study-link.StudentID', '=', 'course-electives.StudentID')
        ->join('study', 'study.ID', '=', 'student-study-link.StudyID')
        ->where('course-electives.Year', 2024)
        ->where('course-electives.Approved', 1);
        // return $coursesFromEdurole;

        // $totalCoursesCoordinated = $coursesFromEdurole->unique('ID','Delivery','StudyID')->count();
        $totalCoursesCoordinated = $coursesFromEdurole->unique(function ($item) {
            return $item['ID'] . '-' . $item['Delivery'] . '-' . $item['StudyID'];
        })->count();
        $counts = $coursesFromEdurole->countBy('StudyID');
        $results = $coursesFromEdurole->unique('basicInformationId', 'Name');

        // Return the view with the data
        return view('dean.viewCoordinators', compact('schoolId','coursesFromCourseElectivesQuery','resultsForCount', 'results', 'counts', 'totalCoursesCoordinated'));
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
        
        $getStudyId = EduroleStudy::where('ProgrammesAvailable', '=', $basicInformationId)->first();
        // return $getStudyId;
        $studyId = $getStudyId->ID;
        // return $coursesFromCourseElectives;

        
        // $naturalScienceCourses = $this->getNSAttachedCourses();
        if($studyId == 163 || $studyId == 165){
            $results = $this->getCoursesFromEdurole()
            ->where('basic-information.ID', $basicInformationId)            
            ->orderBy('programmes.Year')
            ->orderBy('courses.Name')
            ->orderBy('study.Delivery')            
            ->get();
        }else{
            $coursesFromCourseElectives = EduroleCourseElective::select('course-electives.CourseID')
                ->join('courses', 'courses.ID','=','course-electives.CourseID')
                ->join('program-course-link', 'program-course-link.CourseID','=','courses.ID')
                ->join('student-study-link','student-study-link.StudentID','=','course-electives.StudentID')
                ->join('study','study.ID','=','student-study-link.StudyID')
                ->where('course-electives.Year', 2024)
                ->where('course-electives.Approved', 1)
                ->where('study.ProgrammesAvailable', $basicInformationId)
                ->distinct()
                ->pluck('course-electives.CourseID')
                ->toArray();
            $results = $this->getCoursesFromEdurole()
                ->where('basic-information.ID', $basicInformationId)
                ->whereIn('courses.ID', $coursesFromCourseElectives)
                ->orderBy('programmes.Year')
                ->orderBy('courses.Name')
                ->orderBy('study.Delivery')            
                ->get();
        }
        
        return view('coordinator.viewCoordinatorsCourses', compact('results'));

    }

    public function viewCoordinatorsCoursesWithComponents($courseIdEncrypt,$basicInformationIdEncrypt,$deliveryEncrypt,$studyIdEncrypt){

        $courseId = Crypt::decrypt($courseIdEncrypt);
        // return $courseIdEncrypt;
        // return $basicInformationIdEncrypt;
        $basicInformationId = Crypt::decrypt($basicInformationIdEncrypt);
        // return $basicInformationId;
        $delivery = Crypt::decrypt($deliveryEncrypt);
        $studyId = Crypt::decrypt($studyIdEncrypt);
        $academicYear = 2024;
        // return $courseId . ' ' . $basicInformationId . ' ' . $delivery . ' ' . $studyId;
        $naturalScienceCourses = $this->getNSAttachedCourses();
        $results = $this->getAllocatedCourses($courseId,$basicInformationId,$delivery,$studyId, $academicYear);
        $getCoure = EduroleCourses::where('ID', $courseId)->first();
        $courseCode = $getCoure->Name;
        $getStudy = EduroleStudy::where('ID', $studyId)->first();
        $studyName = $getStudy->Name;
        $deliveryMode = $getStudy->Delivery;
                
        return view('coordinator.caComponents.viewCoordinatorsCourses', compact('results','courseCode','studyName','deliveryMode','basicInformationId'));

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

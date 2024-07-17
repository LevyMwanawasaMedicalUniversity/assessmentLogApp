<?php

namespace App\Http\Controllers;

use App\Models\EduroleBasicInformation;
use App\Models\EduroleStudy;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;

class AdministratorController extends Controller
{
    //
    public function index()
    {
        return view('admin.index');
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
    

    public function viewCoordinators(){
        $results = EduroleStudy::join('basic-information', 'basic-information.ID', '=', 'study.ProgrammesAvailable')
            ->join('study-program-link', 'study-program-link.StudyID', '=', 'study.ID')
            ->join('programmes', 'programmes.ID', '=', 'study-program-link.ProgramID')
            ->join('program-course-link', 'program-course-link.ProgramID', '=', 'programmes.ID')
            ->join('courses', 'courses.ID', '=', 'program-course-link.CourseID')
            ->join('schools', 'schools.ID', '=', 'study.ParentID')
            ->select('basic-information.ID','basic-information.Firstname', 'basic-information.Surname', 'basic-information.PrivateEmail', 'study.ProgrammesAvailable', 'study.Name', 'courses.Name as CourseName')
            // ->where('basic-information.ID', $basicInformationId)
            ->get();
        $coursesWithCA = $this->getCoursesFromLMMAX();
        
        // return $coursesWithCA;
        $counts = $results->countBy('ID');

        $withCa = $results->whereIn('CourseName', $coursesWithCA)->countBy('ID');
        $results= $results->unique('ID');

        $totalCoursesCoordinated = $counts->sum();
        $totalCoursesWithCA = $withCa->sum();
        return view('dean.viewCoordinators', compact('results', 'counts','withCa','totalCoursesCoordinated','totalCoursesWithCA'));
    }

    public function viewCoordinatorsUnderDean($schoolId){
        $schoolId = Crypt::decrypt($schoolId);
        $results = EduroleStudy::join('basic-information', 'basic-information.ID', '=', 'study.ProgrammesAvailable')
            ->join('study-program-link', 'study-program-link.StudyID', '=', 'study.ID')
            ->join('programmes', 'programmes.ID', '=', 'study-program-link.ProgramID')
            ->join('program-course-link', 'program-course-link.ProgramID', '=', 'programmes.ID')
            ->join('courses', 'courses.ID', '=', 'program-course-link.CourseID')
            ->select('basic-information.ID','basic-information.Firstname', 'basic-information.Surname', 'basic-information.PrivateEmail', 'study.ProgrammesAvailable', 'study.Name', 'courses.Name as CourseName')
            ->where('study.ParentID', $schoolId)
            ->get();
            $counts = $results->countBy('ID');
            $results= $results->unique('ID');
        return view('dean.viewCoordinators', compact('results', 'counts'));
    }

    public function viewDeans(){
        $results = EduroleBasicInformation::join('access', 'access.ID', '=', 'basic-information.ID')
            ->join('roles', 'roles.ID', '=', 'access.RoleID')
            ->join('schools', 'schools.Dean', '=', 'basic-information.ID')
            ->join('study', 'study.ParentID', '=', 'schools.ID')
            ->select('basic-information.FirstName', 'basic-information.Surname', 'basic-information.ID', 'roles.RoleName', 'schools.ID as ParentID', 'study.ID as StudyID', 'schools.Name as SchoolName')
            ->get();
            $counts = $results->countBy('ID');
            $results= $results->unique('ID');
        return view('registrar.viewDeans', compact('results', 'counts'));
    }

    public function viewCoordinatorsCourses($basicInformationId){

        $basicInformationId = Crypt::decrypt($basicInformationId);
        $results = EduroleStudy::join('basic-information', 'basic-information.ID', '=', 'study.ProgrammesAvailable')
            ->join('study-program-link', 'study-program-link.StudyID', '=', 'study.ID')
            ->join('programmes', 'programmes.ID', '=', 'study-program-link.ProgramID')
            ->join('program-course-link', 'program-course-link.ProgramID', '=', 'programmes.ID')
            ->join('courses', 'courses.ID', '=', 'program-course-link.CourseID')
            ->select('courses.ID','basic-information.Firstname','basic-information.ID as basicInformationId', 'basic-information.Surname', 'basic-information.PrivateEmail', 'study.ProgrammesAvailable', 'study.Name', 'courses.Name as CourseName','courses.CourseDescription')
            ->where('basic-information.ID', $basicInformationId)
            ->get();
        
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

<?php

namespace App\Http\Controllers;

use App\Models\EduroleBasicInformation;
use App\Models\EduroleStudy;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

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
                // If it does, set the email to $result->ID@lmmu.ac.zm
                $email = $result->ID . '@lmmu.ac.zm';
            }
            try {
                $user = User::updateOrCreate(
                    [
                        'basic_information_id' => $result->ID
                    ],
                    [
                        'name' => $result->Firstname . ' ' . $result->Surname,
                        'password' => bcrypt('12345678'),
                        'school_id' => $result->ParentID,
                        'email' => $email
                    ]
                );

                $studentRole = Role::firstOrCreate(['name' => 'Coordinator']);
                $studentPermission = Permission::firstOrCreate(['name' => 'Coordinator']);
                $user->assignRole($studentRole);
                $user->givePermissionTo($studentPermission);
            } catch (\Exception $e) {
                // Log the error message
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
                if (User::where('email', $email)->where('basic_information_id', '!=',$result->ID )->exists()) {
                    // If it does, set the email to $result->ID@lmmu.ac.zm
                    $email = $result->ID . '@lmmu.ac.zm';
                }
                try {
                    $user = User::updateOrCreate(
                        [
                            'basic_information_id' => $result->ID
                        ],
                        [
                            'name' => $result->FirstName . ' ' . $result->Surname,
                            'password' => bcrypt('12345678'),
                            'school_id' => $result->ParentID,
                            'email' => $email
                        ]
                    );

                    $studentRole = Role::firstOrCreate(['name' => 'Dean']);
                    $studentPermission = Permission::firstOrCreate(['name' => 'Dean']);
                    $user->assignRole($studentRole);
                    $user->givePermissionTo($studentPermission);
                } catch (\Exception $e) {
                    // Log the error message
                    Log::error('Error creating user: ' . $e->getMessage());
                    continue;
                }
            }        

        return redirect()->back()->with('success', 'Deans imported successfully');
    }

    public function viewCoordinators(){
        $results = EduroleStudy::join('basic-information', 'basic-information.ID', '=', 'study.ProgrammesAvailable')
            ->join('study-program-link', 'study-program-link.StudyID', '=', 'study.ID')
            ->join('programmes', 'programmes.ID', '=', 'study-program-link.ProgramID')
            ->join('program-course-link', 'program-course-link.ProgramID', '=', 'programmes.ID')
            ->join('courses', 'courses.ID', '=', 'program-course-link.CourseID')
            ->select('basic-information.ID','basic-information.Firstname', 'basic-information.Surname', 'basic-information.PrivateEmail', 'study.ProgrammesAvailable', 'study.Name', 'courses.Name as CourseName')
            // ->where('basic-information.ID', $basicInformationId)
            ->get();
            $counts = $results->countBy('ID');
            $results= $results->unique('ID');
        return view('admin.viewCoordinators', compact('results', 'counts'));
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
        return view('admin.viewCoordinators', compact('results', 'counts'));
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
        return view('admin.viewDeans', compact('results', 'counts'));
    }

    public function viewCoordinatorsCourses($basicInformationId){

        $basicInformationId = Crypt::decrypt($basicInformationId);
        $results = EduroleStudy::join('basic-information', 'basic-information.ID', '=', 'study.ProgrammesAvailable')
            ->join('study-program-link', 'study-program-link.StudyID', '=', 'study.ID')
            ->join('programmes', 'programmes.ID', '=', 'study-program-link.ProgramID')
            ->join('program-course-link', 'program-course-link.ProgramID', '=', 'programmes.ID')
            ->join('courses', 'courses.ID', '=', 'program-course-link.CourseID')
            ->select('courses.ID','basic-information.Firstname', 'basic-information.Surname', 'basic-information.PrivateEmail', 'study.ProgrammesAvailable', 'study.Name', 'courses.Name as CourseName','courses.CourseDescription')
            ->where('basic-information.ID', $basicInformationId)
            ->get();
        
        return view('admin.viewCoordinatorsCourses', compact('results'));

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
        
        return view('admin.viewCourse', compact('results'));

    }

    
}

<?php

namespace App\Http\Controllers;

use App\Models\EduroleStudy;
use App\Models\User;
use Illuminate\Http\Request;
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
        $results = EduroleStudy::join('basic-information', 'basic-information.ID', '=', 'study.ProgrammesAvailable')
            ->select('basic-information.ID','basic-information.Firstname', 'basic-information.Surname', 'basic-information.PrivateEmail', 'study.ProgrammesAvailable', 'study.Name')
            ->get();
        
            foreach ($results as $result) {
                $email = trim($result->PrivateEmail);
                $user = User::updateOrCreate(
                    ['email' => $email],
                    [
                        'name' => $result->Firstname . ' ' . $result->Surname,
                        'password' => bcrypt('12345678'),
                        'basic_information_id' => $result->ID,
                    ]
                );

                $studentRole = Role::firstOrCreate(['name' => 'Coordinator']);
                $studentPermission = Permission::firstOrCreate(['name' => 'Coordinator']);
                $user->assignRole($studentRole);
                $user->givePermissionTo($studentPermission);
            }        

        return $results;
    }

    public function viewCoordinatorsCourses($basicInformationId){
        $results = EduroleStudy::join('basic-information', 'basic-information.ID', '=', 'study.ProgrammesAvailable')
            ->join('study-program-link', 'study-program-link.StudyID', '=', 'study.ID')
            ->join('programmes', 'programmes.ID', '=', 'study-program-link.ProgramID')
            ->join('program-course-link', 'program-course-link.ProgramID', '=', 'programmes.ID')
            ->join('courses', 'courses.ID', '=', 'program-course-link.CourseID')
            ->select('basic-information.Firstname', 'basic-information.Surname', 'basic-information.PrivateEmail', 'study.ProgrammesAvailable', 'study.Name', 'courses.Name as CourseName')
            ->where('basic-information.ID', $basicInformationId)
            ->get();
        
        return $results;

    }

    
}

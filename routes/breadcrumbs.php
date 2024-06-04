<?php

use App\Models\CourseAssessment;
use App\Models\EduroleCourses;
use App\Models\EduroleStudy;
use DaveJamesMiller\Breadcrumbs\Facades\Breadcrumbs;
use Illuminate\Support\Facades\Crypt;

Breadcrumbs::for('dashboard', function ($trail) {
    $trail->push('Home', route('dashboard'));
});

Breadcrumbs::for('pages.upload', function ($trail) {
    $trail->parent('dashboard');
    $trail->push('My Courses', route('pages.upload'));
});

Breadcrumbs::for('users.index', function ($trail) {
    $trail->parent('dashboard');
    $trail->push('Users', route('users.index'));
});

Breadcrumbs::for('users.edit', function ($trail, $user) {
    $trail->parent('users.index');
    $trail->push('Edit User', route('users.edit', $user));
});

Breadcrumbs::for('users.create', function ($trail) {
    $trail->parent('users.index');
    $trail->push('Create User', route('users.create'));
});
////////////////
Breadcrumbs::for('roles.index', function ($trail) {
    $trail->parent('dashboard');
    $trail->push('Roles', route('roles.index'));
});

Breadcrumbs::for('roles.edit', function ($trail, $role) {
    $trail->parent('roles.index');
    $trail->push('Edit Role', route('roles.edit', $role));
});

Breadcrumbs::for('roles.create', function ($trail) {
    $trail->parent('roles.index');
    $trail->push('Create Role', route('roles.create'));
});
//////////
Breadcrumbs::for('permissions.index', function ($trail) {
    $trail->parent('dashboard');
    $trail->push('Permissions', route('permissions.index'));
});

Breadcrumbs::for('permissions.edit', function ($trail, $permission) {
    $trail->parent('permissions.index');
    $trail->push('Edit Permission', route('permissions.edit', $permission));
});

Breadcrumbs::for('permissions.create', function ($trail) {
    $trail->parent('permissions.index');
    $trail->push('Create Permission', route('permissions.create'));
});

/////////

Breadcrumbs::for('caAssessmentTypes.index', function ($trail) {
    $trail->parent('dashboard');
    $trail->push('Assessment Types', route('caAssessmentTypes.index'));
});

Breadcrumbs::for('caAssessmentTypes.edit', function ($trail, $permission) {
    $trail->parent('caAssessmentTypes.index');
    $trail->push('Edit Assessment Type', route('caAssessmentTypes.edit', $permission));
});

Breadcrumbs::for('caAssessmentTypes.create', function ($trail) {
    $trail->parent('caAssessmentTypes.index');
    $trail->push('Create Assessment Type', route('caAssessmentTypes.create'));
});

///////////////////

Breadcrumbs::for('admin.viewDeans', function ($trail) {
    $trail->parent('dashboard');
    $trail->push('Deans', route('admin.viewDeans'));
});

Breadcrumbs::for('admin.viewCoordinatorsUnderDean', function ($trail, $schoolId) {
    if(auth()->user()->hasRole('Dean')){
        $trail->parent('dashboard');
    }else{
        $trail->parent('admin.viewDeans');
    }
    
    $trail->push('View Coordinators', route('admin.viewCoordinatorsUnderDean', $schoolId));
});

// Breadcrumbs::for('admin.viewCoordinatorsCourses', function ($trail, $basicInformationId) {
//     $trail->parent('admin.viewCoordinators',);
//     $trail->push('View Coordinators', route('admin.viewCoordinatorsCourses', $basicInformationId));
// });

Breadcrumbs::for('admin.viewCoordinators', function ($trail) {
    $trail->parent('dashboard',);
    $trail->push('View Coordinators', route('admin.viewCoordinators'));
});

Breadcrumbs::for('admin.viewCoordinatorsCourses', function ($trail, $basicInformationId) {
    $trail->parent('admin.viewCoordinators');
    $trail->push('Coordinators Courses', route('admin.viewCoordinatorsCourses', $basicInformationId));
});

Breadcrumbs::for('coordinator.uploadCa', function ($trail, $statusId, $courseIdValue) {
    $courseId = Crypt::decrypt($courseIdValue);

    $results = EduroleStudy::join('basic-information', 'basic-information.ID', '=', 'study.ProgrammesAvailable')
        ->join('study-program-link', 'study-program-link.StudyID', '=', 'study.ID')
        ->join('programmes', 'programmes.ID', '=', 'study-program-link.ProgramID')
        ->join('program-course-link', 'program-course-link.ProgramID', '=', 'programmes.ID')
        ->join('courses', 'courses.ID', '=', 'program-course-link.CourseID')
        ->select('courses.ID','basic-information.Firstname', 'basic-information.Surname', 'basic-information.PrivateEmail', 'study.ProgrammesAvailable', 'study.Name', 'courses.Name as CourseName','courses.CourseDescription','basic-information.ID as basicInformationId')
        ->where('courses.ID', $courseId)
        ->first();

    $basicInformationId = encrypt($results->basicInformationId);

    // Conditional parent based on the role
    if (auth()->user()->hasRole('Coordinator')) {
        $trail->parent('pages.upload');
    } else {
        $trail->parent('admin.viewCoordinatorsCourses', $basicInformationId);
    }

    // Generate the route correctly with all required parameters
    $trail->push('Upload CA', route('coordinator.uploadCa', ['statusId' => $statusId, 'courseIdValue' => $courseIdValue]));
});

Breadcrumbs::for('coordinator.courseCASettings', function ($trail, $courseIdValue) {
    $courseId = Crypt::decrypt($courseIdValue);

    $results = EduroleStudy::join('basic-information', 'basic-information.ID', '=', 'study.ProgrammesAvailable')
        ->join('study-program-link', 'study-program-link.StudyID', '=', 'study.ID')
        ->join('programmes', 'programmes.ID', '=', 'study-program-link.ProgramID')
        ->join('program-course-link', 'program-course-link.ProgramID', '=', 'programmes.ID')
        ->join('courses', 'courses.ID', '=', 'program-course-link.CourseID')
        ->select('courses.ID','basic-information.Firstname', 'basic-information.Surname', 'basic-information.PrivateEmail', 'study.ProgrammesAvailable', 'study.Name', 'courses.Name as CourseName','courses.CourseDescription','basic-information.ID as basicInformationId')
        ->where('courses.ID', $courseId)
        ->first();

    $basicInformationId = encrypt($results->basicInformationId);

    // Conditional parent based on the role
    if (auth()->user()->hasRole('Coordinator')) {
        $trail->parent('pages.upload');
    } else {
        $trail->parent('admin.viewCoordinatorsCourses', $basicInformationId);
    }

    // Generate the route correctly with all required parameters
    $trail->push('Course Settings', route('coordinator.courseCASettings', ['courseIdValue' => $courseId, ]));
});



Breadcrumbs::for('coordinator.editCaInCourse', function ($trail, $statusId, $courseIdValue) {
    $courseId = Crypt::decrypt($courseIdValue);

    $results = EduroleStudy::join('basic-information', 'basic-information.ID', '=', 'study.ProgrammesAvailable')
        ->join('study-program-link', 'study-program-link.StudyID', '=', 'study.ID')
        ->join('programmes', 'programmes.ID', '=', 'study-program-link.ProgramID')
        ->join('program-course-link', 'program-course-link.ProgramID', '=', 'programmes.ID')
        ->join('courses', 'courses.ID', '=', 'program-course-link.CourseID')
        ->select('courses.ID','basic-information.Firstname', 'basic-information.Surname', 'basic-information.PrivateEmail', 'study.ProgrammesAvailable', 'study.Name', 'courses.Name as CourseName','courses.CourseDescription','basic-information.ID as basicInformationId')
        ->where('courses.ID', $courseId)
        ->first();

    $basicInformationId = encrypt($results->basicInformationId);

    // Conditional parent based on the role
    if (auth()->user()->hasRole('Coordinator')) {
        $trail->parent('pages.upload');
    } else {
        $trail->parent('admin.viewCoordinatorsCourses', $basicInformationId);
    }

    // Generate the route correctly with all required parameters
    $trail->push('Upload CA', route('coordinator.editCaInCourse', ['courseAssessmenId' => $statusId, 'courseId' => $courseIdValue]));
});

Breadcrumbs::for('coordinator.viewAllCaInCourse', function ($trail, $statusId, $courseIdValue) {
    $courseId = Crypt::decrypt($courseIdValue);

    $results = EduroleStudy::join('basic-information', 'basic-information.ID', '=', 'study.ProgrammesAvailable')
        ->join('study-program-link', 'study-program-link.StudyID', '=', 'study.ID')
        ->join('programmes', 'programmes.ID', '=', 'study-program-link.ProgramID')
        ->join('program-course-link', 'program-course-link.ProgramID', '=', 'programmes.ID')
        ->join('courses', 'courses.ID', '=', 'program-course-link.CourseID')
        ->select('courses.ID','basic-information.Firstname', 'basic-information.Surname', 'basic-information.PrivateEmail', 'study.ProgrammesAvailable', 'study.Name', 'courses.Name as CourseName','courses.CourseDescription','basic-information.ID as basicInformationId')
        ->where('courses.ID', $courseId)
        ->first();

    $basicInformationId = encrypt($results->basicInformationId);

    // Make sure to pass the correct parameters to the parent breadcrumb
    if (auth()->user()->hasRole('Coordinator')) {
        $trail->parent('pages.upload');
    } else {
        $trail->parent('admin.viewCoordinatorsCourses', $basicInformationId);
    }

    // Generate the route correctly with all required parameters
    $trail->push('View CA', route('coordinator.viewAllCaInCourse', ['statusId' => $statusId, 'courseIdValue' => $courseIdValue]));
});

Breadcrumbs::for('coordinator.viewSpecificCaInCourse', function ($trail, $statusId, $courseIdValue) {
    // Make sure to pass the correct parameters to the parent breadcrumb
    $courseId = Crypt::decrypt($courseIdValue);
    $results = CourseAssessment::where('course_assessments.course_assessments_id', $courseId)
            // ->where('ca_type', $statusId)
            ->join('course_assessment_scores', 'course_assessments.course_assessments_id', '=', 'course_assessment_scores.course_assessment_id')
            ->orderBy('course_assessments.created_at', 'asc')
            ->first();
    $courseCode = $results->course_code;
    $getCourses = EduroleCourses::where('courses.Name', $courseCode)
        ->select('courses.ID')
        ->first();
    $courseIdValueForBreadCrumb = Crypt::encrypt($getCourses->ID);
        
    $trail->parent('coordinator.viewAllCaInCourse', $statusId, $courseIdValueForBreadCrumb);

    // Generate the route correctly with all required parameters
    $trail->push('View The Marks', route('coordinator.viewSpecificCaInCourse', ['statusId' => $statusId, 'courseIdValue' => $courseIdValue]));
});

Breadcrumbs::for('coordinator.viewTotalCaInCourse', function ($trail, $statusId, $courseIdValue) {
    $courseId = Crypt::decrypt($courseIdValue);

    $results = EduroleStudy::join('basic-information', 'basic-information.ID', '=', 'study.ProgrammesAvailable')
        ->join('study-program-link', 'study-program-link.StudyID', '=', 'study.ID')
        ->join('programmes', 'programmes.ID', '=', 'study-program-link.ProgramID')
        ->join('program-course-link', 'program-course-link.ProgramID', '=', 'programmes.ID')
        ->join('courses', 'courses.ID', '=', 'program-course-link.CourseID')
        ->select('courses.ID','basic-information.Firstname', 'basic-information.Surname', 'basic-information.PrivateEmail', 'study.ProgrammesAvailable', 'study.Name', 'courses.Name as CourseName','courses.CourseDescription','basic-information.ID as basicInformationId')
        ->where('courses.ID', $courseId)
        ->first();

    $basicInformationId = encrypt($results->basicInformationId);

    // Make sure to pass the correct parameters to the parent breadcrumb
    if (auth()->user()->hasRole('Coordinator')) {
        $trail->parent('pages.upload');
    } else {
        $trail->parent('admin.viewCoordinatorsCourses', $basicInformationId);
    }
        
    
    // Generate the route correctly with all required parameters
    $trail->push('Total Ca', route('coordinator.viewTotalCaInCourse', ['statusId' => $statusId, 'courseIdValue' => $courseIdValue]));
});







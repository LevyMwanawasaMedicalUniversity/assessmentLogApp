<?php

use App\Http\Controllers\AdministratorController;
use App\Http\Controllers\CaAssementTypesController;
use App\Http\Controllers\ContinousAssessmentController;
use App\Http\Controllers\CoordinatorController;
use App\Http\Controllers\CourseComponentsController;
use App\Http\Controllers\PagesController;
use App\Http\Controllers\PermissionsController;
use App\Http\Controllers\PhoneNumberController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\TwoFactorController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }else{
        return view('auth.login');    
    }    
});
Route::get('phone-number', [PhoneNumberController::class,'showPhoneNumberForm'])->name('phone.number.form');
Route::post('phone-number', [PhoneNumberController::class, 'storePhoneNumber'])->name('phone.number.store');
Route::post('verify-phone-number', [PhoneNumberController::class, 'verifyPhoneNumber'])->name('phone.number.verify');

Route::get('2fa', [TwoFactorController::class, 'show2faForm'])->name('2fa.form');
Route::post('2fa', [TwoFactorController::class, 'verify2fa'])->name('2fa.verify');

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');
Auth::routes(['register' => false]);
Route::middleware(['auth'])->group(function () {
    Route::get('/password/change', [PagesController::class, 'showChangeForm'])->name('password.change');
    // Route::post('/password/change/', [PagesController::class, 'forcePasswordUpdate'])->name('password.forceUpdate');
});
    // Route::middleware(['auth','2fa'])->group(function () {
Route::middleware(['auth','force.password.change'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    // Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/dashboard', function() {
        // Get basic statistics for the dashboard
        $studentsWithCA = 0;
        $totalCoursesCoordinated = 0;
        $totalCoursesWithCA = 0;
        
        try {
            $studentsWithCA = \App\Models\SisReportsStudent::distinct()
                ->where('status', 6)
                ->count();
                
            $totalCoursesCoordinated = \Illuminate\Support\Facades\DB::table('courses')
                ->distinct()
                ->count();
                
            $totalCoursesWithCA = \App\Models\CourseAssessment::distinct(['course_id', 'delivery_mode', 'study_id'])
                ->count();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Dashboard error: ' . $e->getMessage());
        }
        
        // Get role information safely using DB query instead of relying on methods that might not exist
        $userPermissions = [
            'isAdmin' => false,
            'isCoordinator' => false, 
            'isDean' => false,
            'isRegistrar' => false
        ];
        
        try {
            $userId = \Illuminate\Support\Facades\Auth::id();
            if ($userId) {
                // Check for roles via direct DB query to model_has_roles table
                $roles = \Illuminate\Support\Facades\DB::table('model_has_roles')
                    ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                    ->where('model_has_roles.model_id', $userId)
                    ->pluck('roles.name')
                    ->toArray();
                
                // Check for specific roles
                $userPermissions['isAdmin'] = in_array('Administrator', $roles);
                $userPermissions['isCoordinator'] = in_array('Coordinator', $roles);
                $userPermissions['isDean'] = in_array('Dean', $roles);
                $userPermissions['isRegistrar'] = in_array('Registrar', $roles);
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Permission checking error: ' . $e->getMessage());
        }
        
        // Check if request wants Inertia response
        if (\Illuminate\Support\Facades\Request::header('X-Inertia')) {
            return Inertia\Inertia::render('Dashboard/Dashboard', [
                'studentsWithCA' => $studentsWithCA,
                'totalCoursesCoordinated' => $totalCoursesCoordinated,
                'totalCoursesWithCA' => $totalCoursesWithCA,
                'userPermissions' => $userPermissions
            ]);
        }
        
        // Default to blade template
        return view('dashboard', [
            'studentsWithCA' => $studentsWithCA,
            'totalCoursesCoordinated' => $totalCoursesCoordinated,
            'totalCoursesWithCA' => $totalCoursesWithCA
        ]);
    })->name('dashboard');
    Route::get('/test-dashboard', [PagesController::class, 'testDashboard'])->name('test.dashboard');
    

    
    Route::middleware('can:Administrator')->group(function () {
        Route::get('/resultsReviewerr', [UserController::class, 'resultsReviewer'])->name('administrator.resultsReviewer');
        Route::POST('/coordinator/importGradesForReview',[AdministratorController::class, 'importGradesForReview'])->name('admin.importGradesForReview');
        Route::resource('users', UserController::class);
        Route::resource('roles', RolesController::class);
        Route::resource('permissions', PermissionsController::class);
        Route::resource('caAssessmentTypes', CaAssementTypesController::class);
        Route::resource('courseComponents', CourseComponentsController::class);
        Route::get('user', [UserController::class, 'index'])->name('users.index');
        Route::get('/user/searchForUser', 'UserController@searchForUser')->name('users.searchForUser');
        Route::get('/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/store', [UserController::class, 'store'])->name('users.store');
        Route::get('/{user}/show', 'UserController@show')->name('users.show');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::post('/{user}/resetUserPassword', [UserController::class, 'resetUserPassword'])->name('users.resetUserPassword');
        Route::patch('/{user}/update', [UserController::class, 'update'])->name('users.update');
        Route::post('/{user}/delete', 'UserController@destroy')->name('users.destroy');
        Route::post('/{user}/resetPassword', 'UserController@resetPassword')->name('admin.resetPassword');
        Route::get('/admin/index',[AdministratorController::class, 'index'])->name('admin.index');
        Route::get('/admin/users',[UserController::class, 'index'])->name('admin.users');
        Route::post('/admin/importCoordinators',[AdministratorController::class, 'importCoordinators'])->name('admin.importCoordinators');
        Route::post('/admin/importDeans',[AdministratorController::class, 'importDeans'])->name('admin.importDeans');
        Route::post('/admin/refreshCAs',[AdministratorController::class, 'refreshCAs'])->name('admin.refreshCAs');
        Route::post('/admin/refreshCAInAprogram',[AdministratorController::class, 'refreshCAInAprogram'])->name('admin.refreshCAInAprogram');
        Route::get('/admin/auditTrails',[AdministratorController::class, 'auditTrails'])->name('admin.auditTrails');
        
    });
    
    
    Route::middleware('can:Coordinator')->group(function () {
        Route::get('/upload', [PagesController::class, 'upload'])->name('pages.upload');
        Route::get('/uploadFinalExam', [PagesController::class, 'uploadFinalExam'])->name('pages.uploadFinalExam');
        Route::get('/uploadFinalExamAndCa', [PagesController::class, 'uploadFinalExamAndCa'])->name('pages.uploadFinalExamAndCa');
        Route::get('/uploadCourseWithComponents/{courseId}/{basicInformationId}/{delivery}/{studyId}', [PagesController::class, 'uploadCourseWithComponents'])->name('pages.uploadCourseWithComponents');
        Route::get('/coordinator/uploadCa/{statusId}/{courseIdValue}/{basicInformationId}',[CoordinatorController::class, 'uploadCa'])->name('coordinator.uploadCa');
        Route::get('/coordinator/uploadCaFinalExam/{courseIdValue}/{basicInformationId}',[CoordinatorController::class, 'uploadCaFinalExam'])->name('coordinator.uploadCaFinalExam');
        Route::get('/coordinator/uploadCaAndFinalExamAtOnce/{courseIdValue}/{basicInformationId}',[CoordinatorController::class, 'uploadCaAndFinalExamAtOnce'])->name('coordinator.uploadCaAndFinalExamAtOnce');
        
        
                    
        Route::POST('/coordinator/importCAFromExcelSheet',[CoordinatorController::class, 'importCAFromExcelSheet'])->name('coordinator.importCAFromExcelSheet');   
        Route::POST('/coordinator/importFinalExamFromExcelSheet',[CoordinatorController::class, 'importFinalExamFromExcelSheet'])->name('coordinator.importFinalExamFromExcelSheet');  
        
        Route::POST('/coordinator/importFinalExamAndCaFromExcelSheet',[CoordinatorController::class, 'importFinalExamAndCaFromExcelSheet'])->name('coordinator.importFinalExamAndCaFromExcelSheet'); 

        Route::POST('/coordinator/importStudentCA',[CoordinatorController::class, 'importStudentCA'])->name('coordinator.importStudentCA');     
        
    });
    Route::middleware('can:ViewTheContionousAssessment')->group(function () { //deans, & registrar permissions included
        Route::get('/coordinator/editCaInCourse/{courseAssessmenId}/{courseId}/{basicInformationId}',[CoordinatorController::class, 'editCaInCourse'])->name('coordinator.editCaInCourse');
        Route::get('/coordinator/editAStudentsCaInCourse/{courseAssessmenId}/{courseId}/{basicInformationId}',[CoordinatorController::class, 'editAStudentsCaInCourse'])->name('coordinator.editAStudentsCaInCourse');

        Route::POST('/coordinator/updateCAFromExcelSheet',[CoordinatorController::class, 'updateCAFromExcelSheet'])->name('coordinator.updateCAFromExcelSheet'); 

        Route::POST('/coordinator/updateCAForSingleStudent',[CoordinatorController::class, 'updateCAForSingleStudent'])->name('coordinator.updateCAForSingleStudent'); 
        Route::POST('/coordinator/updateExamForSingleStudent',[CoordinatorController::class, 'updateExamForSingleStudent'])->name('coordinator.updateExamForSingleStudent'); 
        Route::GET('/exportBoardOfExaminersReport/{basicInformationId}',[CoordinatorController::class, 'exportBoardOfExaminersReport'])->name('coordinator.exportBoardOfExaminersReport');
        Route::GET('/exportBoardOfExaminersReportFinalExam/{basicInformationId}',[CoordinatorController::class, 'exportBoardOfExaminersReportFinalExam'])->name('coordinator.exportBoardOfExaminersReportFinalExam');  


        
        ///////////////////////////////////////////////////
        Route::get('/students/caResult/resultsViewCourses/',[ContinousAssessmentController::class, 'studentsCAResults'] )->name('docket.studentsCAResults');
        Route::get('/students/caResult/viewCaComponents/{courseId}/', [ContinousAssessmentController::class, 'viewCaComponents'])->name('docket.viewCaComponents');
        Route::get('/students/caResult/viewCaComponentsWithComponent/{courseId}/', 'App\Http\Controllers\ContinousAssessmentController@viewCaComponentsWithComponent')->name('docket.viewCaComponentsWithComponent');
        Route::post('/students/caResult/deleteStudentCourseAssements/{courseId}/', 'App\Http\Controllers\ContinousAssessmentController@deleteStudentCourseAssements')->name('docket.deleteStudentCourseAssements');
        
        Route::get('/students/caResult/viewInSpecificCaComponent/{courseId}/{caType}', 'App\Http\Controllers\ContinousAssessmentController@viewInSpecificCaComponent')->name('docket.viewInSpecificCaComponent');
        Route::get('/students/caResult/searchForStudents/', 'App\Http\Controllers\ContinousAssessmentController@searchForStudents')->name('coordinator.searchForStudents');

        
        Route::delete('/coordinator/deleteCaInCourse/{courseAssessmenId}/{courseId}',[CoordinatorController::class, 'deleteCaInCourse'])->name('coordinator.deleteCaInCourse');
        Route::delete('/coordinator/deleteStudentCaInCourse/',[CoordinatorController::class, 'deleteStudentCaInCourse'])->name('coordinator.deleteStudentCaInCourse');
        Route::delete('/coordinator/deleteStudentExamInCourse/',[CoordinatorController::class, 'deleteStudentExamInCourse'])->name('coordinator.deleteStudentExamInCourse');


        Route::get('/coordinator/viewSpecificCaInCourse/{statusId}/{courseIdValue}/{assessmentNumber}',[CoordinatorController::class, 'viewSpecificCaInCourse'])->name('coordinator.viewSpecificCaInCourse');
        Route::get('/coordinator/courseCASetings/{courseIdValue}/{basicInformationId}/{delivery}',[CoordinatorController::class, 'courseCASettings'])->name('coordinator.courseCASettings');
        Route::get('/coordinator/viewCourseWithComponents/{courseIdValue}/{basicInformationId}/{delivery}',[CoordinatorController::class, 'viewCourseWithComponents'])->name('coordinator.viewCourseWithComponents');
        
        Route::post('/coordinator/updateCourseWithComponents/{courseIdValue}',[CoordinatorController::class, 'updateCourseWithComponents'])->name('coordinator.updateCourseWithComponents');
        Route::POST('/coordinator/updateCourseCASetings/{courseIdValue}',[CoordinatorController::class, 'updateCourseCASetings'])->name('coordinator.updateCourseCASetings');
        
        Route::get('viewOnlyProgrammesWithCa',[CoordinatorController::class, 'viewOnlyProgrammesWithCa'])->name('coordinator.viewOnlyProgrammesWithCa');
        Route::get('viewOnlyProgrammesWithCaForCoordinator/{coordinator}',[CoordinatorController::class, 'viewOnlyProgrammesWithCaForCoordinator'])->name('coordinator.viewOnlyProgrammesWithCaForCoordinator');
        Route::get('showCaWithin/{courseId}',[CoordinatorController::class, 'showCaWithin'])->name('coordinator.showCaWithin');
        
        Route::get('/coordinator/viewTotalCaInCourse/{statusId}/{courseIdValue}/{basicInformationId}/{delivery}',[CoordinatorController::class, 'viewTotalCaInCourse'])->name('coordinator.viewTotalCaInCourse');
        Route::get('/coordinator/viewTotalCaInCourseAndFinalExam/{statusId}/{courseIdValue}/{basicInformationId}/{delivery}',[CoordinatorController::class, 'viewTotalCaInCourseAndFinalExam'])->name('coordinator.viewTotalCaInCourseAndFinalExam');
        
        Route::get('/coordinator/viewTotalCaInComponentCourse/{statusId}/{courseIdValue}/{basicInformationId}/{delivery}',[CoordinatorController::class, 'viewTotalCaInComponentCourse'])->name('coordinator.viewTotalCaInComponentCourse');
        Route::get('/coordinator/viewTotalCaInComponentCourseAndFinalExam/{statusId}/{courseIdValue}/{basicInformationId}/{delivery}',[CoordinatorController::class, 'viewTotalCaInComponentCourseAndFinalExam'])->name('coordinator.viewTotalCaInComponentCourseAndFinalExam');
        
        Route::get('/coordinator/viewCa/{statusId}/{courseIdValue}/{basicInformationId}/{delivery}',[CoordinatorController::class, 'viewAllCaInCourse'])->name('coordinator.viewAllCaInCourse');
        Route::get('/coordinator/viewExamCaInCourse/{courseIdValue}/{basicInformationId}/{delivery}',[CoordinatorController::class, 'viewAllExamInCourse'])->name('coordinator.viewExamCaInCourse');
        Route::get('/coordinator/viewExamCaInCourseFinalExamAndCa/{courseIdValue}/{basicInformationId}/{delivery}',[CoordinatorController::class, 'viewExamCaInCourseFinalExamAndCa'])->name('coordinator.viewExamCaInCourseFinalExamAndCa');

    });
    
    Route::middleware('can:Dean')->group(function () { 
                
    });

    Route::middleware('can:Registrar')->group(function () {
        Route::get('/admin/viewDeans',[AdministratorController::class, 'viewDeans'])->name('admin.viewDeans');
        Route::post('/admin/viewsCourse',[AdministratorController::class, 'viewCourse'])->name('admin.viewCourse');
    });

    Route::middleware([ 'can:ViewCoordinatorsCourses'])->group(function () { //deans, coordinators, registrar permissions included
        
        Route::get('/admin/viewCoordinators',[AdministratorController::class, 'viewCoordinators'])->name('admin.viewCoordinators');

        Route::get('/coordinator/viewCoordinatorsUnderDean/{schoolId}',[AdministratorController::class, 'viewCoordinatorsUnderDean'])->name('admin.viewCoordinatorsUnderDean');
        Route::get('/editCourseAssessmentDescription/{courseAssessmentId}/{statusId}',[AdministratorController::class, 'editCourseAssessmentDescription'])->name('editCourseAssessmentDescription');
        Route::post('/updateCourseAssessmentDescription/{courseAssessmentId}',[AdministratorController::class, 'updateCourseAssessmentDescription'])->name('updateCourseAssessmentDescription');

        

        Route::get('/admin/viewCoordinatorsCourses/{basicInformationId}',[AdministratorController::class, 'viewCoordinatorsCourses'])->name('admin.viewCoordinatorsCourses');
        Route::get('/admin/viewCoordinatorsCoursesWithComponents/{courseId}/{basicInformationId}/{delivery}/{studyId}',[AdministratorController::class, 'viewCoordinatorsCoursesWithComponents'])->name('admin.viewCoordinatorsCoursesWithComponents');
    });  
});

require __DIR__.'/auth.php';

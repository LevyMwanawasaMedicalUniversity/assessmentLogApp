<?php

use App\Http\Controllers\AdministratorController;
use App\Http\Controllers\CaAssementTypesController;
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
    Route::get('/dashboard', [PagesController::class, 'dashboard'])->name('dashboard');
    

    
    Route::middleware('can:Administrator')->group(function () {
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
        Route::post('/admin/importCoordinators',[AdministratorController::class, 'importCoordinators'])->name('admin.importCoordinators');
        Route::post('/admin/importDeans',[AdministratorController::class, 'importDeans'])->name('admin.importDeans');
        Route::post('/admin/refreshCAs',[AdministratorController::class, 'refreshCAs'])->name('admin.refreshCAs');
        Route::get('/admin/auditTrails',[AdministratorController::class, 'auditTrails'])->name('admin.auditTrails');
        
    });
    
    
    Route::middleware('can:Coordinator')->group(function () {
        Route::get('/upload', [PagesController::class, 'upload'])->name('pages.upload');
        Route::get('/uploadCourseWithComponents/{courseId}/{basicInformationId}/{delivery}/{studyId}', [PagesController::class, 'uploadCourseWithComponents'])->name('pages.uploadCourseWithComponents');
        Route::get('/coordinator/uploadCa/{statusId}/{courseIdValue}/{basicInformationId}',[CoordinatorController::class, 'uploadCa'])->name('coordinator.uploadCa');
        
                    
        Route::POST('/coordinator/importCAFromExcelSheet',[CoordinatorController::class, 'importCAFromExcelSheet'])->name('coordinator.importCAFromExcelSheet');        
        
    });
    Route::middleware('can:ViewTheContionousAssessment')->group(function () { //deans, & registrar permissions included
        Route::get('/coordinator/editCaInCourse/{courseAssessmenId}/{courseId}/{basicInformationId}',[CoordinatorController::class, 'editCaInCourse'])->name('coordinator.editCaInCourse');
        Route::POST('/coordinator/updateCAFromExcelSheet',[CoordinatorController::class, 'updateCAFromExcelSheet'])->name('coordinator.updateCAFromExcelSheet'); 

        Route::delete('/coordinator/deleteCaInCourse/{courseAssessmenId}/{courseId}',[CoordinatorController::class, 'deleteCaInCourse'])->name('coordinator.deleteCaInCourse');

        Route::get('/coordinator/viewSpecificCaInCourse/{statusId}/{courseIdValue}/{assessmentNumber}',[CoordinatorController::class, 'viewSpecificCaInCourse'])->name('coordinator.viewSpecificCaInCourse');
        Route::get('/coordinator/courseCASetings/{courseIdValue}/{basicInformationId}/{delivery}',[CoordinatorController::class, 'courseCASettings'])->name('coordinator.courseCASettings');
        Route::get('/coordinator/viewCourseWithComponents/{courseIdValue}/{basicInformationId}/{delivery}',[CoordinatorController::class, 'viewCourseWithComponents'])->name('coordinator.viewCourseWithComponents');
        
        Route::post('/coordinator/updateCourseWithComponents/{courseIdValue}',[CoordinatorController::class, 'updateCourseWithComponents'])->name('coordinator.updateCourseWithComponents');
        Route::POST('/coordinator/updateCourseCASetings/{courseIdValue}',[CoordinatorController::class, 'updateCourseCASetings'])->name('coordinator.updateCourseCASetings');
        
        Route::get('viewOnlyProgrammesWithCa',[CoordinatorController::class, 'viewOnlyProgrammesWithCa'])->name('coordinator.viewOnlyProgrammesWithCa');
        Route::get('viewOnlyProgrammesWithCaForCoordinator/{coordinator}',[CoordinatorController::class, 'viewOnlyProgrammesWithCaForCoordinator'])->name('coordinator.viewOnlyProgrammesWithCaForCoordinator');
        Route::get('showCaWithin/{courseId}',[CoordinatorController::class, 'showCaWithin'])->name('coordinator.showCaWithin');
        Route::get('/coordinator/viewTotalCaInCourse/{statusId}/{courseIdValue}/{basicInformationId}/{delivery}',[CoordinatorController::class, 'viewTotalCaInCourse'])->name('coordinator.viewTotalCaInCourse');
        Route::get('/coordinator/viewCa/{statusId}/{courseIdValue}/{basicInformationId}/{delivery}',[CoordinatorController::class, 'viewAllCaInCourse'])->name('coordinator.viewAllCaInCourse');
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

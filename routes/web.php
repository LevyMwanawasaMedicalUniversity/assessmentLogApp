<?php

use App\Http\Controllers\AdministratorController;
use App\Http\Controllers\CaAssementTypesController;
use App\Http\Controllers\CoordinatorController;
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
    // Route::middleware(['auth','2fa'])->group(function () {
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    // Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/dashboard', [PagesController::class, 'dashboard'])->name('dashboard');

    
    Route::middleware('can:Administrator')->group(function () {
        Route::resource('users', UserController::class);
        Route::resource('roles', RolesController::class);
        Route::resource('permissions', PermissionsController::class);
        Route::resource('caAssessmentTypes', CaAssementTypesController::class);
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
    });
    
    
    Route::middleware('can:Coordinator')->group(function () {
        Route::get('/upload', [PagesController::class, 'upload'])->name('pages.upload');
        Route::get('/coordinator/uploadCa/{statusId}/{courseIdValue}',[CoordinatorController::class, 'uploadCa'])->name('coordinator.uploadCa');
        
                    
        Route::POST('/coordinator/importCAFromExcelSheet',[CoordinatorController::class, 'importCAFromExcelSheet'])->name('coordinator.importCAFromExcelSheet');        
        
    });
    Route::middleware('can:ViewTheContionousAssessment')->group(function () { //deans, & registrar permissions included
        Route::get('/coordinator/viewSpecificCaInCourse/{statusId}/{courseIdValue}',[CoordinatorController::class, 'viewSpecificCaInCourse'])->name('coordinator.viewSpecificCaInCourse');
        Route::get('/coordinator/courseCASetings/{courseIdValue}',[CoordinatorController::class, 'courseCASettings'])->name('coordinator.courseCASettings');
        Route::get('/coordinator/viewTotalCaInCourse/{statusId}/{courseIdValue}',[CoordinatorController::class, 'viewTotalCaInCourse'])->name('coordinator.viewTotalCaInCourse');
        Route::get('/coordinator/viewCa/{statusId}/{courseIdValue}',[CoordinatorController::class, 'viewAllCaInCourse'])->name('coordinator.viewAllCaInCourse');
    });
    Route::middleware('can:Dean')->group(function () {
        Route::get('/coordinator/editCaInCourse/{courseAssessmenId}/{courseId}',[CoordinatorController::class, 'editCaInCourse'])->name('coordinator.editCaInCourse');
        Route::delete('/coordinator/deleteCaInCourse/{courseAssessmenId}/{courseId}',[CoordinatorController::class, 'deleteCaInCourse'])->name('coordinator.deleteCaInCourse');
        Route::POST('/coordinator/updateCAFromExcelSheet',[CoordinatorController::class, 'updateCAFromExcelSheet'])->name('coordinator.updateCAFromExcelSheet');
    });

    Route::middleware('can:Registrar')->group(function () {
        Route::get('/admin/viewDeans',[AdministratorController::class, 'viewDeans'])->name('admin.viewDeans');
        Route::post('/admin/viewsCourse',[AdministratorController::class, 'viewCourse'])->name('admin.viewCourse');
    });

    Route::middleware([ 'can:ViewCoordinatorsCourses'])->group(function () { //deans, coordinators, registrar permissions included
        Route::get('/admin/viewCoordinators',[AdministratorController::class, 'viewCoordinators'])->name('admin.viewCoordinators');
        Route::get('/coordinator/viewCoordinatorsUnderDean/{schoolId}',[AdministratorController::class, 'viewCoordinatorsUnderDean'])->name('admin.viewCoordinatorsUnderDean');

        Route::get('/admin/viewCoordinatorsCourses/{basicInformationId}',[AdministratorController::class, 'viewCoordinatorsCourses'])->name('admin.viewCoordinatorsCourses');
    });  
});

require __DIR__.'/auth.php';

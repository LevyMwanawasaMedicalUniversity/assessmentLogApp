<?php

use App\Http\Controllers\AdministratorController;
use App\Http\Controllers\CoordinatorController;
use App\Http\Controllers\PagesController;
use App\Http\Controllers\PermissionsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');
Auth::routes(['register' => false]);
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('user', UserController::class);
    Route::resource('roles', RolesController::class);
    Route::resource('permissions', PermissionsController::class);

    Route::get('', [UserController::class, 'index'])->name('users.index');
    Route::get('/user/searchForUser', 'UserController@searchForUser')->name('users.searchForUser');
    Route::get('/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/store', [UserController::class, 'store'])->name('users.store');
    Route::get('/{user}/show', 'UserController@show')->name('users.show');
    Route::get('/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::post('/{user}/resetUserPassword', [UserController::class, 'resetUserPassword'])->name('users.resetUserPassword');
    Route::patch('/{user}/update', [UserController::class, 'update'])->name('users.update');
    Route::post('/{user}/delete', 'UserController@destroy')->name('users.destroy');
    Route::post('/{user}/resetPassword', 'UserController@resetPassword')->name('admin.resetPassword');

    Route::get('/upload', [PagesController::class, 'upload'])->name('pages.upload');
    Route::get('/admin/index',[AdministratorController::class, 'index'])->name('admin.index');
    Route::post('/admin/importCoordinators',[AdministratorController::class, 'importCoordinators'])->name('admin.importCoordinators');
    Route::post('/admin/importDeans',[AdministratorController::class, 'importDeans'])->name('admin.importDeans');    
    Route::get('/admin/viewCoordinators',[AdministratorController::class, 'viewCoordinators'])->name('admin.viewCoordinators');
    Route::get('/admin/viewDeans',[AdministratorController::class, 'viewDeans'])->name('admin.viewDeans');
    Route::get('/coordinator/viewCoordinatorsUnderDean/{schoolId}',[AdministratorController::class, 'viewCoordinatorsUnderDean'])->name('admin.viewCoordinatorsUnderDean');
    
    Route::get('/admin/viewCoordinatorsCourses/{basicInformationId}',[AdministratorController::class, 'viewCoordinatorsCourses'])->name('admin.viewCoordinatorsCourses');
    Route::post('/admin/viewsCourse',[AdministratorController::class, 'viewCourse'])->name('admin.viewCourse');
    Route::get('/coordinator/uploadCa/{statusId}/{courseIdValue}',[CoordinatorController::class, 'uploadCa'])->name('coordinator.uploadCa');
    Route::get('/coordinator/editCaInCourse/{courseAssessmenId}/{courseId}',[CoordinatorController::class, 'editCaInCourse'])->name('coordinator.editCaInCourse');
    Route::get('/coordinator/viewCa/{statusId}/{courseIdValue}',[CoordinatorController::class, 'viewAllCaInCourse'])->name('coordinator.viewAllCaInCourse');
    Route::get('/coordinator/viewSpecificCaInCourse/{statusId}/{courseIdValue}',[CoordinatorController::class, 'viewSpecificCaInCourse'])->name('coordinator.viewSpecificCaInCourse');
    Route::get('/coordinator/viewTotalCaInCourse/{statusId}/{courseIdValue}',[CoordinatorController::class, 'viewTotalCaInCourse'])->name('coordinator.viewTotalCaInCourse');
    Route::delete('/coordinator/deleteCaInCourse/{courseAssessmenId}/{courseId}',[CoordinatorController::class, 'deleteCaInCourse'])->name('coordinator.deleteCaInCourse');
    
    Route::POST('/coordinator/importCAFromExcelSheet',[CoordinatorController::class, 'importCAFromExcelSheet'])->name('coordinator.importCAFromExcelSheet');
    Route::POST('/coordinator/updateCAFromExcelSheet',[CoordinatorController::class, 'updateCAFromExcelSheet'])->name('coordinator.updateCAFromExcelSheet');


    
});

require __DIR__.'/auth.php';

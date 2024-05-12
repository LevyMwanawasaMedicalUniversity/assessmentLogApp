<?php

use App\Http\Controllers\AdministratorController;
use App\Http\Controllers\CoordinatorController;
use App\Http\Controllers\PagesController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/upload', [PagesController::class, 'upload'])->name('pages.upload');
    Route::get('/admin/index',[AdministratorController::class, 'index'])->name('admin.index');
    Route::get('/admin/importCoordinators',[AdministratorController::class, 'importCoordinators'])->name('admin.importCoordinators');
    Route::get('/admin/viewCoordinators',[AdministratorController::class, 'viewCoordinators'])->name('admin.viewCoordinators');
    
    Route::get('/admin/viewCoordinatorsCourses/{basicInformationId}',[AdministratorController::class, 'viewCoordinatorsCourses'])->name('admin.viewCoordinatorsCourses');
    Route::post('/admin/viewsCourse',[AdministratorController::class, 'viewCourse'])->name('admin.viewCourse');
    Route::get('/coordinator/uploadCa/{statusId}/{courseIdValue}',[CoordinatorController::class, 'uploadCa'])->name('coordinator.uploadCa');
    Route::get('/coordinator/viewCa/{statusId}/{courseIdValue}',[CoordinatorController::class, 'viewAllCaInCourse'])->name('coordinator.viewAllCaInCourse');
    Route::get('/coordinator/viewSpecificCaInCourse/{statusId}/{courseIdValue}',[CoordinatorController::class, 'viewSpecificCaInCourse'])->name('coordinator.viewSpecificCaInCourse');
    Route::get('/coordinator/viewTotalCaInCourse/{statusId}/{courseIdValue}',[CoordinatorController::class, 'viewTotalCaInCourse'])->name('coordinator.viewTotalCaInCourse');
    Route::POST('/coordinator/importCAFromExcelSheet',[CoordinatorController::class, 'importCAFromExcelSheet'])->name('coordinator.importCAFromExcelSheet');


    
});

require __DIR__.'/auth.php';

<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StudentFormController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\DocumentTypeController;
use App\Http\Controllers\Admin\SchoolRequirementController;
use App\Http\Controllers\StudentFileController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Admin\SchoolController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\StudentApplicationCommentController;
use App\Http\Controllers\StudentZipController;
use App\Http\Controllers\Student\StudentDashboardController;
use App\Http\Controllers\School\SchoolDashboardController;

use App\Livewire\Admin\PreSchoolDashboard;
use App\Livewire\Admin\AgentPage;
use App\Livewire\Agent\AgentDashboard;


use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

use App\Http\Controllers\Export\StudentExportController;

Route::middleware(['auth'])->group(function () {
    Route::post('/students/export-selected', [StudentExportController::class, 'exportSelected'])
        ->name('students.export.selected');
});

//admin manage users routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    // List users
    Route::get('/manage-users', [UserController::class, 'index'])
        ->name('manage-users.index');

    // Create form
    Route::get('/manage-users/create', [UserController::class, 'create'])
        ->name('manage-users.create');

    // Store user
    Route::post('/manage-users', [UserController::class, 'store'])
        ->name('manage-users.store');

    // Edit form
    Route::get('/manage-users/{user}/edit', [UserController::class, 'edit'])
        ->name('manage-users.edit');

    // Update user
    Route::put('/manage-users/{user}', [UserController::class, 'update'])
        ->name('manage-users.update');

    // Delete user
    Route::delete('/manage-users/{user}', [UserController::class, 'destroy'])
        ->name('manage-users.destroy');
});



//profile
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

//admin-schoolrequirementcontroller
Route::middleware(['auth','role:admin'])->group(function () {
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/school-requirements', [SchoolRequirementController::class, 'index'])
            ->name('school-requirements.index');

        Route::post('/school-requirements/save', [SchoolRequirementController::class, 'save'])
            ->name('school-requirements.save');

        Route::post('/document-types', [DocumentTypeController::class, 'store'])
            ->name('document-types.store');

        Route::put('/document-types/{documentType}', [DocumentTypeController::class, 'update'])
            ->name('document-types.update');
    });
});

// //student form controller for show create and update destroy
Route::middleware(['auth', 'role:admin,agent,student'])->group(function () {

    // Show create form
    Route::get('/student-form/create', [StudentFormController::class, 'create'])
        ->name('student.create');

    // Store new student
    Route::post('/student-form/save', [StudentFormController::class, 'store'])
        ->name('student.store');

    // Edit form
    Route::get('/student-form/{student}/edit', [StudentFormController::class, 'edit'])
        ->name('student.edit');

    // Update student
    Route::put('/student-form/{student}', [StudentFormController::class, 'update'])
        ->name('student.update');

    // Delete student
    Route::delete('/student-form/{student}', [StudentFormController::class, 'destroy'])
        ->name('student.destroy');

});


// Student File controller
Route::middleware(['auth'])->group(function () {
    Route::get('/student/{student}/file/{school}', [StudentFileController::class, 'show'])
        ->name('student.file.show');

    Route::post('/student/{student}/file/{school}/upload', [StudentFileController::class, 'upload'])
        ->name('student.file.upload');

    Route::post('/student/{student}/file/{school}/verify/{document}', [StudentFileController::class, 'verify'])
        ->name('student.file.verify');

    Route::post('/student/{student}/file/{school}/chat/{document}', [StudentFileController::class, 'sendChat'])
        ->name('student.file.chat');
});

//notification
Route::middleware(['auth'])->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index'])
        ->name('notifications.index');

    Route::get('/notifications/{notification}/open', [NotificationController::class, 'open'])
        ->name('notifications.open');

    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markRead'])
        ->name('notifications.read');

    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])
        ->name('notifications.readAll');
});

//admin dashborad get dashboard info
Route::middleware(['auth','role:admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])
        ->name('admin.dashboard');
});

Route::middleware(['auth'])->get('/dashboard', function () {
    if (auth()->user()->role === 'admin') {
        return redirect()->route('admin.dashboard');
    }

    if (auth()->user()->role === 'agent') {
        return redirect()->route('agent.dashboard');
    }

    if (auth()->user()->role === 'student') {
        return redirect()->route('student.dashboard');
    }

    if (auth()->user()->role === 'school') {
        return redirect()->route('school.dashboard');
    }

    abort(403);
})->name('dashboard');


// admin agent page
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/agents/{agent}', AgentPage::class)
        ->name('admin.agents.show');

    Route::put('/admin/agents/{agent}', [AgentController::class, 'update'])
        ->name('admin.agents.update');

    Route::delete('/admin/agents/{agent}', [AgentController::class, 'destroy'])
        ->name('admin.agents.destroy');

    Route::post('/admin/agents/{agent}/students/{student}/assign-school', [AgentController::class, 'assignStudentToSchool'])
        ->name('agent.assign-student-school');

    Route::delete('/admin/agents/{agent}/students/{student}/applications/{targetApplication}', [AgentController::class, 'removeStudentFromSchool'])
        ->name('agent.remove-student-school');

    Route::post('/admin/agents/{agent}/applications/{application}/status', [AgentController::class, 'updateStatus'])
        ->name('agent.applications.status');
});

//admin school page
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/school/{school}', [SchoolController::class, 'show'])
        ->name('admin.school.show');
    Route::post('/admin/school/{school}/applications/{application}/assign-school', [SchoolController::class, 'assignStudentToSchool'])
    ->name('admin.school.assign-student-school');
    Route::delete('/admin/school/{school}/applications/{application}/remove-school/{targetApplication}', [SchoolController::class, 'removeStudentFromSchool'])
        ->name('admin.school.remove-student-school');
    Route::post('/admin/school/applications/{application}/status', [SchoolController::class, 'updateStatus'])
    ->name('admin.school.applications.status');
    
    //admin preschool
    Route::middleware(['auth', 'role:admin'])->group(function () {
        Route::get('/admin/pre-school/{school}', PreSchoolDashboard::class)
            ->name('admin.preschool.show');
    });
});

// //admin student page
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/student-page/{user}', [StudentController::class, 'show'])
        ->name('admin.student.show');
});        
//student zip file
Route::middleware(['auth'])->group(function () {
    Route::get('/student/{student}/zip', [StudentZipController::class, 'download'])
        ->name('student.zip');
});

//agent dashboard controller
// Route::middleware(['auth', 'role:agent'])->group(function () {
//     Route::get('/agent/dashboard', [AgentDashboardController::class, 'index'])
//         ->name('agent.dashboard');

//     Route::put('/agent/profile', [AgentDashboardController::class, 'updateProfile'])
//         ->name('agent.profile.update');
// });

Route::middleware(['auth', 'role:agent'])->group(function () {
    Route::get('/agent/dashboard', AgentDashboard::class)->name('agent.dashboard');
});

// student dashboard
Route::middleware(['auth'])->group(function () {
    Route::get('/student-panel/dashboard', [StudentDashboardController::class, 'index'])
        ->name('student.dashboard');
});


//school dashboard
Route::middleware(['auth', 'role:school'])->group(function () {
    Route::get('/school/dashboard', [SchoolDashboardController::class, 'index'])
        ->name('school.dashboard');

    Route::post('/school/applications/{application}/status', [SchoolDashboardController::class, 'updateStatus'])
        ->name('school.applications.status');
});

Route::middleware(['auth', 'role:admin,school'])->group(function () {
    Route::post('/student-applications/{application}/comment', [StudentApplicationCommentController::class, 'store'])
        ->name('student.applications.comment');
});

require __DIR__.'/auth.php';
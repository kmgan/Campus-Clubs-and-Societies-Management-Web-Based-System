<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Website\HomeController;
use App\Http\Controllers\WebPlatform\ClubMemberController;
use App\Http\Controllers\WebPlatform\DashboardController;
use App\Http\Controllers\WebPlatform\EventController;
use App\Http\Controllers\WebPlatform\UserController;
use App\Http\Controllers\WebPlatform\Auth\LoginController;
use App\Http\Controllers\WebPlatform\ClubController;
use Illuminate\Support\Facades\Auth;

Route::get('/', [HomeController::class, 'index'])->name('home');

// Authentication Routes
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

// Routes requiring authentication
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'showDashboardPage'])->name('dashboard.page');

    Route::get('/club/data', [ClubController::class, 'getClub'])->name('clubs.data');

    Route::get('/event', [EventController::class, 'showEventPage'])->name('event.page');

    Route::get('/user', [UserController::class, 'showUserPage'])->name('user.page');
    Route::get('/user/data', [UserController::class, 'getUsersData'])->name('users.data');
    Route::delete('/user/{id}', [UserController::class, 'deleteUser'])->name('users.delete');
    Route::get('/user/{id}', [UserController::class, 'getUser']);
    Route::put('/user/{id}', [UserController::class, 'updateUser']);
    Route::post('/user', [UserController::class, 'addUser'])->name('users.add');
    Route::get('/role/data', [UserController::class, 'getRole'])->name('roles.data');
    
    Route::get('/member', [ClubMemberController::class, 'showMemberPage'])->name('club.page')->middleware('role:club_manager');
    Route::get('/club-members/data', [ClubMemberController::class, 'getClubMembersData'])->name('clubMembers.data');
    Route::delete('/club-members/{id}', [ClubMemberController::class, 'deleteMember'])->name('clubMembers.delete');
    Route::get('/club-members/{id}', [ClubMemberController::class, 'getMember']);
    Route::put('/club-members/{id}', [ClubMemberController::class, 'updateMember']);
    Route::post('/club-members', [ClubMemberController::class, 'addMember'])->name('clubMembers.add');
});

// Public Home Route
Route::get('/home', [HomeController::class, 'index'])->name('home');

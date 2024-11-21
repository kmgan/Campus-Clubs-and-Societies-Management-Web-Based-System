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
Route::middleware(['auth'])->prefix('iclub')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'showDashboardPage'])->name('iclub.dashboard.page');

    Route::get('/club/data', [ClubController::class, 'getClub'])->name('iclub.clubs.data');

    Route::get('/event', [EventController::class, 'showEventPage'])->name('iclub.event.page');

    Route::get('/user', [UserController::class, 'showUserPage'])->name('iclub.user.page')->middleware('role:admin');
    Route::get('/user/data', [UserController::class, 'getUsersData'])->name('iclub.users.data');
    Route::delete('/user/{id}', [UserController::class, 'deleteUser'])->name('iclub.users.delete');
    Route::get('/user/{id}', [UserController::class, 'getUser'])->name('iclub.user.get');
    Route::put('/user/{id}', [UserController::class, 'updateUser'])->name('iclub.user.update');
    Route::post('/user', [UserController::class, 'addUser'])->name('iclub.users.add');
    Route::get('/role/data', [UserController::class, 'getRole'])->name('iclub.roles.data');

    Route::get('/member', [ClubMemberController::class, 'showMemberPage'])->name('iclub.club.page')->middleware('role:club_manager');
    Route::get('/club-members/data', [ClubMemberController::class, 'getClubMembersData'])->name('iclub.clubMembers.data');
    Route::delete('/club-members/{id}', [ClubMemberController::class, 'deleteMember'])->name('iclub.clubMembers.delete');
    Route::get('/club-members/{id}', [ClubMemberController::class, 'getMember'])->name('iclub.clubMember.get');
    Route::put('/club-members/{id}', [ClubMemberController::class, 'updateMember'])->name('iclub.clubMember.update');
    Route::post('/club-members', [ClubMemberController::class, 'addMember'])->name('iclub.clubMembers.add');
});

// Public Home Route
Route::get('/home', [HomeController::class, 'index'])->name('home');

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Website\WebsiteController;
use App\Http\Controllers\Website\WebsiteEventController;
use App\Http\Controllers\WebPlatform\ClubMemberController;
use App\Http\Controllers\WebPlatform\DashboardController;
use App\Http\Controllers\WebPlatform\EventController;
use App\Http\Controllers\WebPlatform\UserController;
use App\Http\Controllers\WebPlatform\Auth\LoginController;
use App\Http\Controllers\WebPlatform\ClubController;
use Illuminate\Support\Facades\Auth;

Route::get('/', [WebsiteController::class, 'index'])->name('home');

// Authentication Routes
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

// Routes requiring authentication
Route::middleware(['auth'])->prefix('iclub')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'showDashboardPage'])->name('iclub.dashboard.page');

    Route::get('/club', [ClubController::class, 'showClubPage'])->name('iclub.club.page');
    Route::get('/club/data', [ClubController::class, 'getClub'])->name('iclub.clubs.data');

    Route::get('/event', [EventController::class, 'showEventPage'])->name('iclub.event.page');
    Route::post('/event/register', [EventController::class, 'register'])->name('iclub.event.register');
    Route::delete('/event/unregister', [EventController::class, 'unregister'])->name('iclub.event.unregister');
    Route::post('/event/create', [EventController::class, 'createEvent'])->name('iclub.event.create');
    Route::get('/event/{id}', [EventController::class, 'getEvent'])->name('iclub.event.get');
    Route::put('/event/{id}', [EventController::class, 'updateEvent'])->name('iclub.event.update');
    Route::delete('/event/{id}/cancel', [EventController::class, 'cancelEvent'])->name('iclub.event.cancel');
    Route::get('/eventParticipant/data', [EventController::class, 'getEventParticipant'])->name('iclub.eventparticipants.data');
    Route::post('/eventParticipant/update', [EventController::class, 'updateEventParticipant'])
        ->name('iclub.eventparticipants.update');

    Route::get('/user', [UserController::class, 'showUserPage'])->name('iclub.user.page')->middleware('role:admin');
    Route::get('/user/data', [UserController::class, 'getUsersData'])->name('iclub.users.data');
    Route::delete('/user/{id}', [UserController::class, 'deleteUser'])->name('iclub.users.delete');
    Route::get('/user/{id}', [UserController::class, 'getUser'])->name('iclub.users.get');
    Route::put('/user/{id}', [UserController::class, 'updateUser'])->name('iclub.users.update');
    Route::post('/user', [UserController::class, 'addUser'])->name('iclub.users.add');
    Route::get('/role/data', [UserController::class, 'getRole'])->name('iclub.roles.data');

    Route::get('/clubMember', [ClubMemberController::class, 'showMemberPage'])->name('iclub.clubMember.page')->middleware('role:club_manager');
    Route::get('/clubMember/data', [ClubMemberController::class, 'getClubMembersData'])->name('iclub.clubMembers.data');
    Route::delete('/clubMember/{id}', [ClubMemberController::class, 'deleteMember'])->name('iclub.clubMembers.delete');
    Route::get('/clubMember/{id}', [ClubMemberController::class, 'getMember'])->name('iclub.clubMembers.get');
    Route::put('/clubMember/{id}', [ClubMemberController::class, 'updateMember'])->name('iclub.clubMembers.update');
    Route::post('/clubMember', [ClubMemberController::class, 'addMember'])->name('iclub.clubMembers.add');
});

// Public Home Route
Route::get('/home', [WebsiteController::class, 'index'])->name('home');
Route::get('/event/{id}', [WebsiteController::class, 'showEventDetails'])->name('event.details');
Route::get('/club/{id}', [WebsiteController::class, 'showClubDetails'])->name('club.details');

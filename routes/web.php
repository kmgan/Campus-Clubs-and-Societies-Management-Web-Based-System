<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Website\WebsiteController;
use App\Http\Controllers\Webplatform\EditWebContentController;
use App\Http\Controllers\WebPlatform\ClubMemberController;
use App\Http\Controllers\WebPlatform\EventController;
use App\Http\Controllers\WebPlatform\UserController;
use App\Http\Controllers\WebPlatform\Auth\LoginController;
use App\Http\Controllers\WebPlatform\Auth\ConfirmPasswordController;
use App\Http\Controllers\WebPlatform\Auth\ForgotPasswordController;
use App\Http\Controllers\WebPlatform\Auth\ResetPasswordController;
use App\Http\Controllers\WebPlatform\ClubController;
use Illuminate\Support\Facades\Auth;

// Public Home Route
Route::get('/', [WebsiteController::class, 'index'])->name('home');
Route::get('/home', [WebsiteController::class, 'index'])->name('home');
Route::get('/event/{id}', [WebsiteController::class, 'showEventDetails'])->name('event.details');
Route::get('/club/{id}', [WebsiteController::class, 'showClubDetails'])->name('club.details');

// Authentication Routes
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

// Forgot Password Routes
Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');

// Confirm Password Routes
Route::get('password/confirm', [ConfirmPasswordController::class, 'showConfirmForm'])->name('password.confirm');
Route::post('password/confirm', [ConfirmPasswordController::class, 'confirm']);

// Routes requiring authentication
Route::middleware(['auth'])->prefix('iclub')->group(function () {
    Route::post('/webContent/{clubId}/save', [EditWebContentController::class, 'saveContent'])->name('iclub.webContent.save');
    Route::get('/webContent', [EditWebContentController::class, 'showEditPage'])->name('iclub.webContent.page')->middleware('role:club_manager');

    Route::get('/club', [ClubController::class, 'showClubPage'])->name('iclub.club.page');
    Route::get('/club/data', [ClubController::class, 'getClub'])->name('iclub.clubs.data');
    Route::post('/club/register', [ClubController::class, 'register'])->name('iclub.club.register');
    Route::post('/club/create', [ClubController::class, 'createClub'])->name('iclub.club.create')->middleware('role:admin');
    Route::delete('/club/{id}', [ClubController::class, 'deleteClub'])->name('iclub.club.delete')->middleware('role:admin');

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
    Route::put('/pendingParticipant/{id}/approve', [EventController::class, 'approveParticipant'])->name('iclub.pendingParticipants.approve')
        ->middleware('role:club_manager');
    Route::delete('/pendingParticipant/{id}/reject', [EventController::class, 'rejectParticipant'])->name('iclub.pendingParticipants.reject')
        ->middleware('role:club_manager');
    Route::get('/{eventId}/pendingParticipant', [EventController::class, 'getPendingParticipant'])->name('iclub.pendingParticipants.data')
        ->middleware('role:club_manager');

    Route::get('/user', [UserController::class, 'showUserPage'])->name('iclub.user.page')->middleware('role:admin');
    Route::get('/user/data', [UserController::class, 'getUsersData'])->name('iclub.users.data');
    Route::delete('/user/{id}', [UserController::class, 'deleteUser'])->name('iclub.users.delete')->middleware('role:admin');
    Route::get('/user/{id}', [UserController::class, 'getUser'])->name('iclub.users.get');
    Route::put('/user/{id}', [UserController::class, 'updateUser'])->name('iclub.users.update')->middleware('role:admin');
    Route::post('/user', [UserController::class, 'addUser'])->name('iclub.users.add')->middleware('role:admin');
    Route::get('/role/data', [UserController::class, 'getRole'])->name('iclub.roles.data');

    Route::get('/clubMember', [ClubMemberController::class, 'showMemberPage'])->name('iclub.clubMember.page')->middleware('role:club_manager');
    Route::get('/clubMember/data', [ClubMemberController::class, 'getClubMembersData'])->name('iclub.clubMembers.data');
    Route::delete('/clubMember/{id}', [ClubMemberController::class, 'deleteMember'])->name('iclub.clubMembers.delete')->middleware('role:club_manager');
    Route::put('/clubMember/{id}/role', [ClubMemberController::class, 'updateRole'])->name('iclub.clubMembers.updateRole')->middleware('role:club_manager');
    Route::get('/pendingMember', [ClubMemberController::class, 'getPendingMember'])->name('iclub.pendingMembers.data')->middleware('role:club_manager');
    Route::put('/pendingMember/{id}/approve', [ClubMemberController::class, 'approveMember'])->name('iclub.pendingMembers.approve')->middleware('role:club_manager');
    Route::delete('/pendingMember/{id}/reject', [ClubMemberController::class, 'rejectMember'])->name('iclub.pendingMembers.reject')->middleware('role:club_manager');
    Route::post('/clubMember/updateApprovalSetting', [ClubMemberController::class, 'updateApprovalSetting'])->name('iclub.clubMembers.updateApprovalSetting')->middleware('role:club_manager');

});

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Website\HomeController;
use App\Http\Controllers\WebPlatform\ClubController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/club', [ClubController::class, 'showClubPage'])->name('club.page');

Route::get('/club-members/data', [ClubController::class, 'getClubMembersData'])->name('clubMembers.data');

Route::delete('/club-members/{id}', [ClubController::class, 'deleteMember'])->name('clubMembers.delete');

Route::get('/club-members/{id}', [ClubController::class, 'getMember']);

Route::put('/club-members/{id}', [ClubController::class, 'updateMember']); 

Route::post('/club-members', [ClubController::class, 'addMember'])->name('clubMembers.add');

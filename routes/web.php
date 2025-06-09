<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BillController;
use App\Http\Controllers\NavigationController;



Auth::routes();

Route::get('/', [App\Http\Controllers\BillController::class, 'index']);
Route::get('/home', [App\Http\Controllers\DashboardController::class, 'index'])->name('home');
Route::get('/users', [App\Http\Controllers\DashboardController::class, 'index'])->name('users');
Route::get('/get-users', [DashboardController::class, 'getUsers'])->name('get.users'); // Json response for users
Route::delete('/delete-user/{id}', [DashboardController::class, 'deleteUser'])->name('delete.user'); // Json response for users

 


 

Route::get('/navigation', [NavigationController::class, 'viewNavigation'])->name('navigation'); // navigation page
Route::post('/navigation', [NavigationController::class, 'createNavigation'])->name('navigation.post'); //create navigation
Route::delete('/navigation/{id}', [NavigationController::class, 'deleteNavigation'])->name('navigation.delete');
Route::get('/navigation/get', [NavigationController::class, 'getNavigation'])->name('navigation.get'); // Json response for navigation
Route::delete('/navigation/submenu/delete/{id}', [NavigationController::class, 'deleteSubNavigation'])->name('/navigation/submenu/delete');
Route::patch('/navigation/submenu/update-status/{id}', [NavigationController::class, 'updateSubNavigation'])->name('/navigation/submenu/update-status'); // Update status of sub-navigation

Route::post('/navigation/submenu/create', [NavigationController::class, 'createSubNavigation'])->name('navigation/submenu/create');
Route::get('get-submenu/{id}', [NavigationController::class, 'getSubNavigation'])->name('navigation.sub'); // Json response for sub-navigation
Route::patch('/change-submenu/{id}', [NavigationController::class, 'changeSubNavigation'])->name('navigation.change'); // Json response for sub-navigation
Route::get('get-nav', [NavigationController::class, 'getNavigationjson'])->name('navigation.nav'); // Json response for sub-navigation


// Get all users
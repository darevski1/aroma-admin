<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BillController;
use App\Http\Controllers\NavigationController;
use App\Http\Controllers\PublicController;



Auth::routes();

Route::get("/", function(){
    return view("welcome");
});
// Route::get("/{slug}", function($slug){
//     return $slug;
// });

// Public routes

// Route::get("/", function(){
//     return view("welcome");
// });

 Route::group(['middleware' => ['auth', 'verified']], function () {

    Route::get('/home', [App\Http\Controllers\DashboardController::class, 'index'])->name('home');
    Route::get('/users', [App\Http\Controllers\DashboardController::class, 'index'])->name('users');
    Route::get('/get-users', [DashboardController::class, 'getUsers'])->name('get.users'); // Json response for users
    Route::delete('/delete-user/{id}', [DashboardController::class, 'deleteUser'])->name('delete.user'); // Json response for users

    Route::get('/navigation', [NavigationController::class, 'viewNavigation'])->name('navigation'); // navigation page
    Route::post('/navigation', [NavigationController::class, 'createNavigation'])->name('navigation.post'); //create navigation
    Route::delete('/navigation/delete/{id}', [NavigationController::class, 'deleteNavigation'])->name('navigation.delete');
    Route::patch('/navigation/update/{id}', [NavigationController::class, 'updateLink'])->name('navigation.update');
    Route::get('/navigation/get/{id}', [NavigationController::class, 'getByid'])->name('navigation.get');

    Route::get('/navigation/get', [NavigationController::class, 'getNavigation'])->name('navigation.get'); // Json response for navigation
    Route::delete('/navigation/submenu/delete/{id}', [NavigationController::class, 'deleteSubNavigation'])->name('/navigation/submenu/delete');
    Route::patch('/navigation/submenu/update-status/{id}', [NavigationController::class, 'updateSubNavigation'])->name('/navigation/submenu/update-status'); // Update status of sub-navigation

    Route::post('/navigation/submenu/create', [NavigationController::class, 'createSubNavigation'])->name('navigation/submenu/create');
    Route::get('get-submenu/{id}', [NavigationController::class, 'getSubNavigation'])->name('navigation.sub'); // Json response for sub-navigation
    Route::patch('/change-submenu/{id}', [NavigationController::class, 'changeSubNavigation'])->name('navigation.change'); // Json response for sub-navigation
    Route::get('get-nav', [NavigationController::class, 'getNavigationjson'])->name('navigation.nav'); // Json response for sub-navigation

 
 });

Route::get('/services/cenobots', [PublicController::class, 'cennobots'])->name('cennobots');
 
 


Route::get('uslugi/mrezni-instalacii-i-odrzuvanje-na-mrezi', [PublicController::class, 'computerNetwork'])->name('mrezni-instalacii-i-odrzuvanje-na-mrezi');  
Route::get('uslugi/odrzuvanje-na-kompjuterski-sistemi', [PublicController::class, 'computerWorld'])->name('odrzuvanje-na-kompjuterski-sistemi');  
Route::get('uslugi/video-nadzor-i-kontrola-na-pristap', [PublicController::class, 'accessControl'])->name('video-nadzor-i-kontrola-na-pristap');  
Route::get('uslugi/servis-i-prodazba-na-printeri', [PublicController::class, 'printerWorld'])->name('servis-i-prodazba-na-printeri');  
Route::get('za-nas', [PublicController::class, 'about'])->name('za-nas');  